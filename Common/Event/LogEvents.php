<?php
namespace Common\Event;
//aggregate dependancy
use Zend\EventManager\EventInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\EventManager\ListenerAggregateTrait;
use Zend\Log\Logger;

use Zend\Http\Client;
use Zend\Http\Request;
use Zend\Http\Header;

class LogEvents implements ListenerAggregateInterface
{
    use ListenerAggregateTrait;

    private $log;

    public function __construct(Logger $log)
    {
        $this->log = $log;
    }

    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $this->listeners[] = $events->attach('updateUserSSOInfo', [$this, 'updateQoctor']);
        $this->listeners[] = $events->attach('updateUserMetaSSOInfo', [$this, 'updateQoctorUserMeta']);
      
    }

    public function updateQoctor(EventInterface $e)
    {
        $event  = $e->getName();
        $data = $e->getParams();
        
        $settings = $data['settings'];
        $data = $data['data'];

        
        $postArray = array();
        if(!empty($data['firstname']))   $postArray['FirstName']  = $data['firstname'];
        if(!empty($data['surname'])) $postArray['LastName'] = $data['surname'];
        if(!empty($data['dob']))  $postArray['Dob']  = $data['dob'];
        if(!empty($data['mobile']))      $postArray['Mobile']     = $data['mobile'];
        if(!empty($data['address']))   $postArray['Address']  = $data['address'];
        if(!empty($data['suburb']))   $postArray['Suburb']  = $data['suburb'];
        if(!empty($data['state'])) $postArray['State'] = $data['state'];
        if(!empty($data['postcode']))  $postArray['Postcode']  = $data['postcode'];
        if(!empty($data['gender']))   $postArray['Gender']  = $data['gender'];
        if(!empty($data['username']))   $postArray['UserName']  = $data['username'];
        if(!empty($data['ssoEmail']))   $postArray['email']  = $data['ssoEmail'];
        
        $postArray['consumer_key'] = $settings['qoctor_key'];
        $postArray['consumer_secret'] = $settings['qoctor_secret'];

        $client = new Client();
        $client->setUri($settings['qoctor_update_user_api']);

        $client->setOptions(array(
            'maxredirects' => 0,
            'timeout' => 3000000,
            'sslverifypeer'=>false
            
        ));
        $client->setMethod('POST');

        $client->setHeaders(array(
            'Host: '.$settings['host'],
            'Accept-encoding: deflate',
            'X-Powered-By: Zend Framework')
        );
        $client->setParameterPost($postArray);

        $response = $client->send();
//        echo '<pre>';
//        print_r($response->getBody());exit;
        
        
        mail('fmangat@itdelivery.com.au', 'test', ($response->getBody()));
        return 1;
       
    }

    public function updateQoctorUserMeta(EventInterface $e)
    {
        $event  = $e->getName();
        $data = $e->getParams();
        
        $settings = $data['settings'];
        $data = $data['data'];

        
        $postArray = array();
    

        $postArray = $data;
        
        $postArray['consumer_key'] = $settings['qoctor_key'];
        $postArray['consumer_secret'] = $settings['qoctor_secret'];

        $client = new Client();
        $client->setUri($settings['qoctor_update_user_meta_api']);

        $client->setOptions(array(
            'maxredirects' => 0,
            'timeout' => 3000000,
            'sslverifypeer'=>false
            
        ));
        $client->setMethod('POST');
        $client->setHeaders(array(
            'Host: '.$settings['host'],
            'Accept-encoding: deflate',
            'X-Powered-By: Zend Framework')
        );
        $client->setParameterPost($postArray);              
        $response = $client->send();
        return 1;
       
    }


}
