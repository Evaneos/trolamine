<?php
namespace Trolamine\Core\Permission;

/**
 * Describes a permission
 * 
 * @author Remi
 */
interface Permission {
    
    /**
     * Returns the bits that represents the permission.
     *
     * @return the bits that represent the permission
     */
    function getMask();
    
    /**
     * Returns the code that represents the permission.
     *
     * @return the code that represent the permission
     */
    function getCode();
    
    /**
     * Returns character bit pattern <code>String</code> representing this permission.
     * 
     * This method is only used for user interface and logging purposes. It is not used in any permission
     * calculations. Therefore, duplication of characters within the output is permitted.
     *
     * @return string
     */
    function getPattern();
    
    /**
     * Are permissions equal ?
     *
     * @param  Object $obj
     *
     * @return boolean
     */
    function equals($obj);
    
    /**
     * Does local permission contains the permission passed in parameter ? 
     * 
     * @param Object $obj
     * 
     * @return boolean
     */
    function contains($obj);
    
}
