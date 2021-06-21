<?php
namespace CommonPlugin\Factory;

use Interop\Container\ContainerInterface;
use CommonPlugin\SmsPlugin;

class SmsPluginFactory
{
    public function __invoke(ContainerInterface $container)
    {        
        $entityManager = $container->get('doctrine.entitymanager.orm_default');
        $settings = $container->get(\Admin\Service\SettingManager::class);
        
        return new SmsPlugin($entityManager, $settings);
    }
}


