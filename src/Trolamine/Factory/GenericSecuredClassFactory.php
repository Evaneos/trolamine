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
    public function __construct(SecurityContext $securityContext, $cacheDir) {
        $this->securityContext = $securityContext;
        $this->cacheDir = realpath($cacheDir);
    }
    
    /**
     * (non-PHPdoc)
     * @see \Trolamine\Factory\SecuredClassFactory::build()
     */
    function build($instance, $alias, array $securedParameters=array()) {
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
    function getSecuredInstance($instance, $alias, array $securedParameters) {
        $class = new \ReflectionClass($instance);
        $securedInstance = $this->getSecuredClassInstance($class, $alias, $securedParameters);
        $securedClass = new \ReflectionClass($securedInstance);

        //Copy the properties
        $properties = $class->getProperties();
        foreach($properties as $property) {
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
    function getSecuredClassInstance(\ReflectionClass $class, $alias, array $securedParameters) {
        
        $namespace = $class->getNamespaceName();
        $name = $class->getName();
        $securedName = $this->getSecuredName($name, $alias);
        
        $fileName = $this->cacheDir.DIRECTORY_SEPARATOR.$securedName.'.php';
        
        if (!file_exists($fileName) || filemtime($fileName)<filemtime($class->getFileName())) {
            $code = $this->generateCode($class, $alias, $securedParameters);
            $this->write($fileName, $code);
        }
        
        include_once($fileName);
        
        $newReflectionClass = new \ReflectionClass($namespace.'\\'.$securedName);
        
        return $newReflectionClass->newInstanceArgs();
    }
    
    function generateCode(\ReflectionClass $class, $alias, $securedParameters) {
        
        $namespace = $class->getNamespaceName();
        $name = $class->getName();
        $securedName = $this->getSecuredName($name, $alias);
        
        $fileName = $this->cacheDir.DIRECTORY_SEPARATOR.$securedName.'.php';
        
        $methodsArray = array();
        $uses = array();
        
        $methodNames = array();
        if(array_key_exists(Secured::ALL, $securedParameters)) {
            $methods = $class->getMethods();
            foreach ($methods as $method) {
                /* @var $method \ReflectionMethod */
                $methodName = $method->getName();
                if(strpos($methodName, '__') !== 0) {
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
                $filename = $method->getFileName();
                $startLine = $method->getStartLine() - 1;
                $endLine = $method->getEndLine();
                $length = $endLine - $startLine;
        
                //Method Signature
                $source = file($filename);
                $body = implode("", array_slice($source, $startLine, $length));
                $signature = trim(substr($body, 0, strpos($body, '{')+1));
        
                //Method Parameters
                $methodParameters = $method->getParameters();
                $params = array();
                foreach($methodParameters as $parameter) {
                    /* @var $parameter \ReflectionParameter */
                    $paramName = $parameter->getName();
        
                    $typeClass = $parameter->getClass();
                    if ($typeClass != null) {
                        $type = $typeClass->name;
                    
                        if ($type != null) {
                            $uses[$type] = 'use '.$type.';';
                        }
                    }
        
                    $params[$paramName] = '$'.$paramName;
                }
        
                //Code generation
                $resultVar = $this->generateRandomVarName();
                $paramsVar = $this->generateRandomVarName();
                $methodContent  = "\n";
                $methodContent .= '    '.$signature."\n";
                $methodContent .= '        $'.$paramsVar.' = array('.$this->getParamsArrayAsString($params).');'."\n";
                $methodContent .= '        $this->secured->preAuthorize(\''.$methodName.'\', $'.$paramsVar.');'."\n";
                $methodContent .= '        $'.$resultVar.' = parent::'.$methodName.'('.implode(', ', $params).');'."\n";
                $methodContent .= '        $this->secured->postAuthorize(\''.$methodName.'\', $'.$paramsVar.', $'.$resultVar.');'."\n";
                $methodContent .= '        return $'.$resultVar.';'."\n";
                $methodContent .= '    }'."\n";
        
                $methodsArray[] = $methodContent;
            }
        }
        
        $classContent  = '<?'."\n";
        
        //namespace
        $classContent .= 'namespace '.$namespace.';'."\n\n";
        
        //uses
        $classContent .= implode("\n", $uses);
        $classContent .= "\n\n";
        
        //class declaration
        $classContent .= 'class '.$securedName.' extends \\'.$name.' {'."\n";
        $classContent .= "\n";
        
        //member variables
        $classContent .= '    private $secured;'."\n";
        $classContent .= "\n";
        
        //Add an empty constructor
        $classContent .= '    public function __construct() {}'."\n";
        $classContent .= "\n";
        
        //Secured variable setter
        $classContent .= '    public function setSecured(\\Trolamine\\Factory\\Secured $secured) {'."\n";
        $classContent .= '        $this->secured = $secured;'."\n";
        $classContent .= '    }'."\n";
        
        //secured methods
        $classContent .= implode("\n", $methodsArray);
        $classContent .= "\n";
        
        $classContent .= '}'."\n";
        
        return $classContent;
    }
    
    function write($fileName, $code) {
        file_put_contents($fileName, $code);
    }
    
    function getParamsArrayAsString($params) {
        $keyValueArray = array();
        foreach ($params as $key=>$value) {
            $keyValueArray[] = "'".$key."'=>".$value;
        }
        return implode(', ', $keyValueArray);
    }
    
    function getSecuredName($name, $alias) {
        return 'Secured'.md5($name.$alias);
    }
    
    function generateRandomVarName() {
        $str = '';
        for ($i=0;$i<10;$i++) {
            $str .= chr(97 + mt_rand(0, 25));
        }
        return $str;
    }
}
