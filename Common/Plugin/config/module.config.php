<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace CommonPlugin;
use Zend\ServiceManager\Factory\InvokableFactory;

return [
'router' => [
        'routes' => [
        ],
    ],
    'controllers' => [
        'factories' => [
        ],
    ],
    'controller_plugins' => [
        'factories' => [

            MetaPlugin::class => Factory\MetaPluginFactory::class,
            TwoWayOauthPlugin::class => Factory\TwoWayOauthFactory::class,
            SmsPlugin::class => Factory\SmsPluginFactory::class

        ],
        'aliases' => [
            'metaPlugin' => MetaPlugin::class,
            'TwoWayOauthPlugin' => TwoWayOauthPlugin::class,
            'smsPlugin' => SmsPlugin::class
        ],

    ],
    'service_manager' => [
        'factories' => [
        ],
    ],
];
