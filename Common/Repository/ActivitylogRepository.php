<?php

namespace Common\Repository;

use Common\Entity\User;
use Common\Entity\Usersso;
use Doctrine\ORM\EntityRepository;
use Common\Entity\Activitylog;

/**
 * This is the custom repository class for Post entity.
 */
class ActivitylogRepository extends EntityRepository
{

    public function getAllActivitylog($action_search,$message_search,$data_search,$state_search,$date_search)
    {
        $entityManager = $this->getEntityManager();

        $queryBuilder = $entityManager->createQueryBuilder();

        $queryBuilder->select('p')
            ->from(Activitylog::class, 'p')
            ->orderBy('p.dateCreated', 'DESC');
        $queryBuilder->where('1 = 1');

        if (!empty($action_search)) {
            $queryBuilder->andWhere('p.action LIKE ?1');
            $queryBuilder->setParameter('1', '%'. $action_search . '%');
        }
        if (!empty($message_search )) {
            $queryBuilder->andWhere('p.message LIKE ?2');
            $queryBuilder->setParameter('2', '%'. $message_search . '%');
        }
        if (!empty($data_search)) {
            $queryBuilder->andWhere('p.data LIKE ?3');
            $queryBuilder->setParameter('3', '%'. $data_search . '%');
        }
        if (!empty($state_search)) {
            $queryBuilder->andWhere('p.state LIKE ?4');
            $queryBuilder->setParameter('4', '%'. ucfirst($state_search) . '%');
        }
        if (!empty($date_search)) {
            $queryBuilder->andWhere('p.dateCreated  LIKE ?5');
            $queryBuilder->setParameter('5',  '%'.$date_search . '%');
        }

        return $queryBuilder->getQuery();
    }

    public function getAllActivitylogJoinUser($action_search,$message_search,$data_search,$state_search,$date_search)
    {
        $entityManager = $this->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();

        $queryBuilder->select('a','us.userName', 'u.fullName' )
            ->from(Activitylog::class, 'a')
            ->leftJoin(
                Usersso::class, 'us', \Doctrine\ORM\Query\Expr\Join::WITH, ' us.id = a.loggedFor'
            )
            ->leftJoin(
                User::class, 'u', \Doctrine\ORM\Query\Expr\Join::WITH, 'u.id = a.loggedBy'
            );

        $queryBuilder->where('1 = 1');
        if (!empty($action_search)) {
            $queryBuilder->andWhere('a.action LIKE ?1');
            $queryBuilder->setParameter('1', '%'. $action_search . '%');
        }
        if (!empty($message_search )) {
            $queryBuilder->andWhere('a.message LIKE ?2');
            $queryBuilder->setParameter('2', '%'. $message_search . '%');
        }
        if (!empty($data_search)) {
            $queryBuilder->andWhere('a.data LIKE ?3');
            $queryBuilder->setParameter('3', '%'. $data_search . '%');
        }
        if (!empty($state_search)) {
            $queryBuilder->andWhere('a.state LIKE ?4');
            $queryBuilder->setParameter('4', '%'. ucfirst($state_search) . '%');
        }
        if (!empty($date_search)) {
            $queryBuilder->andWhere('a.dateCreated  LIKE ?5');
            $queryBuilder->setParameter('5',  '%'.$date_search . '%');
        }

        $queryBuilder->orderBy('a.dateCreated', 'DESC');
        return $queryBuilder->getQuery();
        //->getResult(\Doctrine\ORM\AbstractQuery::HYDRATE_ARRAY)
    }

    public function getAllActivitylogSsoView($log)
    {

        $entityManager = $this->getEntityManager();

        $queryBuilder = $entityManager->createQueryBuilder();

        $queryBuilder->select('p')
            ->from(Activitylog::class, 'p')
            ->orderBy('p.dateCreated', 'DESC');

        $queryBuilder->andWhere('p.loggedFor = ?1');
        $queryBuilder->setParameter('1', $log );
        /*if ($log != '') {
        }*/

        return $queryBuilder->getQuery();
    }
}