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

}
