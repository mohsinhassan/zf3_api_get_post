<?php
namespace Api\Service;
use Admin\Entity\Clinicalnotes;
#use Admin\Service\ActivitylogManager;
/**
 * This service is responsible for adding/editing users
 * and changing user password.
 */
class ApiClinicalnotesManager
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



    public function addClinicalnotes($data,$doctor,$patient)
    {
        //echo "<pre>";print_r($data);exit;
        $clinicalnotes = new Clinicalnotes();


        $clinicalnotes->setDoctor($doctor);
        $clinicalnotes->setPatient($patient);
        $clinicalnotes->setClinicalNotes($data['clinicalNotes']);

        $currentDate = date('Y-m-d H:i:s');
        $clinicalnotes->setDateCreated($currentDate);
        $clinicalnotes->setDateUpdated($currentDate);

        // Add the entity to the entity manager.
        $this->entityManager->persist($clinicalnotes);
        
        // Apply changes to database.
        $this->entityManager->flush();
        
        return $clinicalnotes;
    }

    /**
     * This method updates data of an existing user.
     */

    public function updateClinicalnotes($clinicalnotes,$data,$doctor,$patient)
    {

        $currentDate = date('Y-m-d H:i:s');
        $clinicalnotes->setDoctor($doctor);
        $clinicalnotes->setPatient($patient);
        $clinicalnotes->setClinicalNotes($data['clinicalNotes']);
        //$clinicalnotes->setDateCreated($currentDate);
        $clinicalnotes->setDateUpdated($currentDate);

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