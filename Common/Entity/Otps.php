<?php
namespace Common\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * This class represents a registered Otps.
 * @ORM\Entity(repositoryClass="\Common\Repository\OtpsRepository")
 * @ORM\Table(name="otps")
 */

class Otps
{
    
    /**
     * @ORM\Id
     * @ORM\Column(name="id")
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @var string|null
     *
     * @ORM\Column(name="ip_address")
     */
    private $ipAddress;

    /**
     * @var string|null
     *
     * @ORM\Column(name="username")
     */
    private $username;

    /**
     * @ORM\Column(name="otp")
     */
    private $otp;

    /**
     * @ORM\Column(name="otp_created")
     */
    private $otpCreated;

    /**
     * @ORM\Column(name="otp_expiry")
     */
    private $otpExpiry;

    /**
     * @ORM\Column(name="status")
     */
    private $status;
    
    
    /**
     * @ORM\Column(name="sms_sent")
     */
    private $smsSent;
    
    /**
     * @ORM\Column(name="otp_type")
     */
    private $otpType;
    /**
     * @ORM\Column(name="mobile_number")
     */
    private $mobileNumber;
    
    public function getId()
    {
        return $this->id;
    }
    public function setId($id)
    {
        $this->id = $id;
    }
    
    public function getIpAddress()
    {
        return $this->ipAddress;
    }
    public function setIpAddress($ipAddress)
    {
        $this->ipAddress = $ipAddress;
    }
    public function getUsername()
    {
        return $this->username;
    }
    public function setUsername($Username)
    {
        $this->username = $Username;
    }

    /**
     * Returns the date of category creation.
     * @return string
     */
    public function getOtp()
    {
        return $this->otp;
    }

    /**
     * Sets the date when this category was created.
     * @param string $otp
     */
    public function setOtp($otp)
    {
        $this->otp = $otp;
    }

    /**
     * Sets the date when this category was created.
     * @param string $tokenExpiry
     */

    public function setOtpCreated($otpCreated)
    {
        $this->otpCreated = $otpCreated;
    }


    /**
     * Returns the date of category creation.
     * @return string
     */
    public function getOtpCreated()
    {
        return $this->otpCreated;
    }

    /**
     * Sets the date when this category was created.
     * @param string $tokenExpiry
     */

    public function setOtpExpiry($tokenExpiry)
    {
        $this->otpExpiry = $tokenExpiry;
    }

    /**
     * Returns the date of category creation.
     * @return string
     */
    public function getOtpExpiry()
    {
        return $this->otpExpiry;
    }
    public function setStatus($status)
    {
        $this->status = $status;
    }
    /**
     * Returns the date of category creation.
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Returns the date of category creation.
     * @return string
     */
    public function getSmsSent()
    {
        return $this->smsSent;
    }

    /**
     * Returns the date of category creation.
     * @return string
     */
    public function setSmsSent($smsSent)
    {
        $this->smsSent = $smsSent;
    }

    public function getOtpType()
    {
        return $this->otpType;
    }
    public function setOtpType($otpType)
    {
        $this->otpType = $otpType;
    }
    public function getMobileNumber()
    {
        return $this->mobileNumber;
    }
    public function setMobileNumber($mobileNumber)
    {
        $this->mobileNumber = $mobileNumber;
    }
}