<?php

namespace Common\Repository;

use Doctrine\ORM\EntityRepository;
use Common\Entity\Role;

/**
 * This is the custom repository class for Post entity.
 */
class RoleRepository extends EntityRepository
{

    public function getAllRoles($role)
    {
        $entityManager = $this->getEntityManager();

        $queryBuilder = $entityManager->createQueryBuilder();

        $queryBuilder->select('p')
            ->from(Role::class, 'p')
            ->orderBy('p.dateCreated', 'DESC');

        if ($role != '') {
            $queryBuilder->andWhere('p.name LIKE ?2');
            $queryBuilder->setParameter('2', $role . '%');
        }


        return $queryBuilder->getQuery();
    }
}