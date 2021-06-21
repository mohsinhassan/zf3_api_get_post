<?php
namespace Api\Service;
use Firebase\JWT\JWT;
use Zend\Http\PhpEnvironment\RemoteAddress;
#use Zend\View\Model\JsonModel;
/**
 * This service is responsible for adding/editing users
 * and changing user password.
 */
class ApiValidateTokenManager
{
    /**
     * Doctrine entity manager.
     * @var Doctrine\ORM\EntityManager
     */
    private $entityManager;

    private $tokenPayload;
    public $config;
    public $apiResponse;
    public $httpStatusCode = 200;

    /**
     * Constructs the service.
     */
    public function __construct($entityManager,$config)
    {
        $this->entityManager = $entityManager;
        $this->config = $config;
    }
    
    /**
     * This method adds a new user.
     */
    //,$consulttype,$source,$status
    public function findJwtToken($request)
    {
        $jwtToken = $request->getHeaders("Authorization") ? $request->getHeaders("Authorization")->getFieldValue() : '';
        if ($jwtToken) {
            $jwtToken = trim(trim($jwtToken, "Bearer"), " ");
            return $jwtToken;
        }
        if ($request->isGet()) {
            $jwtToken = $request->getQuery('token');
        }
        if ($request->isPost()) {
            $jwtToken = $request->getPost('token');
        }
        return $jwtToken;
    }

    public function isValidToken($event)
    {
        $token = $this->decodeToken($event);

        if(!is_object($token)){
            $this->apiResponse['message'] = 'Token is empty';
            //$this->apiResponse['refresh_token'] = $token->getRefreshToken();
            $this->apiResponse['action'] = "NOK";
            $this->apiResponse['code'] = 401;

            echo json_encode($this->apiResponse);
            return false;

        }

        $tokenTime = $token->token_time;
        $remote = new RemoteAddress();
        $ipAddress = $remote->setUseProxy()->getIpAddress();

        if (date('Y-m-d H:i:s') > date($tokenTime)){
            $this->apiResponse['message'] = 'Token Expired';
            $this->apiResponse['action'] = "NOK";
            $this->apiResponse['code'] = 401;

            echo json_encode($this->apiResponse);
            return false;
            //$errorCode = "Token Expired";


        } elseif ($ipAddress != $token->ip_address) {
            $this->apiResponse['message'] = 'Invalid IP Address .';
            $this->apiResponse['action'] = "NOK";
            $this->apiResponse['code'] = 401;

            echo json_encode($this->apiResponse);
            return false;
        }
        else
        {
            $controller = $event->getTarget();
            $controllerName = $event->getRouteMatch()->getParam('controller', null);

            $actionName = $event->getRouteMatch()->getParam('action', null);

            // Convert dash-style action name to camel-case.
            $actionName = str_replace('-', '', lcfirst(ucwords($actionName, '-')));

            // Get the instance of ApiAuthManager service.
            $authManager = $event->getApplication()->getServiceManager()->get(ApiAuthManager::class);
            $result = $authManager->filterAccess($controllerName, $actionName, $token->email);

            if ($result == ApiAuthManager::ACCESS_GRANTED) {
                return true;
            }
            else{
                $this->apiResponse['message'] = 'You are not authorized for this operation. Please contact administrator';
                $this->apiResponse['action'] = "NOK";
                $this->apiResponse['code'] = 401;

                echo json_encode($this->apiResponse);
                return false;
            }
        }
    }


    public function decodeToken($event)
    {
        $request = $event->getRequest();
        $token = $this->findJwtToken($request);
        if (!$token) {
            $this->tokenPayload = false;
        }
        $config = $this->config;
        $cypherKey = $config['ApiRequest']['jwtAuth']['cypherKey'];
        $tokenAlgorithm = $config['ApiRequest']['jwtAuth']['tokenAlgorithm'];
        try {
            $decodeToken = JWT::decode($token, $cypherKey, [$tokenAlgorithm]);
            $this->tokenPayload = $decodeToken;
        } catch (\Exception $e) {
            $this->tokenPayload = $e->getMessage();
        }

        return $this->tokenPayload;
    }

    /**
     * Create Response for api Assign require data for response and check is valid response or give error
     * @return \Zend\View\Model\JsonModel
     *
     */
    /*public function createResponse($response)
    {
        $config = $this->config;


        if (is_array($this->apiResponse)) {
            //$response->setStatusCode($this->httpStatusCode);
        } else {
            $this->httpStatusCode = 500;
            $response->setStatusCode($this->httpStatusCode);
            $errorKey = $config['ApiRequest']['responseFormat']['errorKey'];
            $defaultErrorText = $config['ApiRequest']['responseFormat']['defaultErrorText'];
            $this->apiResponse[$errorKey] = $defaultErrorText;
        }
        if(isset($this->data)){
            $sendResponse['data'] = $this->data;
        }
        $statusKey = $config['ApiRequest']['responseFormat']['statusKey'];
        if ($this->httpStatusCode == 200) {
            $sendResponse[$statusKey] = $config['ApiRequest']['responseFormat']['statusOkText'];
        } else {
            $sendResponse[$statusKey] = $config['ApiRequest']['responseFormat']['statusNokText'];
        }
        $sendResponse[$config['ApiRequest']['responseFormat']['resultKey']] = $this->apiResponse;
        return new JsonModel($sendResponse);
    }*/

}