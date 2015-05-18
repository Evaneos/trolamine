<?php
namespace Trolamine\Factory;

interface SecuredClassFactory
{
    
    /**
     * The factory method
     *
     * @param  object $instance          The instance to secure
     * @param  string $alias             The alias of the service to secure
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
    public function build($instance, $alias, array $securedParameters=array());
}
