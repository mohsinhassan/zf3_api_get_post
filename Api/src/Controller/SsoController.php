<?php

/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Api\Controller;

use Common\Event\EventTargets;
use RestApi\Controller\ApiController;
use Common\Entity\Usersso;
use Common\Entity\Role;
use Common\Entity\Permission;
use Zend\View\Model\JsonModel;
use Zend\Mvc\MvcEvent;

//use Zend\Mime\Mime;


class SsoController extends ApiController
{

    /**
     * Entity manager.
     * @var Doctrine\ORM\EntityManager
     */
    private $entityManager;

    /**
     * User manager.
     * @var Api\Service\ssoUserManager
     */
    private $roleManager;
    private $apiValidateTokenManager;

    /**
     * User manager.
     * @var Api\Service\RbacManager
     */
    private $payload;

    public function __construct($entityManager, $ssoManager, $authService,  $apiValidateTokenManager ,$roleManager)
    {
        $this->entityManager = $entityManager;
        $this->ssoManager = $ssoManager;
        $this->authService = $authService;
        $this->roleManager = $roleManager;
        $this->apiValidateTokenManager = $apiValidateTokenManager;
    }

    public function onDispatch(MvcEvent $e)
    {

        $decodeToken = $this->apiValidateTokenManager->decodeToken($e);
        $this->apiUserEmail = $decodeToken->email;
        $this->apiUserId = $decodeToken->user_id;
        $this->moduleName = 'sso';
        $this->actions = array('validate' => 'login',
            'post' => 'Signup',
            'put' => 'Update Profile',
            'updateProfileByPassword' => 'Update Profile By Password',
            'changepassword' => 'Change password',
            'setResetPasswordToken' => 'Set reset password token',
            'resetPasswordByToken' => 'reset password by token',
            'delete' => 'delete',
            'view' => 'View'
        );

        return parent::onDispatch($e);
    }



    /**
     * This function is used to get user has permission for specific feature 
     *
     * @return response json array success or failure 
     */
    public function getUserValidationAction()
    {
        $email = $this->params()->fromQuery('email','');
        $featureName = $this->params()->fromQuery('featureName','');
        $res = $this->entityManager->getRepository(Usersso::class)->isUserAllowed($email ,$featureName );
        if($res[0]['canAccess']>0)
        {
            $this->httpStatusCode = 200;           

        }else{
            $this->httpStatusCode = 403;
        }
        $this->apiResponse['canAccess'] = $res[0]['canAccess'];
        return $this->createResponse();
       
    }


    /**
     * This function is used to give user permission for specific feature 
     *
     * @return json array response array success or failure 
     */
    public function grantPermissionToUserAction()
    {
        $data['email'] = $this->params()->fromPost('email','');
        $data['featureName'] = $this->params()->fromPost('featureName','');
        
        $user = $this->entityManager->getRepository(Usersso::class)->findOneByEmail($data['email']);
        $role = $this->entityManager->getRepository(Role::class)->find($user->getRole()); //

        $permission = $this->entityManager->getRepository(Permission::class)->findOneByName($data['featureName']);

        // $permissions 
        $permissions = $role->getPermissions();
        $data['permissions'] = array();
        $data['permissions'][] = $data['featureName'];

        foreach($permissions as $p)
        {
            $data['permissions'][] = $p->getName();
        }
        
        $this->httpStatusCode = 403;
        $this->apiResponse['canAccess'] = 0;
        if ($this->roleManager->updateRolePermissions($role, $data)) {
            $this->httpStatusCode = 200;  
            $this->apiResponse['canAccess'] = 1;   
        }
       
        return $this->createResponse();
        
    }
}
