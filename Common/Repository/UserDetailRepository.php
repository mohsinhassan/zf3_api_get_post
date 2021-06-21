<?php

namespace Common\Repository;

use Doctrine\ORM\EntityRepository;
use Common\Entity\Userdetail;

/**
 * This is the custom repository class for Post entity.
 */
class UserdetailRepository extends EntityRepository {

    public function getAllUserdetail($user_id) {
        $entityManager = $this->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();
        $queryBuilder->select('p')
            ->from(Userdetail::class, 'p')
            ->where('p.user_id = ?1');
        $queryBuilder->orderBy('p.dateCreated', 'DESC')
            ->setParameter('1', $user_id);

        return $queryBuilder->getQuery();
    }

}
