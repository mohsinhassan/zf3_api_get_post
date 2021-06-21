<?php

namespace Common\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * This class represents a registered invoice.
 * @ORM\Entity(repositoryClass="\Common\Repository\UserSsoMetaRepository")
 * @ORM\Table(name="user_sso_meta")
 */
class Userssometa {

    /**
     * @ORM\ManyToOne(targetEntity="\Common\Entity\Usersso", inversedBy="userssometa")
     * @ORM\JoinColumn(name="user_sso_id", referencedColumnName="id")
     */
    protected $usersso;

    /**
     * @ORM\Id
     * @ORM\Column(name="id")
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @ORM\Column(name="user_sso_id")  
     */
    protected $userssoId;

    /**
     * @ORM\Column(name="meta_key")  
     */
    protected $key;

    /**
     * @ORM\Column(name="meta_value")  
     */
    protected $value;

    /**
     * Returns invoice ID.
     * @return integer
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Sets invoice ID. 
     * @param int $id    
     */
    public function setId($id) {
        $this->id = $id;
    }

    /**
     * Returns title.     
     * @return string
     */
    public function getUserssoId() {
        return $this->userssoId;
    }

    /**
     * Sets title.     
     * @param string $InvoiceId
     */
    public function setUserssoId($id) {
        $this->userssoId = $id;
    }

    /**
     * Returns full name.
     * @return string     
     */
    public function getKey() {
        return $this->key;
    }

    /**
     * Sets full name.
     * @param string $fullName
     */
    public function setKey($variableName) {
        $this->key = $variableName;
    }

    /**
     * Returns status.
     * @return int     
     */
    public function getValue() {
        return $this->value;
    }

    /**
     * Sets status.
     * @param int $status     
     */
    public function setValue($variableValue) {
        $this->value = $variableValue;
    }
    /*
     * Returns associated invoice.
     * @return \Common\Entity\Usersso
     */

    public function getUsersso() {
        return $this->usersso;
    }

    /**
     * Sets associated invoice.
     * @param \Common\Entity\Usersso $sso
     */
    public function setUsersso($sso) {
        $this->usersso = $sso;
        $sso->addUserSsoMeta($this);
    }

}
