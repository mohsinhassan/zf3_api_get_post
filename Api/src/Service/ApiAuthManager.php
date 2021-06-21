<?php
namespace Api\Service;

use Zend\Authentication\Result;
use Zend\Session\Container;

/**
 * The ApiAuthManager service is responsible for user's login/logout and simple access
 * filtering. The access filtering feature checks whether the current visitor
 * is allowed to see the given page or not.
 */
class ApiAuthManager
{
    // Constants returned by the access filter.
    const ACCESS_GRANTED = 1; // Access to the page is granted.
    const AUTH_REQUIRED  = 2; // Authentication is required to see the page.
    const ACCESS_DENIED  = 3; // Access to the page is denied.

    /**
     * Contents of the 'access_filter' config key.
     * @var array
     */
    private $config;

    /**
     * RBAC manager.
     * @var Api\Service\RbacManager
     */
    private $rbacManager;

    /**
     * Constructs the service.
     */
    //$authService, $sessionManager,
    public function __construct( $config, $rbacManager)
    {
        /*$this->authService = $authService;
        $this->sessionManager = $sessionManager;*/
        $this->config = $config;
        $this->rbacManager = $rbacManager;
    }

    /**
     * This is a simple access control filter. It is able to restrict unauthorized
     * users to visit certain pages.
     *
     * This method uses the 'access_filter' key in the config file and determines
     * whenther the current visitor is allowed to access the given controller action
     * or not. It returns true if allowed; otherwise false.
     */
    public function filterAccess($controllerName, $actionName, $user)
    {

        /*Adding code for populating side-menu*/
        // Determine mode - 'restrictive' (default) or 'permissive'. In restrictive
        // mode all controller actions must be explicitly listed under the 'access_filter'
        // config key, and access is denied to any not listed action for unauthorized users. 
        // In permissive mode, if an action is not listed under the 'access_filter' key, 
        // access to it is permitted to anyone (even for not logged in users.
        // Restrictive mode is more secure and recommended to use.
        $mode = isset($this->config['options']['mode'])?$this->config['options']['mode']:'restrictive';
        if ($mode!='restrictive' && $mode!='permissive')
            throw new \Exception('Invalid access filter mode (expected either restrictive or permissive mode');

        if (isset($this->config['controllers'][$controllerName])) {
            $items = $this->config['controllers'][$controllerName];
            foreach ($items as $item) {
                $actionList = $item['actions'];
                $allow = $item['allow'];
                if (is_array($actionList) && in_array($actionName, $actionList) ||
                    $actionList=='*') {
                    if ($allow=='*')
                        // Anyone is allowed to see the page.
                        return self::ACCESS_GRANTED;

                    if ($allow=='@') {
                        // Any authenticated user is allowed to see the page.
                        return self::ACCESS_GRANTED;
                    } /*else if (substr($allow, 0, 1)=='@') {
                        // Only the user with specific identity is allowed to see the page.
                        $identity = substr($allow, 1);
                        if ($this->authService->getIdentity()==$identity)
                            return self::ACCESS_GRANTED;
                        else
                            return self::ACCESS_DENIED;
                    } */
                    else if (substr($allow, 0, 1)=='+') {
                        // Only the user with this permission is allowed to see the page.
                        $permission = substr($allow, 1);
                        if ($this->rbacManager->isGranted($user, $permission))
                            return self::ACCESS_GRANTED;
                        else
                            return self::ACCESS_DENIED;
                    } else {
                        throw new \Exception('Unexpected value for "allow" - expected ' .
                            'either "?", "@", "@identity" or "+permission"');
                    }
                }
            }
        }

        // In restrictive mode, we require authentication for any action not 
        // listed under 'access_filter' key and deny access to authorized users 
        // (for security reasons).
        if ($mode=='restrictive') {
            /*if(!$this->authService->hasIdentity())
                return self::AUTH_REQUIRED;
            else*/
            if(empty($user))
                return self::AUTH_REQUIRED;
            else
                return self::ACCESS_DENIED;
        }

        // Permit access to this page.
        return self::ACCESS_GRANTED;
    }
}