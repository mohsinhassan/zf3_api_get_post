<?php

namespace CommonPlugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Common\Entity\Otps;
use Common\Repository\OtpsRepository;
use Zend\Http\Client;
use \Datetime;
use \DateInterval;

// Plugin class
class TwoWayOauthPlugin extends AbstractPlugin {

// This method checks whether user is allowed
// to visit the page
    //private $orderManager;
    private $entityManager;
    private $otpsManager;

    public function __construct($entityManager , $otpsManager) {
        $this->entityManager = $entityManager;
        $this->otpsManager = $otpsManager;
    }

    /**
     * Author : Mohsin Hassan
     *
     * @param [array] $data
     * @return Object
     */
    public function generateOtp($data) {
        $this->entityManager->getRepository(Otps::class)->setPreviousOtpsDeactivate($data['username']);

        $otps = $this->otpsManager->add($data);
        return $otps;
    }

    /**
     * Author : Mohsin Hassan
     * Add data for mobile verification
     * @param [array] $data
     * @return Object
     */

    public function generateOtpMobile($data) {
        $this->entityManager->getRepository(Otps::class)->setPreviousOtpsDeactivateMobile($data['username']);

        $otps = $this->otpsManager->addMobileVerification($data);
        return $otps;
    }

    public function verifyOtp($data)
    {
        $otps = $this->entityManager->getRepository(Otps::class)->getUserActiveOtp($data);

       if(!empty($otps)){
            return $otps;
        }
        else{
            return false;
        }
    }

    public function setSmsSent($smsSent,$otp)
    {
        $otps = $this->otpsManager->setSmsSent($smsSent,$otp);
        return $otps;
    }

    //
    public function checkAttemptsLimitExceed($data)
    {
        $otps = $this->entityManager->getRepository(Otps::class)->getUserActiveOtp($data);

        if(!empty($otps)){
            return true;
        }
        else{
            return false;
        }

    }

    //
}
