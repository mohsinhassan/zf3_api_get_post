<?php
namespace Common\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * This class represents a registered user.
 * @ORM\Entity(repositoryClass="\Common\Repository\UserSsoRepository")
 * @ORM\Table(name="user_sso")
 */
class Usersso
{
    // User status constants.
    const STATUS_ACTIVE = 1; // Active user.
    const STATUS_RETIRED = 0; // Retired user.

    /**
     * @ORM\OneToMany(targetEntity="\Common\Entity\Userssometa", mappedBy="usersso", cascade={"persist"})
     * @ORM\JoinColumn(name="id", referencedColumnName="user_sso_id")
     */
    protected $userssometa;


    /**
     * @ORM\Id
     * @ORM\Column(name="id")
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @ORM\Column(name="email")
     */
    protected $email;

    /**
     * @ORM\Column(name="username")
     */
    protected $userName;

    /**
     * @ORM\Column(name="password")
     */
    protected $password;

    /**
     * @ORM\Column(name="status")
     */
    protected $status;
    /**
     * @ORM\Column(name="dob")
     */
    protected $dob;    
    /**
     * @ORM\Column(name="first_name")
     */
    protected $fname;
    /**
     * @ORM\Column(name="last_name")
     */
    protected $lname;  
    /**
     * @ORM\Column(name="gender")
     */
    protected $gender;  
    /**
     * @ORM\Column(name="mobile_number")
     */
    protected $mobileNumber;
    /**
     * @ORM\Column(name="role")
     */
    protected $role;

    /**
     * @ORM\Column(name="address_user")
     */
    protected $addressUser; 
    /**
     * @ORM\Column(name="suburb")
     */
    protected $suburb;  
    /**
     * @ORM\Column(name="state_user")
     */
    protected $stateUser;  
    /**
     * @ORM\Column(name="postcode")
     */
    protected $postcode; 
    /**
     * @ORM\Column(name="sso_id")
     */
    protected $ssoId;    
    /**
     * @ORM\Column(name="date_created")
     */
    protected $dateCreated;
    /**
     * @ORM\Column(name="password_reset_token")
     */

    protected $passwordResetToken;

    /**
     * @ORM\Column(name="password_reset_expiry")
     */
    protected $passwordResetExpiry;
    
    /**
     * Returns user ID.
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Sets user ID.
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    public function setPasswordResetToken($token)
    {
        $this->passwordResetToken = $token;
    }

    public function getPasswordResetToken()
    {
        return $this->passwordResetToken;
    }

    public function setPasswordResetExpiry($expiry)
    {
        $this->passwordResetExpiry = $expiry;
    }

    public function getPasswordResetExpiry()
    {
        return $this->passwordResetExpiry;
    }


    /**
     * Returns email.
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Sets email.
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * Returns full name.
     * @return string
     */
    public function getUserName()
    {
        return $this->userName;
    }

    /**
     * Sets full name.
     * @param string $fullName
     */
    public function setUserName($fullName)
    {
        $this->userName = $fullName;
    }

    /**
     * Returns status.
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Returns possible statuses as array.
     * @return array
     */
    public static function getStatusList()
    {
        return [
            self::STATUS_ACTIVE => '1',
            self::STATUS_RETIRED => '0'
        ];
    }

    /**
     * Returns user status as string.
     * @return string
     */
    public function getStatusAsString()
    {
        $list = self::getStatusList();
        if (isset($list[$this->status]))
            return $list[$this->status];

        return 'Unknown';
    }

    /**
     * Sets status.
     * @param int $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * Sets status.
     * @param int $role
     */
    public function setRole($role)
    {
        $this->role = $role;
    }

    /**
     * Returns role.
     * @return string
     */
    public function getRole()
    {
        return $this->role;
    }


    /**
     * Returns password.
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Sets password.
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }
    /**
     * Returns password.
     * @return string
     */
    public function getFirstName()
    {
        return $this->fname;
    }

    /**
     * Sets password.
     * @param string $password
     */
    public function setFirstName($password)
    {
        $this->fname = $password;
    }
    /**
     * Returns password.
     * @return string
     */
    public function getLastName()
    {
        return $this->lname;
    }

    /**
     * Sets password.
     * @param string $password
     */
    public function setLastName($password)
    {
        $this->lname = $password;
    }    
    /**
     * Returns password.
     * @return string
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * Sets password.
     * @param string $password
     */
    public function setGender($password)
    {
        $this->gender = $password;
    }   
    /**
     * Returns password.
     * @return string
     */
    public function getMobile()
    {
        return $this->mobileNumber;
    }
    /**
     * Sets password.
     * @param string $password
     */
    public function setDob($dob)
    {
        $this->dob = $dob;
    } 
    /**
     * Returns password.
     * @return string
     */
    public function getDob()
    {
        return $this->dob;
    }
    /**
     * Sets password.
     * @param string $password
     */
    public function setMobile($password)
    {
        $this->mobileNumber = $password;
    } 
    /**
     * Returns password.
     * @return string
     */
    public function getAddress()
    {
        return $this->addressUser;
    }

    /**
     * Sets password.
     * @param string $password
     */
    public function setAddress($password)
    {
        $this->addressUser = $password;
    }
    /**
     * Returns password.
     * @return string
     */
    public function getSuburb()
    {
        return $this->suburb;
    }

    /**
     * Sets password.
     * @param string $password
     */
    public function setSuburb($password)
    {
        $this->suburb = $password;
    } 
    /**
     * Returns password.
     * @return string
     */
    public function getPostcode()
    {
        return $this->postcode;
    }

    /**
     * Sets password.
     * @param string $password
     */
    public function setPostcode($password)
    {
        $this->postcode = $password;
    }     
    /**
     * Returns password.
     * @return string
     */
    public function getState()
    {
        return $this->stateUser;
    }

    /**
     * Sets password.
     * @param string $password
     */
    public function setState($password)
    {
        $this->stateUser = $password;
    }    
    /**
     * Returns password.
     * @return string
     */
    public function getSsoid()
    {
        return $this->ssoId;
    }

    /**
     * Sets password.
     * @param string $password
     */
    public function setSsoid($password)
    {
        $this->ssoId = $password;
    }       
    /**
     * Returns the date of user creation.
     * @return string
     */
    public function getDateCreated()
    {
        return $this->dateCreated;
    }

    /**
     * Sets the date when this user was created.
     * @param string $dateCreated
     */
    public function setDateCreated($dateCreated)
    {
        $this->dateCreated = $dateCreated;
    }
    /**
     * Returns invoice orders for this invoice.
     * @return array
     */
    public function getUserSsoMeta() {
        return $this->userssometa;
    }

    public function addUserSsoMeta($variables) {
        $this->userssometa[] = $variables;
    }

    public function allPermissionTypes()
    {
        return [
            'admin_access' => 'admin_access',
            'medical_carer_access' => 'medical_carer_access',
            'patient_history_access' => 'patient_history_access',
            'prescription_access' => 'prescription_access',
            'medication_access' => 'medication_access',
            'monitor_repeats_access' => 'monitor_repeats_access',
            'tracking_api_access' => 'tracking_api_access',
            'other_consult_access' => 'other_consult_access',
            'view_billing_dashboard' => 'view_billing_dashboard',
            'referrals_access' => 'referrals_access',
        ];
    }
}