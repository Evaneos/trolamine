<?php
namespace Trolamine\Factory;

interface SecuredClassFactory {
    
    /**
     * The factory method
     * 
     * @param  object $instance          The instance to secure
     * @param  array  $securedParameters The associative array of parameters :
     *                                   array (
     *                                       ['methodNameToSecure'] => array (
     *                                           ['preAuthorize'] => array<ConfigAttribute>(...),
     *                                           ['postAuthorize'] => array<ConfigAttribute>(...)
     *                                       ),
     *                                       ...
     *                                   )
     * 
     * @return mixed  the secured class instance which extends the class passed in parameters
     */
    function build($instance, array $securedParameters=array());
    
}