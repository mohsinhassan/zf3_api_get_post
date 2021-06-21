<?php

namespace Common\Repository;

use Doctrine\ORM\EntityRepository;
use Common\Entity\Permission;

/**
 * This is the custom repository class for Post entity.
 */
class PermissionRepository extends EntityRepository
{

    public function getAllPermissions($permission)
    {
        $entityManager = $this->getEntityManager();

        $queryBuilder = $entityManager->createQueryBuilder();

        $queryBuilder->select('p')
            ->from(Permission::class, 'p')
            ->orderBy('p.dateCreated', 'DESC');

        if ($permission != '') {
            $queryBuilder->andWhere('p.name LIKE ?2');
            $queryBuilder->setParameter('2', $permission . '%');
        }


        return $queryBuilder->getQuery();
    }
}