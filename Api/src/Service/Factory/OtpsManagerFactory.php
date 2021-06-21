<?php
namespace Api\Service\Factory;
use Interop\Container\ContainerInterface;
use Api\Service\OtpsManager;


/**
 * This is the factory class for OtpsManager service. The purpose of the factory
 * is to instantiate the service and pass it dependencies (inject dependencies).
 */
class OtpsManagerFactory
{
    /**
     * This method creates the OtpsManager service and returns its instance.
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get('doctrine.entitymanager.orm_default');

        return new OtpsManager($entityManager); //,$authManager,$authService
    }
}