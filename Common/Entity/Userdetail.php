<?php
namespace Common\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * This class represents a registered user.
 * @ORM\Entity(repositoryClass="\Common\Repository\UserDetailRepository")
 * @ORM\Table(name="user_detail")
 */
class Userdetail
{
    /**
     * One number entry belongs to One person
     * @ORM\OneToOne(targetEntity="\Common\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

    /**
     * Returns ID of person for this vat id number.
     * @param person
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Adds a new person matching this number
     * @param
     */
    public function setUser($id)
    {
        $this->user = $id;
    }

    /**
     * @ORM\Id
     * @ORM\Column(name="id")
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @ORM\Column(name="gender")
     */
    protected $gender;

    /**
     * @ORM\Column(name="address")
     */
    protected $address;

    /**
     * @ORM\Column(name="dob")
     */
    protected $dob;

    /**
     * @ORM\Column(name="mobile")
     */
    protected $mobile;

    /**
     * @ORM\Column(name="post_code")
     */
    protected $postCode;

    /**
     * @ORM\Column(name="state")
     */
    protected $state;

    /**
     * @ORM\Column(name="suburb")
     */
    protected $suburb;

    /**
     * @ORM\Column(name="surname")
     */
    protected $surname;

    /**
     * @ORM\Column(name="date_created")
     */
    protected $dateCreated;

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

    /**
     * Returns gender.
     * @return string
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * Sets gender.
     * @param int $gender
     */

    public function setGender($gender)
    {
        $this->gender = $gender;
    }

    /**
     * Returns email.
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Sets email.
     * @param string $email
     */
    public function setAddress($address)
    {
        $this->address = $address;
    }

    /**
     * Returns full name.
     * @return string
     */
    public function getDob()
    {
        return $this->dob;
    }

    public function setDob($dob)
    {
        $this->dob = $dob;
    }
    public function getMobile()
    {
        return $this->mobile;
    }

    public function setMobile($mobile)
    {
        $this->mobile = $mobile;
    }
    public function setPostCode($postCode)
    {
        $this->postCode = $postCode;
    }

    public function getPostCode()
    {
        return $this->postCode;
    }

    public function setState($state)
    {
        $this->state = $state;
    }

    public function getState()
    {
        return $this->state;
    }

    public function setSuburb($suburb)
    {
        $this->suburb = $suburb;
    }
    /**
     * Returns status.
     * @return int
     */
    public function getSuburb()
    {
        return $this->suburb;
    }

    public function setSurname($surname)
    {
        $this->surname = $surname;
    }
    /**
     * Returns status.
     * @return int
     */
    public function getSurname()
    {
        return $this->surname;
    }
    /**
     * Sets the date when this user was created.
     * @param string $dateCreated
     */
    public function setDateCreated($dateCreated)
    {
        $this->dateCreated = $dateCreated;
    }

    public function getDateCreated()
    {
        return $this->dateCreated;
    }
}