<?php
namespace Api\Service\Factory;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
//use Zend\Authentication\AuthenticationService;
//use Zend\Session\SessionManager;
use Api\Service\ApiAuthManager;
//use Admin\Service\UserManager;
use Api\Service\RbacManager;
/**
 * This is the factory class for ApiAuthManager service. The purpose of the factory
 * is to instantiate the service and pass it dependencies (inject dependencies).
 */
class ApiAuthManagerFactory implements FactoryInterface
{
    /**
     * This method creates the ApiAuthManager service and returns its instance.
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {        
        // Instantiate dependencies.
        //$authenticationService = $container->get(\Zend\Authentication\AuthenticationService::class);
        //$sessionManager = $container->get(SessionManager::class);
        $rbacManager = $container->get(RbacManager::class);
        
        // Get contents of 'access_filter' config key (the ApiAuthManager service
        // will use this data to determine whether to allow currently logged in user
        // to execute the controller action or not.
        $config = $container->get('Config');
        if (isset($config['access_filter']))
            $config = $config['access_filter'];
        else
            $config = [];
                        
        // Instantiate the ApiAuthManager service and inject dependencies to its constructor.
        //$authenticationService, $sessionManager,
        return new ApiAuthManager($config, $rbacManager);
    }
}