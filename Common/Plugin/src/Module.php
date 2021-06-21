<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace CommonPlugin;
use Zend\Mvc\MvcEvent;

class Module
{
    const VERSION = '3.0.3-dev';

    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }

    /**
     * This method is called once the MVC bootstrapping is complete and allows
     * to register event listeners.
     */
    public function onBootstrap(MvcEvent $event)
    {

        // Get event manager.
        $eventManager = $event->getApplication()->getEventManager();
        $sharedEventManager = $eventManager->getSharedManager();
        // Register the event listener method.
        $sharedEventManager->attach(__NAMESPACE__, 'dispatch',
            [$this, 'onDispatch'], 100);

        //$application = $event->getApplication();
        //$serviceManager = $application->getServiceManager();

        // The following line instantiates the SessionManager and automatically
        // makes the SessionManager the 'default' one.
        //$sessionManager = $serviceManager->get(SessionManager::class);
    }

    public function onDispatch(MvcEvent $event)
    {

        // Get controller and action to which the HTTP request was dispatched.
        //$controller = $event->getTarget();

        $controllerName = $event->getRouteMatch()->getParam('controller', null);

       /* if ($controllerName!=ApiTokenController::class)
        {
            $checkToken = $event->getApplication()->getServiceManager()->get(ApiValidateTokenManager::class);
            $isValid = $checkToken->isValidToken($event);
            if (!$isValid)  exit;
        }*/
    }
}
