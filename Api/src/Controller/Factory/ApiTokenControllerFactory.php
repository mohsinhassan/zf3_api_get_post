<?php
namespace Api\Controller\Factory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Api\Controller\ApiTokenController;
use Api\Service\ApiTokenManager;
use Api\Service\ApiSsoUserManager;
/**
 * This is the factory for UserController. Its purpose is to instantiate the
 * controller and inject dependencies into it.
 */
class ApiTokenControllerFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {

        //$config = $container->get('config');
        $entityManager = $container->get('doctrine.entitymanager.orm_default');
        $authService = $container->get(\Zend\Authentication\AuthenticationService::class);
        $apiTokenManager = $container->get(ApiTokenManager::class);
        $apiSsoUserManager = $container->get(ApiSsoUserManager::class);
        /*$authManager = $container->get(AuthManager::class);


        */
        // Instantiate the controller and inject dependencies
        return new ApiTokenController($entityManager, $authService ,$apiTokenManager ,$apiSsoUserManager);
    }
}