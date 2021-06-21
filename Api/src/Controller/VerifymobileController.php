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
use Zend\View\Model\JsonModel;
use Api\Form\ApiVerifyMobileForm;
use Api\Form\ApiVerifyOtpForm;
use Api\Form\ApiThirdpartySsoForm;
use Zend\Mvc\MvcEvent;
use Zend\Http\PhpEnvironment\RemoteAddress;
//use Zend\Mime\Mime;


class VerifymobileController extends ApiController
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
    private $apiValidateTokenManager;
    private $apiUserId;
    private $apiUserEmail;
    private $otpsManager;
    private $moduleName;
    private $ssoManager;

    /**
     * User manager.
     * @var Admin\Service\RbacManager
     */
    private $payload;

    public function __construct($entityManager,$otpsManager,$apiValidateTokenManager , $ssoMetaManager , $ssoManager)
    {
        $this->entityManager = $entityManager;
        $this->otpsManager = $otpsManager;
        $this->apiValidateTokenManager = $apiValidateTokenManager;
        $this->ssoMetaManager = $ssoMetaManager;
        $this->ssoManager = $ssoManager;
        
    }

    public function onDispatch(MvcEvent $e)
    {
        $decodeToken = $this->apiValidateTokenManager->decodeToken($e);
        $this->apiUserEmail = $decodeToken->email;
        $this->apiUserId = $decodeToken->user_id;
        $this->moduleName = 'verify-mobile';        

        return parent::onDispatch($e);
    }

    /**
     * Author : Mohsin Hassan
     * This is the API call function which generates OTP code for mobile verification
     */

    public function setotpAction()
    {
        $username = $this->params()->fromPost('username', -1);
        $mobile = $this->params()->fromPost('mobile', -1);
        $postData =$this->params()->fromPost();
        if (empty($username)) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        

        $form = new ApiVerifyMobileForm('api', $this->entityManager, $username);

        $form->setData($postData);
        if ($form->isValid()) {

            $user = $user = $this->entityManager->getRepository(Usersso::class)->findOneByUserName($username);
       
            if(empty($user))
            {
                //activity log
                $this->activitylog()->writeLog('setotp', "User not found.", $username , $this->apiUserId ,$this->moduleName, "error" ,  0 );

                $this->httpStatusCode = 403;
                $this->apiResponse['message'] = "User not found.";
                return $this->createResponse();
            }
            $remote = new RemoteAddress;
                        
            //Creating data for sms plugin call to send OTP through SMS
            $otpData['username'] = $username;
            $otpData['mobile_number'] = $mobile;
            $otpData['ipAddress'] = $remote->setUseProxy()->getIpAddress();
            $otp = $this->TwoWayOauthPlugin()->generateOtpMobile($otpData);
        
            $smsData['mobile_number'] = $mobile;
            $smsData['message'] = $otp->getOtp() . " is your mobile verification 6 digits code. Please verify your mobile number, Do not share this with any one.";
    
            if ($this->smsPlugin()->sendSmsByMobile($smsData)) {
                $this->TwoWayOauthPlugin()->setSmsSent(1,$otp);

                $this->activitylog()->writeLog('otp generated', 'OTP generated', $postData , $this->apiUserId ,$this->moduleName, "add" ,  0 );
                $this->httpStatusCode = 200;
                $this->apiResponse['message'] = "Done";
                return $this->createResponse();
            } else {
                $this->activitylog()->writeLog('otp generated', 'OTP generated', $postData , $this->apiUserId ,$this->moduleName, "error" ,  0 );
                $this->httpStatusCode = 403;
                $this->apiResponse['message'] = $form->getMessages();
                return $this->createResponse();
            }


        } else {
            //activity log
            $this->activitylog()->writeLog('otp generated', $form->getMessages(), $postData , $this->apiUserId ,$this->moduleName, "error" ,  0 );
            $this->httpStatusCode = 403;
            $this->apiResponse['message'] = $form->getMessages();
            return $this->createResponse();
        }      
    }

    /**
     * Author : Mohsin Hassan
     * This is the API call function to verify mobile number
     */
    public function verifyotpAction()
    {
        $username = $this->params()->fromPost('username', -1);
        $otp = $this->params()->fromPost('otp', -1);
        $postData =$this->params()->fromPost();
        if (empty($username)) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        $form = new ApiVerifyOtpForm('api', $this->entityManager, $username);

        $form->setData($postData);
        if ($form->isValid()) {
            $user = $user = $this->entityManager->getRepository(Usersso::class)->findOneByUserName($username);
       
            if(empty($user))
            {
                //activity log
                $this->activitylog()->writeLog('setotp', "User not found.", $username , $this->apiUserId ,$this->moduleName, "error" ,  0 );

                $this->httpStatusCode = 403;
                $this->apiResponse['message'] = "User not found.";
                return $this->createResponse();
            }
            $remote = new RemoteAddress;
            $otpData['username'] = $username;
            $otpData['ipAddress'] = $remote->setUseProxy()->getIpAddress();
            $otpData['otpExpiry'] = date('Y-m-d H:i:s');
            $otpData['otp'] = $otp;
            $otpData['type'] = 2; 

            // valid otp
            if ($verified = $this->TwoWayOauthPlugin()->verifyOtp($otpData)) {
                
                //adding data into meta 
                $metaData = array();
                $metaData[0]['meta_key'] = 'is_mobile_verified';
                $metaData[0]['meta_value'] = 1;
                $metaData[0]['user_sso_id'] = $user->getId();
                
                $metaData[1]['meta_key'] = 'mobile_verified_date';
                $metaData[1]['meta_value'] = $otpData['otpExpiry'];
                $metaData[1]['user_sso_id'] = $user->getId();

                $this->metaPlugin()->addUpdateMetaBulk($metaData);
                $data['mobile_number'] = $verified[0]->getMobileNumber();
                $this->ssoManager->setUserMobileNumber($user, $data);
                              
           
                $this->activitylog()->writeLog('otp-verified', 'OTP verified', $postData , $this->apiUserId ,$this->moduleName, "verified" ,  0 );
                $this->httpStatusCode = 200;
                $this->apiResponse['message'] = "Done";
                $this->apiResponse['data']['mobile'] = $verified[0]->getMobileNumber();
                $this->apiResponse['data']['username'] = $username;
                $this->apiResponse['data']['otp'] = $otp;
                return $this->createResponse();
            } else {
                $this->activitylog()->writeLog('otp-verified', 'OTP verified', $postData , $this->apiUserId ,$this->moduleName, "error" ,  0 );
                $this->httpStatusCode = 403;
                $this->apiResponse['message'] = "Verification failed, try again";
                return $this->createResponse();
            }
        } else {
            //activity log
            $this->activitylog()->writeLog('otp generated', $form->getMessages(), $postData , $this->apiUserId ,$this->moduleName, "error" ,  0 );
            $this->httpStatusCode = 403;
            $this->apiResponse['message'] = $form->getMessages();
            return $this->createResponse();
        }      
    }

    /**
     * Author : Mohsin Hassan
     * This is the API call function which generates OTP code for mobile verification
     */

    public function setotpresendAction()
    {
        $username = $this->params()->fromPost('username', -1);
        $mobile = $this->params()->fromPost('mobile', -1);
        $postData =$this->params()->fromPost();
        if (empty($username)) {
            $this->getResponse()->setStatusCode(404);
            return;
        }       

        $form = new ApiVerifyMobileForm('api', $this->entityManager, $username);

        $form->setData($postData);
        if ($form->isValid()) {

            $user = $user = $this->entityManager->getRepository(Usersso::class)->findOneByUserName($username);
       
            if(empty($user))
            {
                //activity log
                $this->activitylog()->writeLog('setotp', "User not found.", $username , $this->apiUserId ,$this->moduleName, "error" ,  0 );

                $this->httpStatusCode = 403;
                $this->apiResponse['message'] = "User not found.";
                return $this->createResponse();
            }
            $remote = new RemoteAddress;
                        
            //Creating data for sms plugin call to send OTP through SMS
            $otpData['username'] = $username;
            $otpData['mobile_number'] = $mobile;
            $otpData['ipAddress'] = $remote->setUseProxy()->getIpAddress();
            $otp = $this->TwoWayOauthPlugin()->generateOtpMobile($otpData);
        
            $smsData['mobile_number'] = $mobile;
            $smsData['message'] = $otp->getOtp() . " Otp 6 digits code resent to your mobile. Please verify your mobile number, Do not share this with any one.";
    
            if ($this->smsPlugin()->sendSmsByMobile($smsData)) {
                $this->TwoWayOauthPlugin()->setSmsSent(1,$otp);

                $this->activitylog()->writeLog('otp resend', 'OTP resend', $postData , $this->apiUserId ,$this->moduleName, "add" ,  0 );
                $this->httpStatusCode = 200;
                $this->apiResponse['message'] = "re-sent";
                return $this->createResponse();
            } else {
                $this->activitylog()->writeLog('otp generated', 'OTP generated', $postData , $this->apiUserId ,$this->moduleName, "error" ,  0 );
                $this->httpStatusCode = 403;
                $this->apiResponse['message'] = $form->getMessages();
                return $this->createResponse();
            }


        } else {
            //activity log
            $this->activitylog()->writeLog('otp generated', $form->getMessages(), $postData , $this->apiUserId ,$this->moduleName, "error" ,  0 );
            $this->httpStatusCode = 403;
            $this->apiResponse['message'] = $form->getMessages();
            return $this->createResponse();
        }      
    }
}
