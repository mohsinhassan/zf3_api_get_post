<?php
namespace Api\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\Http\Client;
use Zend\Http\Request;
/**
 * This controller plugin is designed to let you get the currently logged in User entity
 * inside your controller.
 */


class CurlPlugin extends AbstractPlugin
{
    private $serverApi = 'http://localhost/server/endpoint_SKBF5S8sDJZO2vfKiAmmxGFItx1VTkTP/api_endpoint.php';
    //private $serverApi = 'https://52.33.15.254/sso-server/endpoint_T2PjxkzmyhxySOYhNHc6GTtRWdxQ8cpc/api_endpoint.php';
    //private $serverApi =   'http://13.55.133.166/sso-server/endpoint_T2PjxkzmyhxySOYhNHc6GTtRWdxQ8cpc/api_endpoint.php';

    private $qoctorOrderApi = 'https://www.qoctor.com.au/wp-json/getordernotes/allnotes';
    private $qoctorOrderApiKey = 'ck_8322fb133a36cd1a10b990198babb51f22b6b287';
    private $qoctorOrderApiSecret = 'cs_7a3cb5774c59bcca84d2c59a9b30edb089df0841';
    private $synchUserUrl = 'https://www.qoctor.com.au/wp-json/getusers/allusers?consumer_key=ck_8322fb133a36cd1a10b990198babb51f22b6b287&consumer_secret=cs_7a3cb5774c59bcca84d2c59a9b30edb089df0841';

    public function getApiUserid($data)
    {
        $request = new Request();
        $request->setUri($this->serverApi);
        $request->setMethod('POST');
        $request->getPost()->set('action', 'signin');
        $request->getPost()->set('email', $data['email']); //"mohsin"
        $request->getPost()->set('password', $data["password"]); // $request->getPost()->set('password', $data["password"]); //

        $client = new Client();
        $response = $client->send($request);

        $result = json_decode($response->getBody(), true);
        return $result;
    }

    public function signupUser($data)
    {
        $request = new Request();
        $request->setUri($this->serverApi);
        $request->setMethod('POST');
        $request->getPost()->set('action', 'signup'); //"mohsin"
        $request->getPost()->set('email', $data['email']); //"mohsin"
        $request->getPost()->set('username', $data['username']); //"mohsin"
        $request->getPost()->set('password', $data["password"]); // $request->getPost()->set('password', $data["password"]); //

        $request->getPost()->set('address', $data["address"]);
        $request->getPost()->set('dob', $data["dob"]);
        $request->getPost()->set('gender', $data["gender"]);
        $request->getPost()->set('mobile', $data["mobile"]);
        $request->getPost()->set('postcode', $data["postcode"]);
        $request->getPost()->set('state', $data["state"]);
        $request->getPost()->set('suburb', $data["suburb"]);
        $request->getPost()->set('surname', $data["surname"]);

        $client = new Client();
        $response = $client->send($request);

        $result = json_decode($response->getBody(), true);
        return $result;
    }

    public function synchOrders($param)
    {
        $client = new Client();
        $client->setUri($this->qoctorOrderApi);
        $client->setOptions(array(
            'maxredirects' => 0,
            'timeout' => 3000000
        ));
        $client->setHeaders(array(
                'Host: www.qoctor.com.au',
                'Accept-encoding: deflate',
                'X-Powered-By: Zend Framework')
        );
        $client->setParameterGet(array(
            'consumer_key' => $this->qoctorOrderApiKey,
            'consumer_secret' => $this->qoctorOrderApiSecret,
            'date_from' => $param['date_from'],
            'date_to' => $param['date_to'],
        ));

        $response = $client->send();
        $orders = json_decode(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $response->getBody()), true);
        return $orders;
    }

    public function synchUsers($data)
    {
        $client = new Client();
        $client->setUri($this->synchUserUrl);
        $client->setOptions(array(
            'maxredirects' => 0,
            'timeout' => 700
        ));
        $client->setHeaders(array(
                'Host: www.qoctor.com.au',
                'Accept-encoding: deflate',
                'X-Powered-By: Zend Framework')
        );
        $client->setParameterGet(array(
            'consumer_key' => 'ck_8322fb133a36cd1a10b990198babb51f22b6b287',
            'consumer_secret' => 'cs_7a3cb5774c59bcca84d2c59a9b30edb089df0841',
            'from' => $data['from'],
            'to' => $data['to'],
        ));

        $response = $client->send();
        return $users = json_decode(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $response->getBody()), true);
    }
}



