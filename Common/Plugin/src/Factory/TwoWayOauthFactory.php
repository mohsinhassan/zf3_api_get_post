<?php
namespace CommonPlugin\Factory;

use Interop\Container\ContainerInterface;
use CommonPlugin\TwoWayOauthPlugin;
use Api\Service\OtpsManager;

class TwoWayOauthFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $entityManager = $container->get('doctrine.entitymanager.orm_default');
        $OtpsManager =  $container->get(OtpsManager::class);
        return new TwoWayOauthPlugin($entityManager,$OtpsManager); //, $authService $entityManager,
    }
}