Trolamine
=========
Security and Authentication components for [Pyrite][1].

Basics
-----------------
Trolamine is a component aimed at providing security and authentication functionalities to Pyrite.

To simplify authentication, a Pyrite Layer has been created : [`AuthenticationLayer`][2].

To provide method-level security (which can be used to secure routes too if they're defined by a method), a [DIC-IT][3] Activator has been created to enable the "security" keyword in the dependency injection configuration : [`SecurityActivator`][4].


----------

Quickstart
==========

SecurityContext
---------------
First, you have to declare the SecurityContext in your DIC-IT YML configuration file :

    classes :
        SecurityContext :
            class : \Trolamine\Core\SimpleSecurityContext
            singleton : true
            arguments : [ @AccessDecisionManager ]
            
        AccessDecisionManager :
            class : \Trolamine\Core\Access\UnanimousBased #you can switch to AffirmativeBased or ConsensusBased
            singleton : true
            arguments : [ [ @OperationDecisionVoter ] ] #you can add as many voters you want
            
        OperationDecisionVoter :
            class : \Trolamine\Core\Access\OperationDecisionVoter
            singleton : true

Authentication
--------------
Then, you need to declare the Layer :

    classes :
        AuthenticationLayer :
        class : \Trolamine\Layer\AuthenticationLayer
        arguments  : [ @SecurityContext, authentication ] #the second argument sets the name of the session var
        
If you want to manually log a user (or create a controller that retrieves the login parameters and logs the user), you'll have to declare the AuthenticationManager :

    classes :
        AuthenticationManager :
                class : \Trolamine\Core\Authentication\BaseAuthenticationManager
                singleton : true
                arguments : [ @UserDetailsService, @PasswordEncoder, @RoleManager]
        

See :
- [`UserDetailsService`][5] *(will retrieve the user corresponding to the login passed)*
- [`PasswordEncoder`][6] *(how to encode the password before checking the retrieved value)*
- [`RoleManager`][7] *(how to retrieve the roles for the authenticated user)*


After that, in your routes, you'll be able to add the Layer which will retrieve the authenticated user in session and add it to the `SecurityContext` :

    routes:
        test:
            route:
                pattern: "/test"
                methods: [ get ]
            dispatch:
                PyriteSessionFactory : { 'start' : true }
                PyriteApplicationFactory :
                    AuthenticationLayer: [ ]
                    Executor : [ MyController ]

Security
--------
Then, you can activate the security activator to be able to use the "security" keyword in dic-it config.
    
    classes :
        # Factory
        SecuredClassFactory :
            class : \Trolamine\Factory\GenericSecuredClassFactory
            arguments : [ @SecurityContext, %secured_dir ]
            
        SecurityActivator :
            class : Trolamine\Activator\SecurityActivator
            arguments : [ @SecuredClassFactory ]

When setting the container, you'll have to add the declared activator.

	$activator = new \DICIT\ActivatorFactory();
    $container = new \Evaneos\Pro\Container\DICITAdapter($config, $activator);
    [...]
    $activator->addActivator('security', $container->get('SecurityActivator'), false);

After that, you'll be able to declare the operations you want to use for your security rules :

    classes :
        MethodSecurityExpressionOperations :
            class : \Trolamine\Core\Operation\MethodSecurityExpressionRoot

You can add as much as you want, they just have to implement [`Operation`][8] (you can extend class [`AbstractOperation`][9])

Be careful, using the "security" keyword in your config will result in the generation of a proxy class which will be written in the directory specified by `%secured_dir`.

    parameters :
        root_dir : /path/to/your/application/root/
        secured_dir : /path/to/your/application/root/secured/

Example
=======

Now, you can secure your methods in your DIC-IT config :

    classes :
        TestService :
            class : \Test\Application\TestService
            arguments : [ ]
            security :
                testMethod1 :
                    preAuthorize :
                        0 : { operation : @MethodSecurityExpressionOperations, method : hasRole, args : [ ROLE_ADMIN ] }
                    postAuthorize :
                        0 : { operation : @MyOperations, method : hasRight, args : [ &returnObject ] }
                        
In this example, the method `testMethod1` in class `TestService` has been configured to check if the authenticated user has the `'ROLE_ADMIN'` role before being invoked (to check that, the `hasRole` method of class [`MethodSecurityExpressionRoot`][10] will be called).

If he hasn't, an [`AccessDeniedException`][11] will be thrown. Otherwise, the method will be invoked normally.

After invocation, it will check if the returned object of the invoked method is accessible (by calling the `hasRight` method of the class mapped by the `MyOperations` service). The `&returnObject` keyword will automatically be replaced by the return object value.

Any param passed to a security operation that begins with the `&` sign will be converted to the value of the corresponding param of the secured method.


  [1]: https://github.com/Evaneos/pyrite
  [2]: https://github.com/Evaneos/trolamine/tree/master/src/Trolamine/Layer
  [3]: https://github.com/oliviermadre/dic-it
  [4]: https://github.com/Evaneos/trolamine/tree/master/src/Trolamine/Activator
  [5]: https://github.com/Evaneos/trolamine/blob/master/src/Trolamine/Core/Authentication/UserDetailsService.php
  [6]: https://github.com/Evaneos/trolamine/blob/master/src/Trolamine/Core/Authentication/Password/PasswordEncoder.php
  [7]: https://github.com/Evaneos/trolamine/blob/master/src/Trolamine/Core/Authentication/Role/RoleManager.php
  [8]: https://github.com/Evaneos/trolamine/blob/master/src/Trolamine/Core/Operation/Operation.php
  [9]: https://github.com/Evaneos/trolamine/blob/master/src/Trolamine/Core/Operation/AbstractOperation.php
  [10]: https://github.com/Evaneos/trolamine/blob/master/src/Trolamine/Core/Operation/MethodSecurityExpressionRoot.php
  [11]: https://github.com/Evaneos/trolamine/blob/master/src/Trolamine/Core/Exception/AccessDeniedException.php