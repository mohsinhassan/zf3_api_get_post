<?php
namespace Api\Service;
use Common\Entity\ApiAccessToken;
/**
 * This service is responsible for adding/editing users
 * and changing user password.
 */
class ApiTokenManager
{
    /**
     * Doctrine entity manager.
     * @var Doctrine\ORM\EntityManager
     */
    private $entityManager;  
    
    /**
     * Constructs the service.
     */
    public function __construct($entityManager) 
    {
        $this->entityManager = $entityManager;
    }
    
    /**
     * This method adds a new user.
     */
    //,$consulttype,$source,$status



    public function createToken($data,$user)
    {
        $token = new ApiAccessToken();

        $token->setIpAddress($data['ipAddress']);
        $token->setEmail($data['email']);

        $token->setRefreshTokenExpiry($data['refreshTokenExpiry']);

        $token->setAccessToken($data['accessToken']);
        $token->setRefreshToken($data['refreshToken']);

        $token->setTokenExpiry($data['tokenExpiry']);
        $token->setUser($user);

        // Add the entity to the entity manager.
        $this->entityManager->persist($token);
        
        // Apply changes to database.
        $this->entityManager->flush();
        
        return $token;
    }

    /**
     * This method updates data of an existing user.
     */

    public function refreshToken($token, $data)
    {

        $token->setAccessToken($data['accessToken']);
        $token->setTokenExpiry($data['tokenExpiry']);


        $this->entityManager->flush();
        return true;
    }

    public function deleteClinicalnotes($id) {
        $this->entityManager->remove($id);
        $this->entityManager->flush();
    }
    
    /**
     * Checks whether an active user with given email address already exists in the database.     
     */
    public function checkClinicalnotesExists($consult) {
        
        $qua = $this->entityManager->getRepository(Clinicalnotes::class)
                ->findOneByClinicalnotes($consult);
        
        return $qua !== null;
    }

}