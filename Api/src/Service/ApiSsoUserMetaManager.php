<?php
namespace Api\Service;
use Common\Entity\Userssometa;

/**
 * This service is responsible for adding/editing users
 * and changing user password.
 */
class ApiSsoUserMetaManager
{
    /**
     * Doctrine entity manager.
     * @var Doctrine\ORM\EntityManager
     */
    private $entityManager;
    public $primary_fileds;
    
    /**
     * This method adds a new user.
     */
    //,$consulttype,$source,$status
    public function __construct($entityManager) {
        $this->entityManager = $entityManager;
        $this->primary_fileds = [
            'sso_id',
            'email',
            'username',
            'password',
            'status',
            'first_name',
            'last_name',
            'gender',
            'mobile_user',
            'address_user',
            'suburb',
            'state',
            'role',
            'postcode',
            'dob'
        ];
    }

    /**
     * This method adds a new category.
     */
    public function addSsometa($ssouser,$data) {
        $ssoMeta = new Userssometa();
        $ssoMeta->setUsersso($ssouser);
        $ssoMeta->setKey($data['meta_key']);
        $ssoMeta->setValue($data['meta_value']);
        $this->entityManager->persist($ssoMeta);
        // Apply changes to database.
        $this->entityManager->flush();

        return $ssoMeta;
    }
    /**
     * This method updates data of an existing category.
     */
    public function updateSsometa($ssoMeta, $data) {
        //echo $data['meta_key']."=".$data['meta_value'];exit;

        $ssoMeta->setKey($data['meta_key']);
        $ssoMeta->setValue($data['meta_value']);
        // Apply changes to database.
        $this->entityManager->flush();

        return $ssoMeta;
    }

    public function addSsometaBulk($ssouser,$data) {
        $ssoMeta = new Userssometa();
        $ssoMeta->setUsersso($ssouser);
        $ssoMeta->setKey($data['meta_key']);
        $ssoMeta->setValue($data['meta_value']);
        $this->entityManager->persist($ssoMeta);
        return $ssoMeta;
    }


    public function updateSsometaBulk($ssoMeta, $data) {
        $ssoMeta->setKey($data['meta_key']);
        $ssoMeta->setValue($data['meta_value']);
        /*$this->entityManager->flush();*/

        return $ssoMeta;
    }

    public function delete($ssoMeta) {
        $this->entityManager->remove($ssoMeta);
        $this->entityManager->flush();
    }
    
    public function get_primary_keys(){
        return $this->primary_fileds;
    }



}