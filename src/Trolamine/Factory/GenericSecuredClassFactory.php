<?php
namespace Trolamine\Factory;

use Trolamine\Core\SecurityContext;

class GenericSecuredClassFactory implements SecuredClassFactory
{

    /**
     * The security context
     *
     * @var SecurityContext
     */
    private $securityContext;

    /**
     *
     * @var string
     */
    private $cacheDir;

    /**
     * Constructor
     *
     * @param SecurityContext $securityContext
     */
    public function __construct(SecurityContext $securityContext, $cacheDir)
    {
        $this->securityContext = $securityContext;
        $this->cacheDir = realpath($cacheDir);
    }

    /**
     * (non-PHPdoc)
     * @see \Trolamine\Factory\SecuredClassFactory::build()
     */
    public function build($instance, $alias, array $securedParameters=array())
    {
        $instance = $this->getSecuredInstance($instance, $alias, $securedParameters);
        $secured = new Secured($this->securityContext, $securedParameters);
        $instance->setSecured($secured);

        return $instance;
    }

    /**
     * Gets the secured instance for the class
     *
     * @param object $instance          The unsecured instance
     * @param string $alias
     * @param array  $securedParameters
     *
     * @return the secured instance of the class
     */
    public function getSecuredInstance($instance, $alias, array $securedParameters)
    {
        $class = new \ReflectionClass($instance);
        $securedInstance = $this->getSecuredClassInstance($class, $alias, $securedParameters);
        $securedClass = new \ReflectionClass($securedInstance);

        //Copy the properties
        $properties = $class->getProperties();
        foreach ($properties as $property) {
            /* @var $property \ReflectionProperty */
            $propertyName = $property->getName();
            $property->setAccessible(true);

            $securedProperty = $securedClass->getParentClass()->getProperty($propertyName);
            $securedProperty->setAccessible(true);

            if ($property->isStatic()) {
                $propertyValue = $property->getValue();
                $securedProperty->setValue($propertyValue);
            } else {
                $propertyValue = $property->getValue($instance);
                $securedProperty->setValue($securedInstance, $propertyValue);
            }
        }

        return $securedInstance;
    }

    /**
     *
     * @param \ReflectionClass $class
     * @param array $securedParameters
     *
     * @return $object
     */
    public function getSecuredClassInstance(\ReflectionClass $class, $alias, array $securedParameters)
    {
        $namespace = $class->getNamespaceName();
        $name = $class->getName();
        $securedName = $this->getSecuredName($name, $alias);

        $fileName = $this->cacheDir.DIRECTORY_SEPARATOR.$securedName.'.php';

        if (!file_exists($fileName) || filemtime($fileName)<filemtime($class->getFileName())) {
            $code = $this->generateCode($class, $alias, $securedParameters);
            $tmpFileName = $fileName . "." . uniqid();
            if ($this->write($tmpFileName, $code)) {
                rename($tmpFileName, $fileName);
            }
        }

        include_once($fileName);

        $newReflectionClass = new \ReflectionClass($namespace.'\\'.$securedName);

        return $newReflectionClass->newInstanceArgs();
    }

    public function generateCode(\ReflectionClass $class, $alias, $securedParameters)
    {
        $namespace = $class->getNamespaceName();
        $name = $class->getName();
        $securedName = $this->getSecuredName($name, $alias);

        $skippedMethods = array(
            '__sleep' => true,
            '__clone' => true,
            '__wakeup' => true,
            '__get' => true,
            '__set' => true,
            '__isset' => true
        );
        $methodsArray = array();

        $methodNames = array();
        if (array_key_exists(Secured::ALL, $securedParameters)) {
            $methods = $class->getMethods();
            foreach ($methods as $method) {
                /* @var $method \ReflectionMethod */
                $methodName = $method->getName();
                //We don't secure constructors, magic, final or private methods
                if (
                    !$method->isConstructor() &&
                    !$method->isFinal() &&
                    !$method->isPrivate() &&
                    strpos($methodName, '__') !== 0 &&
                    !array_key_exists($methodName, $skippedMethods)
                ) {
                    $methodNames[] = $methodName;
                }
            }
        } else {
            $methodNames = array_keys($securedParameters);
        }

        foreach ($methodNames as $methodName) {
            if ($class->hasMethod($methodName)) {
                /* @var $method \ReflectionMethod */
                $method = $class->getMethod($methodName);

                if ($method->isConstructor() ||
                    isset($skippedMethods[strtolower($methodName)]) ||
                    $method->isFinal() ||
                    $method->isPrivate()
                ) {
                    // TODO Use a better exception class
                    throw new \LogicException(
                        vsprintf(
                            'Method %s::%s cannot be secured : magic, final or private methods are not allowed',
                            array(
                                $class->getName(),
                                $methodName
                            )
                        )
                    );
                }

                //Method Parameters
                $methodParameters = $method->getParameters();
                $params = $paramsSignature = array();
                foreach ($methodParameters as $parameter) {
                    /* @var $parameter \ReflectionParameter */
                    $paramName = $parameter->getName();
                    $paramString = '';

                    try {
                        $paramClass = $parameter->getClass();
                    } catch (\ReflectionException $previous) {
                        // TODO Rethrow
                    }

                    if (null !== $paramClass) {
                        $paramString .= '\\' . $paramClass->getName() . ' ';
                    } elseif ($parameter->isArray()) {
                        $paramString .= 'array ';
                    } elseif (method_exists($parameter, 'isCallable') && $parameter->isCallable()) {
                        $paramString .= 'callable ';
                    }

                    if ($parameter->isPassedByReference()) {
                        $paramString .= '&';
                    }

                    $paramString .= '$' . $paramName;

                    if ($parameter->isDefaultValueAvailable()) {
                        $paramString .= ' = ' . var_export($parameter->getDefaultValue(), true);
                    } else {
                        $paramString .= ' = null';
                    }

                    $params[$paramName] = '$' . $paramName;
                    $paramsSignature[] = $paramString;
                }

                $signature = "/**\n     * {@inheritDoc}\n     */\n    ";
                
                if ($method->isPublic()) {
                    $signature .= 'public ';
                } elseif ($method->isProtected()) {
                    $signature .= 'protected ';
                }

                if ($method->isStatic()) {
                    $signature .= 'static ';
                }

                $signature .= 'function ';

                if ($method->returnsReference()) {
                    $signature .= '&';
                }

                $signature .= $methodName . '(' .implode(', ', $paramsSignature).') {';
                
                //Code generation
                //TODO generalize secured
                $resultVar = $this->generateRandomVarName();
                $paramsVar = $this->generateRandomVarName();
                $methodContent  = "\n";
                $methodContent .= '    '.$signature."\n";
                $methodContent .= '        $'.$paramsVar.' = array('.$this->getParamsArrayAsString($params).');'."\n";
                $methodContent .= '        $this->secured->preAuthorize(\''.$methodName.'\', $'.$paramsVar.');'."\n";
                $methodContent .= '        $'.$paramsVar.' = $this->secured->preFilter(\''.$methodName.'\', $'.$paramsVar.');'."\n";
                $methodContent .= '        $'.$resultVar.' = parent::'.$methodName.'('.$this->getParamsArrayValuesAsString($paramsVar, $params).');'."\n";
                $methodContent .= '        $'.$resultVar.' = $this->secured->postFilter(\''.$methodName.'\', $'.$paramsVar.', $'.$resultVar.');'."\n";
                $methodContent .= '        $this->secured->postAuthorize(\''.$methodName.'\', $'.$paramsVar.', $'.$resultVar.');'."\n";
                $methodContent .= '        return $'.$resultVar.';'."\n";
                $methodContent .= '    }'."\n";

                $methodsArray[] = $methodContent;
            }
        }

        $classContent  = '<?php'."\n";

        //namespace
        if (!empty($namespace)) {
            $classContent .= 'namespace '.$namespace.';'."\n\n";
        }

        //class declaration
        $classContent .= 'class '.$securedName.' extends \\'.$name.' {'."\n";
        $classContent .= "\n";

        //member variables
        //TODO generalize
        $classContent .= '    private $secured;'."\n";
        $classContent .= "\n";

        //Add an empty constructor
        $classContent .= '    public function __construct() {}'."\n";
        $classContent .= "\n";

        //Secured variable setter
        //TODO generalize
        $classContent .= '    public function setSecured(\\Trolamine\\Factory\\Secured $secured) {'."\n";
        $classContent .= '        $this->secured = $secured;'."\n";
        $classContent .= '    }'."\n";

        //secured methods
        $classContent .= implode("\n", $methodsArray);
        $classContent .= "\n";

        $classContent .= '}'."\n";

        return $classContent;
    }

    public function write($fileName, $code)
    {
        return file_put_contents($fileName, $code, LOCK_EX);
    }

    public function getParamsArrayAsString($params)
    {
        $keyValueArray = array();
        foreach ($params as $key=>$value) {
            $keyValueArray[] = "'".$key."'=>".$value;
        }
        return implode(', ', $keyValueArray);
    }
    
    public function getParamsArrayValuesAsString($arrayName, $params)
    {
        $values = array();
        foreach ($params as $key=>$value) {
            $values[] = '$'.$arrayName."['".$key."']";
        }
        return implode(', ', $values);
    }

    public function getSecuredName($name, $alias)
    {
        return 'Secured'.md5($name.$alias);
    }

    public function generateRandomVarName()
    {
        $str = '';
        for ($i=0;$i<10;$i++) {
            $str .= chr(97 + mt_rand(0, 25));
        }
        return $str;
    }
}
