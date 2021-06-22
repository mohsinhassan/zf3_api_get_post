<?php
namespace Api\Service\Factory;
use Interop\Container\ContainerInterface;
use Api\Service\ApiRoleManager;
use Api\Service\RbacManager;

/**
 * This is the factory class for ApiRoleManager service. The purpose of the factory
 * is to instantiate the service and pass it dependencies (inject dependencies).
 */
class ApiRoleManagerFactory
{
    /**
     * This method creates the ApiRoleManager service and returns its instance.
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get('doctrine.entitymanager.orm_default');
        $rbacManager = $container->get(RbacManager::class);

        return new ApiRoleManager($entityManager , $rbacManager);
    }
}