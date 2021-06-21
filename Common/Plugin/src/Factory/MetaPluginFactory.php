<?php
namespace CommonPlugin\Factory;

use Interop\Container\ContainerInterface;
use CommonPlugin\MetaPlugin;
use Common\Entity\Userssometa;
use Admin\Service\UserSsoMetaManager;

class MetaPluginFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $entityManager = $container->get('doctrine.entitymanager.orm_default');
        $metaManager = $container->get(UserSsoMetaManager::class);
        return new MetaPlugin($entityManager , $metaManager); //, $authService $entityManager,
    }
}