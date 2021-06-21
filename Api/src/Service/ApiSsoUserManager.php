<?php

namespace Api\Service;

use Common\Entity\Usersso;
use Common\Entity\Setting;
use Common\Event\EventTargets;

/**
 * This service is responsible for adding/editing users
 * and changing user password.
 */
class ApiSsoUserManager {

    /**
     * Doctrine entity manager.
     * @var Doctrine\ORM\EntityManager
     */
    private $entityManager;

    /**
     * Constructs the service.
     */
    /* Setting roleId for patient */
    private $roleId;
    private $active;
    private $deactive;

    public function __construct($entityManager) {
        $this->entityManager = $entityManager;
        $this->roleId = 3;
        $this->active = 1;
        $this->deactive = 0;
    }

    /**
     * This method adds a new user.
     */
    //,$consulttype,$source,$status



    public function addUserSso($data) {

            $user = new Usersso();
            $user->setEmail($data['ssoEmail']);
            $user->setuserName($data['username']);
            $user->setPassword($data['ssoPassword']);
            $user->setAddress($data['address']);
            $user->setDob($data['dob']);
            $user->setGender($data['gender']);
            $user->setMobile($data['mobile']);
            $user->setPostcode($data['postcode']);
            $user->setState($data['state']);
            $user->setSuburb($data['suburb']);
            $user->setFirstName($data['firstname']);
            $user->setLastName($data['surname']);
            $user->setSsoid($data['sso_id']);
            if(empty($data['role'])){
                $user->setRole($this->roleId);
            }else{
                $user->setRole($data['role']);
            }
            $user->setStatus($this->active);
            $currentDate = date('Y-m-d H:i:s');
            $user->setDateCreated($currentDate);
            // Add the entity to the entity manager.
            $this->entityManager->persist($user);
            // Apply changes to database.
            $this->entityManager->flush();
            //return $user;
            return $user;
        
    }

    /**
     * This method updates data of an existing user.
     */
    public function updateUser($user, $data) {
        // Do not allow to change user email if another user with such email already exits.
            $user->setAddress($data['address']);
            $user->setEmail($data['ssoEmail']);
            $user->setDob($data['dob']);
            $user->setGender($data['gender']);
            $user->setMobile($data['mobile']);
            $user->setPostcode($data['postcode']);
            $user->setState($data['state']);
            $user->setSuburb($data['suburb']);
            $user->setFirstName($data['firstname']);
            $user->setLastName($data['surname']);
            // Apply changes to database.
            $this->entityManager->flush();

            
            $eventTarget = new EventTargets($this->entityManager, $data);
            $eventTarget->updateUserSSOInfo();

            return $user;
    }
    /**
     * This method updates data of an existing user.
     */
    public function updateUserThirdParty($user, $data) {
        // Do not allow to change user email if another user with such email already exits.
        if(!empty($data['address']))
            $user->setAddress($data['address']);
        if(!empty($data['ssoEmail']))
            $user->setEmail($data['ssoEmail']);
        if(!empty($data['dob']))
            $user->setDob($data['dob']);
        if(!empty($data['gender']))
            $user->setGender($data['gender']);
        if(!empty($data['mobile']))
            $user->setMobile($data['mobile']);
        if(!empty($data['postcode']))
            $user->setPostcode($data['postcode']);
        if(!empty($data['state']))
            $user->setState($data['state']);
        if(!empty($data['suburb']))
            $user->setSuburb($data['suburb']);
        if(!empty($data['firstname']))
            $user->setFirstName($data['firstname']);
        if(!empty($data['surname']))
            $user->setLastName($data['surname']);
        // Apply changes to database.
        $this->entityManager->flush();


        $eventTarget = new EventTargets($this->entityManager, $data);
        $eventTarget->updateUserSSOInfo();

        return $user;
    }
    

    public function userApprove($user, $data) {

        // Do not allow to change user email if another user with such email already exits.
        $user->setSsoid($data['sso_id']);
        $user->setStatus($this->active);
        // Apply changes to database.
        $this->entityManager->flush();
        echo "done<br>";
        return $user;
    }

    public function deleteClinicalnotes($id) {
        $this->entityManager->remove($id);
        $this->entityManager->flush();
    }

    /**
     * Change password
     */
    public function changePassword($user, $data) {

        $user->setPassword($data['newPassword']);
        $this->entityManager->flush();
        return $user;
    }

    public function checkUserNameExists($userName) {
        $qua = $this->entityManager->getRepository(Usersso::class)
                ->findOneByUserName($userName);

        return $qua !== null;
    }

    /**
     * Checks whether an active user with given email address already exists in the database.
     */
    public function checkUserExists($email) {

        $qua = $this->entityManager->getRepository(Usersso::class)
                ->findOneByEmail($email);

        return $qua !== null;
    }

    /* Delete user from database
     * */

    public function deleteUser($id) {

        $this->entityManager->remove($id);
        $this->entityManager->flush();
    }

    public function get_user_meta($user) {
        $counter = 0;
        $r = array();
        foreach ($user->getUserSsoMeta() as $k) {
            $r[$counter]['meta_key'] = $k->getKey();
            $r[$counter]['meta_value'] = $k->getValue();
            $counter++;
        }
        return $r;
    }



    public function addResetPasswordToken($user, $token, $tokenExpiry)
    {
        $user->setPasswordResetToken($token);
        $user->setPasswordResetExpiry($tokenExpiry);
        // Apply changes to database.
        $this->entityManager->flush();
        return $user;
    }
    /**
 * This method updates data of an existing user.
 */
    public function updateFistNameLastName($user, $data) {
        // Do not allow to change user email if another user with such email already exits.
        $user->setFirstName($data['first_name']);
        $user->setLastName($data['last_name']);
        // Apply changes to database.
        $this->entityManager->flush();


        $eventTarget = new EventTargets($this->entityManager, $data);
        $eventTarget->updateUserSSOInfo();

        return $user;
    }

    /**
     * Author : Mohsin Hassan
     * This function will update mobile number of sso user
     */
    public function setUserMobileNumber($user, $data)
    {
        $user->setAddress($user->getAddress());
        $user->setEmail($user->getEmail());
        $user->setDob($user->getDob());
        $user->setGender($user->getGender());
        $user->setPostcode($user->getPostcode());
        $user->setState($user->getState());
        $user->setSuburb($user->getSuburb());
        $user->setFirstName($user->getFirstName());
        $user->setLastName($user->getLastName());
        
        $user->setMobile($data['mobile_number']);
        // Apply changes to database.
        $this->entityManager->flush();        
    }

     /**
     * Author : Mohsin Hassan
     * This function will update mobile number of sso user
     */
    public function setUserPermission($user, $data)
    {
        
        
    }

}
