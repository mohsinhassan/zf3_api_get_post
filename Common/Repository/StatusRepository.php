<?php

namespace Common\Repository;

use Doctrine\ORM\EntityRepository;
use Common\Entity\Status;

/**
 * This is the custom repository class for Post entity.
 */
class StatusRepository extends EntityRepository {

    public function getAllStatus($status) {
        $entityManager = $this->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();
        $queryBuilder->select('p')
                ->from(Status::class, 'p');
        if ($status != '') {
            $queryBuilder->where('p.status LIKE ?1');
            $queryBuilder->setParameter('1', $status . '%');
        }
        $queryBuilder->orderBy('p.dateCreated', 'DESC');

        return $queryBuilder->getQuery();
    }

}
