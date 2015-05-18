<?php
namespace Trolamine\Core\Permission;

/**
 * An abstract permission class
 *
 * @author Remi
 */
abstract class AbstractPermission implements Permission
{
    
    /**
     * @var int
     */
    protected $mask;
    
    /**
     * @var string
     */
    protected $code;
    
    /**
     *
     * @param int    $mask
     * @param string $code
     */
    public function __construct($mask, $code)
    {
        $this->mask = $mask;
        $this->code = $code;
    }
    
    /**
     * (non-PHPdoc)
     * @see \Trolamine\Core\Permission::getMask()
     */
    public function getMask()
    {
        return $this->mask;
    }
    
    /**
     * (non-PHPdoc)
     * @see \Trolamine\Core\Permission::getCode()
     */
    public function getCode()
    {
        return $this->code;
    }
    
    /**
     * (non-PHPdoc)
     * @see \Trolamine\Core\Permission::getPattern()
     */
    public function getPattern()
    {
        return md5($this->mask.$this->code);
    }
    
    /**
     * (non-PHPdoc)
     * @see \Trolamine\Core\Permission::equals()
     */
    public function equals($obj)
    {
        if (null === $obj) {
            return false;
        }
    
        if (!$obj instanceof Permission) {
            return false;
        }
        
        /* @var $obj Permission */
        return $this->mask === $obj->getMask();
    }

    public function __toString()
    {
        return $this->code;
    }
}
