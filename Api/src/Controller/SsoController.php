<?php

/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Api\Controller;

use Common\Entity\MetaLabels;
use Common\Entity\Userssometa;
use Common\Event\EventTargets;
use RestApi\Controller\ApiController;
use Common\Entity\Usersso;
use Common\Entity\Role;
use Common\Entity\Permission;
use Zend\View\Model\JsonModel;
use Api\Form\ApiUserSsoForm;
use Api\Form\ApiThirdpartySsoForm;
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
    private $ssoManager;
    private $ssoMetaManager;
    private $authService;
    private $apiUserId;
    private $apiUserEmail;
    private $roleManager;
    private $apiValidateTokenManager;
    private $moduleName;
    private $actions;

    /**
     * User manager.
     * @var Admin\Service\RbacManager
     */
    private $payload;

    public function __construct($entityManager, $ssoManager, $authService, $ssoMetaManager, $apiValidateTokenManager ,$roleManager)
    {
        $this->entityManager = $entityManager;
        $this->ssoManager = $ssoManager;
        $this->ssoMetaManager = $ssoMetaManager;
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

    /*
     * adds user in sso table
     */

    public function postAction()
    {
        $data = $this->params()->fromPost();
        $date = str_replace('/', '-', $data['dob']);
        $data['dob'] = date('Y-m-d', strtotime($date));
        $form = new ApiUserSsoForm('create', $this->entityManager, $data);
        $form->setData($data);

        $validStates = array('NSW','QLD','SA','TAS','VIC','WA','ACT','NT');

        if(!in_array(strtoupper($data['state']),$validStates)){
            $this->httpStatusCode = 403;
            $this->apiResponse['message'] = "State not allowed.";
            return $this->createResponse();
        }

        if ($form->isValid()) {
            $password_hash = $this->phpass()->HashPassword($data['ssoPassword']);
            $data['ssoPassword'] = $password_hash;
            //getting Sso unique ID
            $data['sso_id'] = $this->getUniqueSSoId();

            if ($user = $this->ssoManager->addUserSso($data)) {
                $data_meta = array();
                foreach ($data['meta'] as $m => $v) {
                    $data_meta['meta_key'] = $m;
                    $data_meta['meta_value'] = $v;
                    $this->ssoMetaManager->addSsometa($user, $data_meta);
                }
                //activity log
                $this->activitylog()->writeLog($this->actions['post'], "Signup successful", $data , $this->apiUserId ,$this->moduleName, "add" ,  (is_object($user) ? $user->getId() : 0) );


                // Set the response
                $this->setSsoUserDataResponse($user);
                return $this->createResponse();
            } else {
                //activity log
                $this->activitylog()->writeLog($this->actions['post'], "User name already exists", $data , $this->apiUserId ,$this->moduleName, "error" ,  (is_object($user) ? $user->getId() : 0) );
                $this->httpStatusCode = 403;
                $this->apiResponse['message'] = 'User name already exists';
                return $this->createResponse();
                //echo "out from validate";
            }
        } else {
            //activity log
            $this->activitylog()->writeLog($this->actions['post'], $form->getMessages(), $data , $this->apiUserId ,$this->moduleName, "error" ,  0 );
            $this->httpStatusCode = 403;
            $this->apiResponse['message'] = $form->getMessages();
            return $this->createResponse();
        }
        exit;
    }

    public function putAction() {
        //edit
        $data = $this->params()->fromPost();
        $dataMeta = $data['meta'];
        $date = str_replace('/', '-', $data['dob']);
        $data['dob'] = date('Y-m-d', strtotime($date));
        $form = new ApiUserSsoForm('update', $this->entityManager, $data);

        $form->setData($data);
        if ($form->isValid()) {
            $user = $this->entityManager->getRepository(Usersso::class)
                ->findOneByUserName($data['username']);
            if ($user != null) {
                // Get filtered and validated data
                $data = $form->getData();
                // Update the user.
                if ($editUser = $this->ssoManager->updateUser($user, $data)) {
                    //add/update meta values
                    if(count($dataMeta)>0) {
                        foreach ($dataMeta as $m => $v) {
                            $meta['meta_key'] = $m;
                            $meta['meta_value'] = $v;
                            $meta['user_sso_id'] = $user->getId();
                            $this->metaPlugin()->addUpdateMeta($meta);
                        }
                    }
                    //activity log
                    $this->activitylog()->writeLog($this->actions['put'], "User profile update successfully", $data , $this->apiUserId ,$this->moduleName, "update" ,  (is_object($user) ? $user->getId() : 0) );
                    $this->httpStatusCode = 200;
                    // Set the response
                    $this->setSsoUserDataResponse($user, 'update');
                    return $this->createResponse();
                } else {
                    //activity log
                    $this->activitylog()->writeLog($this->actions['put'], "Record can not be editted, Please try again later", $data , $this->apiUserId ,$this->moduleName, "error" ,  0 );

                    $this->httpStatusCode = 403;
                    $this->apiResponse['message'] = "Record can not be editted, Please try again later";
                    return $this->createResponse();
                }
            } else {
                //activity log
                $this->activitylog()->writeLog($this->actions['put'], "User not found.", $data['username'] , $this->apiUserId ,$this->moduleName, "error" ,  0 );

                $this->httpStatusCode = 403;
                $this->apiResponse['message'] = "User not found.";
                return $this->createResponse();
            }
        } else {
            //activity log
            $this->activitylog()->writeLog($this->actions['put'], $form->getMessages(), $data , $this->apiUserId ,$this->moduleName, "error" ,  0 );
            $this->httpStatusCode = 403;
            $this->apiResponse['message'] = $form->getMessages();
            return $this->createResponse();
        }
        exit;
    }

    public function changepasswordAction()
    {

        $data = $this->params()->fromPost();
        $user = $this->entityManager->getRepository(Usersso::class)
            ->findOneByEmail($data['ssoEmail']);
        if ($user != null) {
            $securePass = $user->getPassword();
            if ($this->phpass()->CheckPassword($data['ssoPassword'], $securePass)) {
                if (!empty($data['newPassword'])) {
                    $password_hash = $this->phpass()->HashPassword($data['newPassword']);
                    $data['newPassword'] = $password_hash;
                    if ($user = $this->ssoManager->changePassword($user, $data)) {
                        //activity log
                        $this->activitylog()->writeLog($this->actions['changepassword'], "User password update successfully", $data, $this->apiUserId, $this->moduleName, "update", (is_object($user) ? $user->getId() : 0));
                        // Set the response
                        $this->httpStatusCode = 200;
                        $this->apiResponse['action'] = 'changepassword';
                        $this->apiResponse['status'] = 'success';
                        $this->apiResponse['data']['password_hash'] = $password_hash;
                        return $this->createResponse();
                    } else {
                        //activity log
                        $this->activitylog()->writeLog($this->actions['changepassword'], "Password not changed, Please try again later", $data['ssoEmail'], $this->apiUserId, $this->moduleName, "error", (is_object($user) ? $user->getId() : 0));
                        $this->httpStatusCode = 403;
                        $this->apiResponse['message'] = "Password not changed, Please try again later.";
                        return $this->createResponse();
                    }
                } else {
                    //activity log
                    $this->activitylog()->writeLog($this->actions['changepassword'], "Empty password not allowed", $data['ssoEmail'], $this->apiUserId, $this->moduleName, "error", (is_object($user) ? $user->getId() : 0));
                    $this->httpStatusCode = 403;
                    $this->apiResponse['message'] = "Empty password not allowed.";
                    return $this->createResponse();
                }
            } else {
                //activity log
                $this->activitylog()->writeLog($this->actions['changepassword'], "Old password is not correct", $data['ssoEmail'], $this->apiUserId, $this->moduleName, "error", (is_object($user) ? $user->getId() : 0));

                $this->httpStatusCode = 403;
                $this->apiResponse['message'] = "Old password is not correct.";
                return $this->createResponse();
            }
        } else {
            //activity log
            $this->activitylog()->writeLog($this->actions['changepassword'], "User not found.", $data['ssoEmail'], $this->apiUserId, $this->moduleName, "error", (is_object($user) ? $user->getId() : 0));
            $this->httpStatusCode = 403;
            $this->apiResponse['message'] = "User not found.";
            return $this->createResponse();
        }
        exit;
    }

    public function validateAction()
    {
        $data = $this->params()->fromPost();
        $user = $this->entityManager->getRepository(Usersso::class)
            ->findOneByEmail($data['ssoEmail']);
        if (empty($user) || !is_object($user)) {
            $user = $this->entityManager->getRepository(Usersso::class)
                ->findOneByUserName($data['ssoEmail']);
        }

        if (is_object($user)) {
            $status = $user->getStatus();
            if ($status != 1) {
                //activity log
                $this->activitylog()->writeLog($this->actions['validate'], "Inactive user", $data['ssoEmail'], $this->apiUserId, $this->moduleName, "error", (is_object($user) ? $user->getId() : 0));

                $this->httpStatusCode = 403;
                // Set the response
                $this->apiResponse['action'] = 'NOK';
                $this->apiResponse['message'] = "Your current status is In-Active, Please contact admin to Activate your account";
                return $this->createResponse();
            }
            $securePass = $user->getPassword();
            if ($this->phpass()->CheckPassword($data['ssoPassword'], $securePass)) {
                //activity log
                $this->activitylog()->writeLog($this->actions['validate'], "User authenticated successfully.", $data['ssoEmail'] , $this->apiUserId ,$this->moduleName, "add" ,  (is_object($user) ? $user->getId() : 0) );
                // Set the response
                $this->setSsoUserDataResponse($user, 'authenticate');
                return $this->createResponse();
            } else {

                //activity log
                $this->activitylog()->writeLog($this->actions['validate'], "Invalid email or password", $data['ssoEmail'], $this->apiUserId, $this->moduleName, "error", (is_object($user) ? $user->getId() : 0));

                // Set the response
                $this->httpStatusCode = 403;
                $this->apiResponse['action'] = 'NOK';
                $this->apiResponse['message'] = "Invalid email or password";
                return $this->createResponse();
            }
        } else {
            //activity log
            $this->activitylog()->writeLog($this->actions['validate'], "User not found", $data['ssoEmail'], $this->apiUserId, $this->moduleName, "error", (is_object($user) ? $user->getId() : 0));

            // Set the response
            $this->httpStatusCode = 403;
            $this->apiResponse['action'] = 'NOK';
            $this->apiResponse['message'] = 'User not found';
            return $this->createResponse();
        }
        exit;
    }

    /**
     * @param $ssoData
     * @return sso_id
     */
    public function getUniqueSSoId()
    {
        $sso_id = $this->entityManager->getRepository(Usersso::class)->getMax();
        $ssoData['sso_id'] = $sso_id + 1;

        $uniqueSso = false;
        while ($uniqueSso == true) {
            $checkSsoId = $this->entityManager->getRepository(Usersso::class)
                ->findOneBySsoId($ssoData['sso_id']);
            if ($checkSsoId == null) {
                $uniqueSso = true;
            }
            $ssoData['sso_id']++;
        }
        return $ssoData['sso_id'];
    }

    /*
     * function to reset password
     */

    public function setResetPasswordTokenAction()
    {
        $data = $this->params()->fromPost();
        $user = $this->entityManager->getRepository(Usersso::class)
            ->findOneByEmail($data['ssoEmail']);
        if (!is_object($user)) {
            $user = $this->entityManager->getRepository(Usersso::class)
                ->findOneByUserName($data['ssoEmail']);
        }

        if (is_object($user)) {
            $length = 75;
            $token = bin2hex(random_bytes($length));
            if (strlen($token) > 255) {
                $token = substr($token, 0, 255);
            }

            $date = new \DateTime(date('Y-m-d H:i:s'));
            $date->add(new \DateInterval('PT15M'));
            $tokenExpiry = $date->format('Y-m-d H:i:s');

            if ($user = $this->ssoManager->addResetPasswordToken($user, $token, $tokenExpiry)) {
                //activity log
                $this->activitylog()->writeLog($this->actions['setResetPasswordToken'], "Reset password token created successfully", $data['ssoEmail'], $this->apiUserId, $this->moduleName, "add", (is_object($user) ? $user->getId() : 0));
                // Set the response
                $this->httpStatusCode = 200;
                $this->apiResponse['action'] = 'Reset Link Sent in email.';
                $this->apiResponse['status'] = 'success';
                $this->apiResponse['data']['reset_password_token'] = $token;
                //$this->apiResponse['message'] = $generatePassword;
                //$this->apiResponse['data']['password_hash'] = $password_hash;
                $this->apiResponse['data']['username'] = $user->getUserName();
                return $this->createResponse();
            } else {
                //activity log
                $this->activitylog()->writeLog($this->actions['setResetPasswordToken'], "Password can not be changed, Please try again later", $data['ssoEmail'], $this->apiUserId, $this->moduleName, "error", 0);

                $this->httpStatusCode = 403;
                $this->apiResponse['message'] = "Password can not be changed, Please try again later";
                return $this->createResponse();
            }
        } else {
            //activity log
            $this->activitylog()->writeLog($this->actions['setResetPasswordToken'], "User not found.", $data['ssoEmail'], $this->apiUserId, $this->moduleName, "error", 0);
            $this->httpStatusCode = 403;
            $this->apiResponse['message'] = "User not found.";
            return $this->createResponse();
        }
    }

    public function resetPasswordByTokenAction()
    {
        $data = $this->params()->fromPost();
        $user = $this->entityManager->getRepository(Usersso::class)
            ->checkResetPasswordToken($data['reset_password_token']);

        if (isset($user[0]['email'])) {
            $email = $user[0]['email'];
            $user = $this->entityManager->getRepository(Usersso::class)->findOneByEmail($email);

            $password_hash = $this->phpass()->HashPassword($data['newPassword']);
            $data['newPassword'] = $password_hash;
            if ($user = $this->ssoManager->changePassword($user, $data)) {
                //activity log
                $this->activitylog()->writeLog($this->actions['resetPasswordByToken'], "Reset password successfully", $email, $this->apiUserId, $this->moduleName, "update", 0);

                // Set the response
                $this->httpStatusCode = 200;
                $this->apiResponse['action'] = 'resetpassword';
                $this->apiResponse['status'] = 'success';
                $this->apiResponse['data']['password_hash'] = $password_hash;
                $this->apiResponse['data']['username'] = $user->getUserName();
                return $this->createResponse();
            } else {
                //activity log
                $this->activitylog()->writeLog($this->actions['resetPasswordByToken'], "Password can not be changed, Please try again later", $email, $this->apiUserId, $this->moduleName, "error", 0);


                $this->httpStatusCode = 403;
                $this->apiResponse['message'] = "Password can not be changed, Please try again later.";
                return $this->createResponse();
            }
        } else {

            //activity log
            $this->activitylog()->writeLog($this->actions['resetPasswordByToken'], "User not found or Invalid reset token", $data, $this->apiUserId, $this->moduleName, "error", 0);

            $this->httpStatusCode = 403;
            $this->apiResponse['message'] = "User not found or Invalid reset token.";
            return $this->createResponse();
        }
    }

    /**
     * @param $user
     */
    protected function setSsoUserDataResponse($user, $action = 'create')
    {
        $this->httpStatusCode = 200;
        $this->apiResponse['action'] = $action;
        $this->apiResponse['status'] = 'success';
        $this->apiResponse['data']['id'] = $user->getId();
        $this->apiResponse['data']['email'] = $user->getEmail();
        $this->apiResponse['data']['FirstName'] = $user->getFirstName();
        $this->apiResponse['data']['LastName'] = $user->getLastName();
        $this->apiResponse['data']['Gender'] = $user->getGender();
        $this->apiResponse['data']['Suburb'] = $user->getSuburb();
        $this->apiResponse['data']['State'] = $user->getState();
        $this->apiResponse['data']['Postcode'] = $user->getPostcode();
        $this->apiResponse['data']['Dob'] = $user->getDob();
        $this->apiResponse['data']['Mobile'] = $user->getMobile();
        $this->apiResponse['data']['UserName'] = $user->getUserName();
        $this->apiResponse['data']['Role'] = $user->getRole();
        $this->apiResponse['data']['meta'] = $this->ssoManager->get_user_meta($user);
        $this->apiResponse['data']['Address'] = $user->getAddress();
        $this->apiResponse['data']['password_hash'] = $user->getPassword();
    }

    /*
     * used to get sso user  by userName
     * @parameeter userName
     */
    public function getuserAction()
    {
        $data = $this->params()->fromPost();
        $user = $this->entityManager->getRepository(Usersso::class)->findOneByuserName($data['userName']);
        if (is_object($user)) {
            $this->activitylog()->writeLog($this->actions['view'], "User fetched successfully", $data, $this->apiUserId, $this->moduleName, "view", (is_object($user) ? $user->getId() : 0));
            $this->httpStatusCode = 200;
            // Set the response
            $this->setSsoUserDataResponse($user, 'view');


            $label = $setting_query = $this->entityManager->getRepository(MetaLabels::class)
                ->getMetaLabelsByCat('User');

            $this->apiResponse['data']['metaLabels']= $label->getResult(2);
            $this->apiResponse['data']['metaLabels']= $label?$label->getResult(2):[];
            return $this->createResponse();
        }
        //activity log
        $this->activitylog()->writeLog($this->actions['view'], "User fetching failed, Please try again later", $data, $this->apiUserId, $this->moduleName, "error", 0);
        $this->httpStatusCode = 403;
        $this->apiResponse['message'] = "User not found, Please try again later.";
        return $this->createResponse();

    }

    public function getuserbulkAction()
    {
        $data = $this->params()->fromPost();
        $userNames = explode(",",$data['userName']);
        //$user = array();
        foreach($userNames as $userName)
        {
            $users[] = $this->entityManager->getRepository(Usersso::class)->findOneByuserName($userName);

        }
        if (!empty($users)) {
            $this->activitylog()->writeLog($this->actions['view'], "User fetched successfully", json_encode($data), $this->apiUserId, $this->moduleName, "view", json_encode($userNames));
            // Set the response
            $this->httpStatusCode = 200;
            $this->apiResponse['action'] = 'view';
            $this->apiResponse['status'] = 'success';

            $response = array();$i = 0;
            foreach($users as $user)
            {
                $this->apiResponse['data'][$i]['id'] = $user->getId();
                $this->apiResponse['data'][$i]['email'] = $user->getEmail();
                $this->apiResponse['data'][$i]['FirstName'] = $user->getFirstName();
                $this->apiResponse['data'][$i]['LastName'] = $user->getLastName();
                $this->apiResponse['data'][$i]['Gender'] = $user->getGender();
                $this->apiResponse['data'][$i]['Suburb'] = $user->getSuburb();
                $this->apiResponse['data'][$i]['State'] = $user->getState();
                $this->apiResponse['data'][$i]['Postcode'] = $user->getPostcode();
                $this->apiResponse['data'][$i]['Dob'] = $user->getDob();
                $this->apiResponse['data'][$i]['Mobile'] = $user->getMobile();
                $this->apiResponse['data'][$i]['UserName'] = $user->getUserName();
                $this->apiResponse['data'][$i]['meta'] = $this->ssoManager->get_user_meta($user);
                $this->apiResponse['data'][$i]['Address'] = $user->getAddress();
                $this->apiResponse['data'][$i]['password_hash'] = $user->getPassword();
                $this->apiResponse['message'] = "User fetched successfully";
                $i++;
                //$response[] = $this->setSsoUserDataResponse($u, 'view');
            }
            return $this->createResponse();exit;
        }
        //activity log
        $this->activitylog()->writeLog($this->actions['view'], "User fetching failed, Please try again later", json_encode($data), $this->apiUserId, $this->moduleName, "error", json_encode($userNames));
        $this->httpStatusCode = 403;
        $this->apiResponse['message'] = "User not found, Please try again later.";
        return $this->createResponse();

    }


    /*
     * used to get sso user  by userName
     * @parameeter userName
     */
    public function getuserIdNameAction()
    {
        $data = $this->params()->fromPost();
        

        $user = $this->entityManager->getRepository(Usersso::class)->getUserIdName($data['username'],$data['fname'],$data['lname'],$data['mobile']);

        if (!empty($user)) {
            $this->httpStatusCode = 200;
            // Set the response
            $this->apiResponse['action'] = 'view';
            $this->apiResponse['status'] = 'success';
            $this->apiResponse['data']['result'] = $user;

            return $this->createResponse();
        }
        $this->httpStatusCode = 403;
        $this->apiResponse['message'] = "User not found, Please try again later.";
        return $this->createResponse();

    }

    /*
     * used to get sso user  by userName
     * @parameeter userName
     */
    public function getuserbycriteriaAction()
    {
        $data = $this->params()->fromPost();

        $user = $this->entityManager->getRepository(Usersso::class)->findOneBy(array($data['criteria'] => $data['value']));

        if (!empty($user)) {
            $this->httpStatusCode = 200;
            // Set the response
            $this->apiResponse['action'] = 'view';
            $this->apiResponse['status'] = 'success';
            $this->apiResponse['data']['result'] = $user;
            $this->setSsoUserDataResponse($user, 'view');

            return $this->createResponse();
        }
        $this->httpStatusCode = 403;
        $this->apiResponse['message'] = "User not found, Please try again later.";
        return $this->createResponse();

    }

    //Function to change user profile through third party but first check current password is valid
    public function updateUserProfileByPasswordAction()
    {
        $data = $this->params()->fromPost();
        $user = $this->entityManager->getRepository(Usersso::class)
            ->findOneByUserName($data['username']);
        if ($user != null) {
            $securePass = $user->getPassword();
            if ($this->phpass()->CheckPassword($data['ssoPassword'], $securePass)) {
                if (!empty($data['dob'])) {
                    $date = str_replace('/', '-', $data['dob']);
                    $data['dob'] = date('Y-m-d', strtotime($date));
                }
                $form = new ApiThirdpartySsoForm('api-update', $this->entityManager, $data);

                $form->setData($data);
                if ($form->isValid()) {

                    // Get filtered and validated data
                    $data = $form->getData();
                    // Update the user.
                    if ($editUser = $this->ssoManager->updateUserThirdParty($user, $data)) {
                        //activity log
                        $this->activitylog()->writeLog($this->actions['updateProfileByPassword'], "User profile update successfully", $data, $this->apiUserId, $this->moduleName, "update", (is_object($user) ? $user->getId() : 0));
                        $this->httpStatusCode = 200;
                        // Set the response
                        $this->setSsoUserDataResponse($user, 'update');
                        return $this->createResponse();
                    } else {
                        //activity log
                        $this->activitylog()->writeLog($this->actions['updateProfileByPassword'], "User profile not changed, Please try again later", $data, $this->apiUserId, $this->moduleName, "error", 0);

                        $this->httpStatusCode = 403;
                        $this->apiResponse['message'] = "User profile not changed, Please try again later";
                        return $this->createResponse();
                    }

                } else {
                    //activity log
                    $this->activitylog()->writeLog($this->actions['updateProfileByPassword'], $form->getMessages(), $data, $this->apiUserId, $this->moduleName, "error", 0);
                    $this->httpStatusCode = 403;
                    $this->apiResponse['message'] = $form->getMessages();
                    return $this->createResponse();
                }
            } else {
                //activity log
                $this->activitylog()->writeLog($this->actions['updateProfileByPassword'], "Password is not correct", $data['ssoEmail'], $this->apiUserId, $this->moduleName, "error", (is_object($user) ? $user->getId() : 0));

                $this->httpStatusCode = 403;
                $this->apiResponse['message'] = "Password is not correct.";
                return $this->createResponse();
            }
        } else {
            //activity log
            $this->activitylog()->writeLog($this->actions['updateProfileByPassword'], "User not found.", $data['ssoEmail'], $this->apiUserId, $this->moduleName, "error", (is_object($user) ? $user->getId() : 0));
            $this->httpStatusCode = 403;
            $this->apiResponse['message'] = "User not found.";
            return $this->createResponse();
        }
    }

    public function getmetalabelsAction()
    {
        $getArr = $this->getRequest()->getQuery()->toArray();
        $search = $getArr['search']??'';

        $label = $setting_query = $this->entityManager->getRepository(MetaLabels::class)
                ->getAllMetaLabels($search);
        if (!empty($label)) {
            $this->httpStatusCode = 200;
            // Set the response
            $this->apiResponse['action'] = 'view';
            $this->apiResponse['status'] = 'success';
            $this->apiResponse['data']['result'] = $label->getResult(2);

            return $this->createResponse();
        }
        $this->httpStatusCode = 403;
        $this->apiResponse['message'] = "User not found, Please try again later.";
        return $this->createResponse();
    }

    public function getMetaLabelsAvailableAction()
    {
        $data = $this->params()->fromPost();
        $user_name = $data['user_name']??'';

        $user = $this->entityManager->getRepository(Usersso::class)->findOneByUserName($user_name);
        $metaKeys = array();

        foreach ($user->getUserSsoMeta() as $meta):

            $metaKeys[] = $meta->getKey();

        endforeach;

        $labels = $this->entityManager->getRepository(MetaLabels::class)->getMetaLabelsEx($metaKeys);

        if (!empty($labels->getResult(2))) {
            // Set the response
            $this->httpStatusCode = 200;
            $this->apiResponse['action'] = 'view';
            $this->apiResponse['status'] = 'success';
            $this->apiResponse['data']['result'] = $labels->getResult(2);

            return $this->createResponse();
        }
        else{
            $this->httpStatusCode = 403;
            $this->apiResponse['message'] = "No more keys can added";
            $this->apiResponse['data']['result'] = [];
            return $this->createResponse();
        }
    }


    public function addUpdateMetaAction()
    {
        $data = $this->params()->fromPost();
        $res = array();

        $check = $this->entityManager->getRepository(Userssometa::class)
            ->getSsoMeta($data);
        if(!empty($check)){
            $usermeta = $this->entityManager->getRepository(Userssometa::class)
                ->find($check[0]['id']);

        /*if ($meta = self::checkMetaExixsts($data)) {
            $usermeta = $this->entityManager->getRepository(Userssometa::class)
                ->find($meta[0]->getId());*/
            if(is_object($usermeta))
            {
                $this->ssoMetaManager->updateSsometa($usermeta, $data );
                $res[] = $data['meta_key'];
            }
        } else {
            $user = $this->entityManager->getRepository(Usersso::class)
                ->find($data['user_sso_id']);
            if(is_object($user)) {
                $res[] = $data['meta_key'];
                $this->ssoMetaManager->addSsometa($user, $data);
            }
        }

        // Set the response
        if(!empty($res)){
            $this->httpStatusCode = 200;
            $this->apiResponse['action'] = 'view';
            $this->apiResponse['status'] = 'success';
            $this->apiResponse['data']['result'] = $res;
        }
        else{
            $this->httpStatusCode = 401;
            $this->apiResponse['action'] = 'view';
            $this->apiResponse['status'] = 'error';
            $this->apiResponse['data']['result'] = $res;
        }
        return $this->createResponse();
    }
/*
 * mistake here need to correct funtion name right now it username but ...
 */
    public function addUpdateMetaBulkAction()
    {
        $data = $this->params()->fromPost();
        $first_name = $this->params()->fromPost('first_name' , -1);
        $last_name = $this->params()->fromPost('last_name' , -1);

        $res = array();

        $ssoUsername = $this->params()->fromPost('ssoUsername');
        $user = $this->entityManager->getRepository(Usersso::class)
            ->findOneByUserName($ssoUsername);
        if(!is_object($user))
        {
            $this->httpStatusCode = 403;
            $this->apiResponse['message'] = "User not found on SSO";
            return $this->createResponse();
        }
        $message = "";
        if($first_name != -1 && $last_name != -1)
        {
            unset($data['first_name']);
            unset($data['last_name']);

            $profileData['first_name'] = $first_name;
            $profileData['last_name'] = $last_name;
            if ($editUser = $this->ssoManager->updateFistNameLastName($user, $profileData)) {
               $message = "First name & last name updated successfully";
            }else {
                $this->httpStatusCode = 401;
                $this->apiResponse['action'] = 'view';
                $this->apiResponse['status'] = 'error';
                $this->apiResponse['data']['result'] = "Error while update first name or last name, please try again later";
                return $this->createResponse();
            }
        }

        $metaData['user_sso_id'] = $user->getId();
        foreach($data as $d => $key)
        {
            if($d == 'ssoUsername'){
                continue;
            }
            $metaData['meta_key'] = $d;
            $metaData['meta_value'] = $key;

            $check = $this->entityManager->getRepository(Userssometa::class)
                ->getSsoMeta($metaData);
            if(!empty($check)){
                $meta = $this->entityManager->getRepository(Userssometa::class)
                    ->find($check[0]['id']);
                $this->ssoMetaManager->updateSsometaBulk($meta, $metaData );
                $res[] = array('meta_key' =>$metaData['meta_key'], 'meta_value' =>$metaData['meta_value']);
            }else {
                $this->ssoMetaManager->addSsometaBulk($user, $metaData);
                $res[] = array('meta_key' => $metaData['meta_key'], 'meta_value' => $metaData['meta_value']);
            }
        }

        // Apply changes to database.
        $this->entityManager->flush();
        $data['username'] = $ssoUsername;


        $eventTarget = new EventTargets($this->entityManager, $data);
        $eventTarget->updateUserMetaSSOInfo();

        if(!empty($res)){
            $this->httpStatusCode = 200;
            $this->apiResponse['message'] = $message;
            $this->apiResponse['action'] = 'view';
            $this->apiResponse['status'] = 'success';
            $this->apiResponse['data']['result'] = $res;
        }
        else{
            $this->httpStatusCode = 401;
            $this->apiResponse['action'] = 'view';
            $this->apiResponse['message'] = $message;
            $this->apiResponse['status'] = 'error';
            $this->apiResponse['data']['result'] = $res;
        }
        return $this->createResponse();
    }

    public function getPharmacyByCodeAction()
    {
        $data = $this->params()->fromPost();
        $userMeta = $this->entityManager->getRepository(Userssometa::class)->findOneBy(array('key' => 'pharmacy_code' , 'value' => $data['code']));

        if (!empty($userMeta)) {
            $this->httpStatusCode = 200;
            // Set the response
            $this->apiResponse['action'] = 'view';
            $this->apiResponse['status'] = 'success';
            $this->setSsoUserDataResponse($userMeta->getUsersso(), 'view');

            return $this->createResponse();
        }
        $this->httpStatusCode = 403;
        $this->apiResponse['message'] = "Pharmacy code not valid, please try again later";
        return $this->createResponse();
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

    public function debug($debugArray)
    {
        echo "<pre>";
        print_r($debugArray);
        echo "</pre>";
    }
}
