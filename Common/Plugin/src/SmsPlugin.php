<?php
namespace CommonPlugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\Http\Client;
use Zend\Http\Request;
use Laminas\Http\Header;
/**
 * This controller plugin is designed to let you get the currently logged in User entity
 * inside your controller.
 */


class SmsPlugin extends AbstractPlugin
{

    private $entityManager;
    private $settingsManager;
    private $settings;
    private $sendSmsUrl;

    public function __construct($entityManager, $settingsManager)
    {
        $this->entityManager = $entityManager;
        $this->settingsManager = $settingsManager;
        $this->settings = $this->settingsManager->getSettingsByMeta('send_sms_by_mobile');
        $this->sendSmsUrl = $this->settings->getValue();

    }

    public function sendSmsByMobile($data)
    {
        //$url = "https://cerebrum.qoctor.com.au/api/smsapi/custom-bulk-sms-by-phone";
        //$url = "http://localhost/cerebrum_bitbucket/api/smsapi/custom-bulk-sms-by-phone";
        $url = $this->sendSmsUrl;
        $client = new Client();

        $client->setUri($url);
        $client->setOptions(array(
            'maxredirects' => 0,
            'timeout' => 3000000,
            'sslverifyhost' => false,
            'sslverifypeer'=> false
        ));

        $client->setMethod(Request::METHOD_POST);

        $client->setParameterPost(array(
            'data' => array($data))
        );
        $response = $client->send();
        $result = json_decode(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $response->getBody()), true);
        return $result;
    }

    public function sendSms($data)
    {
        //$url = "https://cerebrum.qoctor.com.au/api/smsapi/customSms";
        $url = $this->sendSmsUrl;
        $client = new Client();

        $client->setUri($url);
        $client->setOptions(array(
            'maxredirects' => 0,
            'timeout' => 3000000,
            'sslverifyhost' => false,
            'sslverifypeer'=> false
        ));

        $client->setMethod(Request::METHOD_POST);

        $client->setParameterPost(array(
            'message' => $data['message'],
            'username' => $data['username']
        ));

        $response = $client->send();
        //$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        //echo "<pre>";print_r($response->getBody());exit;
        $result = json_decode(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $response->getBody()), true);
        return $result;
    }

    public function faisal_crypt( $string, $action = 'e' ) {
        // you may change these values to your own
        $secret_key = 'faisalYes$';
        $secret_iv = 'faisalNo$';

        $output = false;
        $encrypt_method = "AES-256-CBC";
        $key = hash( 'sha256', $secret_key );
        $iv =  hash( 'sha256', $secret_iv );

        if( $action == 'e' ) {
            $output = base64_encode( openssl_encrypt( $string, $encrypt_method, $key, 0, $iv ) );
        }
        else if( $action == 'd' ){
            $output = openssl_decrypt( base64_decode( $string ), $encrypt_method, $key, 0, $iv );
        }

        return $output;
    }
}
