<?php
namespace Common\Repository;

use Doctrine\ORM\EntityRepository;
use Common\Entity\User;

/**
 * This is the custom repository class for Post entity.
 */
class UserRepository extends EntityRepository
{
    public function getAllUsers($email)
    {
        $entityManager = $this->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();
        $queryBuilder->select('p')
            ->from(User::class, 'p')
            ->where('p.status = ?1');
            if ($email != '') {
            $queryBuilder->andWhere('p.email LIKE ?2');
            $queryBuilder->setParameter('2', $email . '%');
        }
            $queryBuilder->orderBy('p.dateCreated', 'DESC')
            ->setParameter('1', User::STATUS_ACTIVE);

            return $queryBuilder->getQuery();
    }   

    public function getCount()
    {
        $entityManager = $this->getEntityManager();
        $qb = $entityManager->createQueryBuilder();
        $qb->select('count(p.id)');
        $qb->from(User::class, 'p');

        return $count = $qb->getQuery()->getSingleScalarResult();
    }    
}