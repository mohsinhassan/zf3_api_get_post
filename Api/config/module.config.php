<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Api;

use Zend\Router\Http\Segment;
use Zend\ServiceManager\Factory\InvokableFactory;

return [
'router' => [
        'routes' => [
            'gettoken' => [
                'type' => Segment::class,
                'options' => [
                    'route'    => '/api/apitoken[/:action]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ],
                    'defaults' => [
                        'controller' => Controller\ApiTokenController::class,
                        'action' => 'getToken',
                        'isAuthorizationRequired' => false // set true if this api Required JWT Authorization.
                    ],
                ],
            ],
            'apisso' => [
                'type' => Segment::class,
                'options' => [
                    'route'    => '/api/sso[/:action]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ],
                    'defaults' => [
                        'controller' => Controller\SsoController::class,
                        'action' => 'index',
                        'isAuthorizationRequired' => true // set true if this api Required JWT Authorization.
                    ],
                ],
            ],
        ],
    ],
    'controllers' => [
        'factories' => [
            /*Controller\ApiClinicalnotesController::class => Controller\Factory\ApiClinicalnotesControllerFactory::class,*/
            Controller\ApiTokenController::class => Controller\Factory\ApiTokenControllerFactory::class,
            Controller\SsoController::class => Controller\Factory\SsoControllerFactory::class,
        ],
    ],
    
    // The 'access_filter' key is used by the User module to restrict or permit
    // access to certain controller actions for unauthorized visitors.
    //@ type of permissions will not work for api
    'access_filter' => [
        'controllers' => [
            Controller\ApiTokenController::class => [
                ['actions' => ['getToken' , 'refreshToken'], 'allow' => '*'],
            ],
            Controller\SsoController::class => [
                ['actions' => ['validate','getuserbycriteria'], 'allow' => '*' ],
                ['actions' => ['post', 'getmetalabels','addUpdateMeta','getMetaLabelsAvailable','grantPermissionToUser'], 'allow' => '+api.sso.post' ],
                ['actions' => ['get', 'getUserValidation'], 'allow' => '+api.sso.post' ],
                ['actions' => ['put'], 'allow' => '+api.sso.put' ],
                ['actions' => ['addUpdateMetaBulk'], 'allow' => '+api.sso.put'],
                ['actions' => ['getPharmacyByCode'], 'allow' => '+api.sso.put'],
                ['actions' => ['updateUserProfileByPassword'], 'allow' => '+api.sso.updateuserprofilebypassword' ],
                ['actions' => ['setResetPasswordToken'], 'allow' => '+api.sso.setresetpasswordtoken' ],
                ['actions' => ['resetPasswordByToken'], 'allow' => '+api.sso.resetpasswordbytoken' ],
                ['actions' => ['getuser'], 'allow' => '+api.sso.getuser' ],
                ['actions' => ['getuserbulk'], 'allow' => '+api.sso.getuser' ],
                ['actions' => ['getuserIdName'], 'allow' => '+api.sso.getuseridname' ],
                ['actions' => ['changepassword'], 'allow' => '+api.sso.changepassword' ],

            ],

        ]
    ],
    // This key stores configuration for RBAC manager.
    'rbac_manager' => [
        'assertions' => [Service\RbacAssertionManager::class],
    ],
    'service_manager' => [
        'factories' => [
            /*Service\ApiClinicalnotesManager::class => Service\Factory\ApiClinicalnotesManagerFactory::class,*/
            Service\ApiTokenManager::class => Service\Factory\ApiTokenManagerFactory::class,
            Service\ApiValidateTokenManager::class => Service\Factory\ApiValidateTokenManagerFactory::class,
            Service\ApiSsoUserManager::class => Service\Factory\ApiSsoUserManagerFactory::class,
            Service\ApiSsoUserMetaManager::class => Service\Factory\ApiSsoUserMetaManagerFactory::class,
            Service\ApiAuthManager::class => Service\Factory\ApiAuthManagerFactory::class,
            Service\RbacAssertionManager::class => Service\Factory\RbacAssertionManagerFactory::class,
            Service\RbacManager::class => Service\Factory\RbacManagerFactory::class,
            Service\ApiRoleManager::class => Service\Factory\ApiRoleManagerFactory::class

        ],
    ],

    'doctrine' => [
        'driver' => [
            __NAMESPACE__ . '_driver' => [
                'class' => AnnotationDriver::class,
                'cache' => 'array',
                'paths' => [__DIR__ . '/../../Common/Entity']
            ],
            'orm_default' => [
                'drivers' => [
                    'Common\Entity' => __NAMESPACE__ . '_driver'
                ]
            ]
        ]
    ],
];
