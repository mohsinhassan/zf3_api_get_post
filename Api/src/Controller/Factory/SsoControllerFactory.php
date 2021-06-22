<?php
namespace Api\Controller\Factory;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Api\Controller\SsoController;
/**
 * This is the factory for UserController. Its purpose is to instantiate the
 * controller and inject dependencies into it.
 */
class SsoControllerFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {

        //$config = $container->get('config');
        $entityManager = $container->get('doctrine.entitymanager.orm_default');
        $ssoManager = $container->get(\Api\Service\ApiSsoUserManager::class);
        $roleManager = $container->get(\Api\Service\ApiRoleManager::class);
        $authService = $container->get(\Zend\Authentication\AuthenticationService::class);
        $apiValidateTokenManager = $container->get(\Api\Service\ApiValidateTokenManager::class);
        
        // Instantiate the controller and inject dependencies
        return new SsoController($entityManager, $ssoManager , $authService , $apiValidateTokenManager , $roleManager);
    }
}