<?php


namespace Api\Service;

use Common\Entity\Otps;

use \Datetime;
use \DateInterval;

#use Laminas\Math\Rand;

/**
 * This service is responsible for adding/editing categorys
 * and changing category password.
 */
class OtpsManager
{

    /**
     * Doctrine entity manager.
     * @var Doctrine\ORM\EntityManager
     */
    private $entityManager;

    private $actionAdd = "Add";
    private $actionEdit = "Edit";
    private $actionDelete = "Delete";
    private $stateError = "Error";
    private $stateSuccess = "Success";
    private $otpExpiryMobile = "";

    /**
     * Constructs the service.
     */
    public function __construct($entityManager)
    {
        $this->entityManager = $entityManager;

        $this->otpExpiryMobile = date("Y-m-d H:i:s",strtotime(date("Y-m-d H:i:s")." +3 minute"));
    }

    /** Author : Mohsin Hassan
     * Parameter : Mix Array
     * This method adds a new OneTimePassword.
     * Returns : Object
     */
    public function add(array $data = [] ) 
    {
        $otps = new Otps();
        $otps->setUsername($data['username']);
        $otps->setIpAddress($data['ipAddress']);
        
        $rnd_num = rand(99999,999999);
        $rnd_num = substr($rnd_num , 0, 6);

        $otps->setOtp($rnd_num);
        
        $currentDate = date('Y-m-d H:i:s');
        $minutes_to_add = 1;

        $otpExpiry = date("Y-m-d H:i:s",strtotime(date("Y-m-d H:i:s")." +1 minute"));
    
        $otps->setOtpCreated($currentDate);
        $otps->setOtpExpiry($otpExpiry);
        $otps->setStatus('1');
        
        $this->entityManager->persist($otps);
        $this->entityManager->flush();
        return $otps;

    }

    /** Author : Mohsin Hassan
     * Parameter : Mix Array
     * This method adds a new OneTimePassword for mobile verification.
     * Returns : Object
     */
    public function addMobileVerification(array $data = [] ) 
    {
        $otps = new Otps();
        $otps->setUsername($data['username']);
        $otps->setIpAddress($data['ipAddress']);
        
        $rnd_num = rand(99999,999999);
        $rnd_num = substr($rnd_num , 0, 6);

        $otps->setOtp($rnd_num);
        
        $currentDate = date('Y-m-d H:i:s');

        $otps->setOtpCreated($currentDate);
        $otps->setOtpExpiry($this->otpExpiryMobile);
        $otps->setMobileNumber($data['mobile_number']);
        $otps->setOtpType(2); // mobile verification
        $otps->setStatus('1');
        
        $this->entityManager->persist($otps);
        $this->entityManager->flush();
        return $otps;

    }

    /** Author : Mohsin Hassan
     * Parameter : smsSent 0/1 , otps entity 
     * This method set sms sent bit.
     * Returns : Object
     */


    public function setSmsSent($smsSent,$otps)
    {
        $otps->setSmsSent($smsSent);

        //adding in database
        $this->entityManager->persist($otps);

        // Apply changes to database.
        $this->entityManager->flush();

        return $otps;
    }
}
