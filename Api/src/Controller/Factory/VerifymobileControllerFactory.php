<?php
namespace Api\Controller\Factory;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Api\Controller\VerifymobileController;
//use Admin\Service\AuthManager;
/**
 * This is the factory for UserController. Its purpose is to instantiate the
 * controller and inject dependencies into it.
 */
class VerifymobileControllerFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {

        //$config = $container->get('config');
        $entityManager = $container->get('doctrine.entitymanager.orm_default');
        $otpsManager = $container->get(\Api\Service\OtpsManager::class);
        $apiValidateTokenManager = $container->get(\Api\Service\ApiValidateTokenManager::class);
        $ssoMetaManager = $container->get(\Api\Service\ApiSsoUserMetaManager::class);
        $ssoManager = $container->get(\Api\Service\ApiSsoUserManager::class);
        
        
        return new VerifymobileController($entityManager, $otpsManager,$apiValidateTokenManager,$ssoMetaManager,$ssoManager);
    }
}