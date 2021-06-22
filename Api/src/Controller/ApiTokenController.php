<?php

/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Api\Controller;

use Common\Entity\ApiAccessToken;
use RestApi\Controller\ApiController;
use Common\Entity\User;
use Common\Entity\Usersso;
use Zend\Http\PhpEnvironment\RemoteAddress;
use Zend\Crypt\Password\Bcrypt;
use Zend\View\Model\JsonModel;


class ApiTokenController extends ApiController
{

    /**
     * Entity manager.
     * @var Doctrine\ORM\EntityManager
     */
    private $entityManager;
    private $authService;
    private $apiTokenManager;
    private $apiSsoUserManager;
    private $patientRole;
    private $practitionerRole;


    public function __construct($entityManager ,$authService,  $apiTokenManager,$apiSsoUserManager)
    {
        $this->entityManager = $entityManager;
        $this->authService = $authService;
        $this->apiTokenManager = $apiTokenManager;
        $this->apiSsoUserManager = $apiSsoUserManager;
        $this->role = 'Patient';
    }

    public function getTokenAction()
    {

        if($this->params()->fromPost('client_id','')!=""){
            $email = $this->params()->fromPost('client_id','');
            $password = $this->params()->fromPost('client_secret','');
        }else{
            //POST : http://localhost/sso_zend/public/index.php/api/apitoken/getToken
            $email = $this->params()->fromPost('email','');
            $password = $this->params()->fromPost('password','');
        }
        //echo "email ". $email."=password ".$password."<br>";


        $authAdapter = $this->authService->getAdapter();
        $authAdapter->setEmail($email);
        $authAdapter->setPassword($password);
        $result = $authAdapter->authenticate();

        if ($result->isValid()){
            $user = $this->entityManager->getRepository(User::class)
                ->findOneByEmail($email);
            if(empty($user))
            {
                $this->httpStatusCode = 200;
                // Set the response
                $this->apiResponse['token'] = "";
                $this->apiResponse['refresh_token'] = "";
                $this->apiResponse['action'] = 'NOK';
                $this->apiResponse['message'] = "User not currently available on SSO Server";
                return $this->createResponse();

            }

            $fullName = $user->getFullName();
            $user_id = $user->getId();


            $remote = new RemoteAddress;

            $date = new \DateTime(date('Y-m-d H:i:s'));
            //$date->add(new \DateInterval('PT30M'));
            //$date->add(new \DateInterval('P5Y'));
            $date->add(new \DateInterval(API_TOKEN_EXPIRE_INTERVAL));


            $date2 = new \DateTime(date('Y-m-d H:i:s'));
            //$date2->add(new \DateInterval('PT24H'));
            $date2->add(new \DateInterval(API_REFRESH_TOKEN_EXPIRE_INTERVAL));
            //$date2->add(new \DateInterval('P5Y'));


            $tokenExpiry = $date->format('Y-m-d H:i:s');
            $refreshExpiry = $date2->format('Y-m-d H:i:s');

            $payload = ['token_time' => $tokenExpiry, 'email'=>$email , 'name'=>$fullName, 'user_id'=> $user_id, 'ip_address' => $remote->setUseProxy()->getIpAddress()];
            $this->apiResponse['token'] = $this->generateJwtToken($payload);


            $refresh_token = ['number' => rand(1111111111,999999999), 'user_id' => $user_id];
            $this->apiResponse['refresh_token'] = $this->generateJwtToken($refresh_token);

            $data['ipAddress'] = $remote->setUseProxy()->getIpAddress();
            $data['email'] = $email;
            $data['accessToken'] = $this->apiResponse['token'];
            $data['refreshToken'] = $this->apiResponse['refresh_token'];
            $data['refreshTokenExpiry'] = $refreshExpiry;
            $data['tokenExpiry'] = $tokenExpiry;
            /*$data['user'] = $user;*/

            if($apiToken = $this->apiTokenManager->createToken($data,$user)) {

                $this->httpStatusCode = 200;
                // Set the response
                /*$this->apiResponse['data'] = $data;*/
                $this->apiResponse['action'] = 'OK';
                $this->apiResponse['message'] = "Success";
                // return $this->createResponse();
                $response = ['access_token' => $this->apiResponse['token']
                    , 'refresh_token' => $this->apiResponse['refresh_token']
                    , 'action' => 'OK'
                    , 'message'=> 'Success'
                    , 'expires_in'=> 1800
                ];
                echo json_encode($response);
                exit;

            }
            else
            {
                $this->httpStatusCode = 401;
                // Set the response
                $this->apiResponse['token'] = "";
                $this->apiResponse['refresh_token'] = "";
                $this->apiResponse['action'] = 'NOK';
                $this->apiResponse['message'] = "Error while token creation, Please try later";
                // return $this->createResponse();
                $response = ['access_token' =>'', 'refresh_token' => '', 'action' => '', 'message'=>'Error while token creation, Please try later'];
                echo json_encode($response);
                exit;
            }
        }
        else
        {
            $this->httpStatusCode = 401;
            $this->apiResponse['token'] = "";
            $this->apiResponse['refresh_token'] = "";
            // Set the response
            $this->apiResponse['action'] = 'NOK';
            $this->apiResponse['message'] = "Invalid SSO server client or secrete key.";
            // return $this->createResponse();
            $response = ['access_token' =>'0', 'refresh_token' => '0', 'action' => '0', 'message'=>'Incorrect API user or password'];
            echo json_encode($response);
            exit;
        }

    }

    public function getTokenAdminAction()
    {

        //POST : http://localhost/sso_zend/public/index.php/api/apitoken/getTokenAdmin
        $email = $this->params()->fromPost('email','');
        $password = $this->params()->fromPost('password','');
        $authAdapter = $this->authService->getAdapter();
        $authAdapter->setEmail($email);
        $authAdapter->setPassword($password);
        $result = $authAdapter->authenticate();

        if ($result->isValid()){
            $user = $this->entityManager->getRepository(User::class)
                ->findOneByEmail($email);

            if(empty($user))
            {
                $this->httpStatusCode = 200;
                // Set the response
                $this->apiResponse['token'] = "";
                $this->apiResponse['refresh_token'] = "";
                $this->apiResponse['action'] = 'NOK';
                $this->apiResponse['message'] = "User not currently available on SSO Server";
                return $this->createResponse();

            }

            $fullName = $user->getFullName();
            $user_id = $user->getId();


            $remote = new RemoteAddress;

            $date = new \DateTime(date('Y-m-d H:i:s'));
            //$date->add(new \DateInterval('PT30M'));
            $date->add(new \DateInterval('P50Y'));
            //$date->add(new \DateInterval(API_TOKEN_EXPIRE_INTERVAL));


            $date2 = new \DateTime(date('Y-m-d H:i:s'));
            $date2->add(new \DateInterval('P50Y'));
            //$date2->add(new \DateInterval(API_REFRESH_TOKEN_EXPIRE_INTERVAL));
            //$date2->add(new \DateInterval('P5Y'));


            $tokenExpiry = $date->format('Y-m-d H:i:s');
            $refreshExpiry = $date2->format('Y-m-d H:i:s');

            $payload = ['token_time' => $tokenExpiry, 'email'=>$email , 'name'=>$fullName, 'user_id'=> $user_id, 'ip_address' => $remote->setUseProxy()->getIpAddress()];
            $this->apiResponse['token'] = $this->generateJwtToken($payload);


            $refresh_token = ['number' => rand(1111111111,999999999), 'user_id' => $user_id];
            $this->apiResponse['refresh_token'] = $this->generateJwtToken($refresh_token);

            $data['ipAddress'] = $remote->setUseProxy()->getIpAddress();
            $data['email'] = $email;
            $data['accessToken'] = $this->apiResponse['token'];
            $data['refreshToken'] = $this->apiResponse['refresh_token'];
            $data['refreshTokenExpiry'] = $refreshExpiry;
            $data['tokenExpiry'] = $tokenExpiry;
            /*$data['user'] = $user;*/

            if($apiToken = $this->apiTokenManager->createToken($data,$user)) {
                $this->httpStatusCode = 200;
                // Set the response
                $this->apiResponse['data'] = $data;
                $this->apiResponse['action'] = 'OK';
                $this->apiResponse['message'] = "Success";
                return $this->createResponse();

            }
            else
            {
                $this->httpStatusCode = 401;
                // Set the response
                $this->apiResponse['token'] = "";
                $this->apiResponse['refresh_token'] = "";
                $this->apiResponse['action'] = 'NOK';
                $this->apiResponse['message'] = "Error while token creation, Please try later";
                return $this->createResponse();
            }
        }
        else
        {
            $this->httpStatusCode = 401;
            $this->apiResponse['token'] = "";
            $this->apiResponse['refresh_token'] = "";
            // Set the response
            $this->apiResponse['action'] = 'NOK';
            $this->apiResponse['message'] = "Invalid SSO server client or secrete key.";
            return $this->createResponse();
        }

    }

    public function refreshTokenAction()
    {
        //call : http://localhost/clinical-system/public/index.php/api/apitoken/getToken/refreshToken
        $ref = $this->params()->fromHeader();
        $refreshToken = (isset($ref['Refreshtoken']) ? $ref['Refreshtoken'] : "-1");

        if($refreshToken ==  "-1")
        {
            $this->httpStatusCode = 401;

            // Set the response
            $this->apiResponse['action'] = "Invalid Refresh Token.";
            return $this->createResponse();

        }

        $token = $this->entityManager->getRepository(ApiAccessToken::class)
            ->findOneByRefreshToken([$refreshToken], ['tokenExpiry' => 'DESC']);

        if(empty($token))
        {
            $this->httpStatusCode = 401;
            // Set the response
            $this->apiResponse['action'] = "Invalid Refresh Token.";
            return $this->createResponse();
        }

        $dt = date('Y-m-d H:i:s');
        if ($dt < $token->getRefreshTokenExpiry())
        {
            //update access token
            $date = new \DateTime(date('Y-m-d H:i:s'));
            // 30 minutes interval
            //$date->add(new \DateInterval('PT30M'));
            $date->add(new \DateInterval(API_TOKEN_EXPIRE_INTERVAL));
            $tokenExpiry = $date->format('Y-m-d H:i:s');
            $remote = new RemoteAddress;

            $payload = ['token_time' => $tokenExpiry, 'email'=>$token->getUser()->getEmail() , 'name'=>$token->getUser()->getFullName(),
                'user_id'=> $token->getUser()->getId(),'ip_address' => $remote->setUseProxy()->getIpAddress()];
            $this->apiResponse['token'] = $this->generateJwtToken($payload);
            $data['accessToken'] = $this->apiResponse['token'];
            $data['tokenExpiry'] = $tokenExpiry;

            if($apiToken = $this->apiTokenManager->refreshToken($token,$data))
            {
                $this->apiResponse['message'] = 'Token Refreshed';
                $this->apiResponse['refresh_token'] = $token->getRefreshToken();
                $this->apiResponse['action'] = "OK";

                return $this->createResponse();
            }
            else
            {
                $this->httpStatusCode = 401;

                // Set the response
                $this->apiResponse['action'] = "Invalid Refresh Token.";
                return $this->createResponse();
            }
        }
        else
        {
            $this->httpStatusCode = 401;
            // Set the response
            $this->apiResponse['action'] = "Refresh Token Expired.";
            return $this->createResponse();
        }
    }

    public function signupAction()
    {
        //POST : http://localhost/sso_zend/public/index.php/api/apitoken/signup

        $ssoData = $this->params()->fromPost();
        $alreadyUser = $this->entityManager->getRepository(Usersso::class)
            ->findOneByEmail($ssoData['ssoEmail']);

        if(!empty($alreadyUser))
        {
            $this->httpStatusCode = 200;
            // Set the response
            $this->apiResponse['action'] = 'NOK';
            $this->apiResponse['message'] = "User Already Exists";
            return $this->createResponse();
        }

        $email = $this->params()->fromPost('email','');
        $password = $this->params()->fromPost('password','');
        $authAdapter = $this->authService->getAdapter();
        $authAdapter->setEmail($email);
        $authAdapter->setPassword($password);
        $result = $authAdapter->authenticate();

        if ($result->isValid()){
            $user = $this->entityManager->getRepository(User::class)
                ->findOneByEmail($email);
            $fullName = $user->getFullName();
            $user_id = $user->getId();


            $remote = new RemoteAddress;

            $date = new \DateTime(date('Y-m-d H:i:s'));
            //$date->add(new \DateInterval('PT30M'));
            $date->add(new \DateInterval(API_TOKEN_EXPIRE_INTERVAL));


            $date2 = new \DateTime(date('Y-m-d H:i:s'));
            //$date2->add(new \DateInterval('PT24H'));
            $date2->add(new \DateInterval(API_REFRESH_TOKEN_EXPIRE_INTERVAL));
            //$date2->add(new \DateInterval('PT1S'));

            $tokenExpiry = $date->format('Y-m-d H:i:s');
            $refreshExpiry = $date2->format('Y-m-d H:i:s');

            $payload = ['token_time' => $tokenExpiry, 'email'=>$email , 'name'=>$fullName, 'user_id'=> $user_id, 'ip_address' => $remote->setUseProxy()->getIpAddress()];
            $this->apiResponse['token'] = $this->generateJwtToken($payload);


            $refresh_token = ['number' => rand(1111111111,999999999), 'user_id' => $user_id];
            $this->apiResponse['refresh_token'] = $this->generateJwtToken($refresh_token);

            $data['ipAddress'] = $remote->setUseProxy()->getIpAddress();
            $data['email'] = $email;
            $data['accessToken'] = $this->apiResponse['token'];
            $data['refreshToken'] = $this->apiResponse['refresh_token'];
            $data['refreshTokenExpiry'] = $refreshExpiry;
            $data['tokenExpiry'] = $tokenExpiry;
            /*$data['user'] = $user;*/

            if($apiToken = $this->apiTokenManager->createToken($data,$user))
            {
                $ssoData['sso_id'] = $this->getUniqueSSoId();
                $ssoData['role'] = $this->patientRole;
                if($ssoData['role'] == 'practitioner')
                    $ssoData['role'] = $this->practitionerRole;


                $bcrypt = new Bcrypt();
                $passwordHash = $bcrypt->create($ssoData['ssoPassword']);
                $ssoData['ssoPassword'] = $passwordHash;


                if ($newSSO = $this->apiSsoUserManager->addUserSso($ssoData)){

                    //activity log
                    $logData['module'] = "SSO USER";
                    $logData['module_id'] = $newSSO->getId();
                    $logData['user_id'] = $user->getId();
                    $logData['email'] = $email;

                    $this->activitylog()->writeActivity($logData);

                    $this->httpStatusCode = 200;
                    // Set the response
                    $this->apiResponse['data'] = $data;
                    $this->apiResponse['action'] = 'OK';
                    $this->apiResponse['message'] = "Added new SSO User.";
                    return $this->createResponse();
                }
                else
                {
                    $this->httpStatusCode = 200;
                    // Set the response
                    //$this->apiResponse['data'] = $data;
                    $this->apiResponse['action'] = 'NOK';
                    $this->apiResponse['message'] = $newSSO;
                    return $this->createResponse();
                }
            }
            else
            {
                $this->httpStatusCode = 200;
                // Set the response
                $this->apiResponse['data'] = '';
                $this->apiResponse['action'] = 'NOK';
                $this->apiResponse['message'] = "Error while token creation, Please try again";
                return $this->createResponse();
            }
        }
        else {
            $this->httpStatusCode = 200;
            // Set the response
            $this->apiResponse['data'] = '';
            $this->apiResponse['action'] = 'NOK';
            $this->apiResponse['message'] = "Incorrect SSO Server email or key";
            return $this->createResponse();

        }
    }

    /**
     * Create Response for api Assign require data for response and check is valid response or give error
     * @return \Zend\View\Model\JsonModel
     *
     */
    public function createResponse()
    {
        $config = $this->getEvent()->getParam('config', false);
        $event = $this->getEvent();
        $response = $event->getResponse();

        if (is_array($this->apiResponse)) {
            $response->setStatusCode($this->httpStatusCode);
        } else {
            $this->httpStatusCode = 500;
            $response->setStatusCode($this->httpStatusCode);
            $errorKey = $config['ApiRequest']['responseFormat']['errorKey'];
            $defaultErrorText = $config['ApiRequest']['responseFormat']['defaultErrorText'];
            $this->apiResponse[$errorKey] = $defaultErrorText;
        }
        $statusKey = $config['ApiRequest']['responseFormat']['statusKey'];
        if ($this->httpStatusCode == 200) {
            $sendResponse[$statusKey] = $config['ApiRequest']['responseFormat']['statusOkText'];
        } else {
            $sendResponse[$statusKey] = $config['ApiRequest']['responseFormat']['statusNokText'];
        }
        $sendResponse[$config['ApiRequest']['responseFormat']['resultKey']] = $this->apiResponse;
        return new JsonModel($sendResponse);
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
                $ssoData['sso_id']++;
            }
        }
        return $ssoData['sso_id'];
    }
}
