<?php

namespace Common\Repository;

use Doctrine\ORM\EntityRepository;
use Common\Entity\Setting;
/**
 * This is the custom repository class for Post entity.
 */
class SettingRepository extends EntityRepository {

    public function getAllSetting($filter) {
        //print_r($filter);exit;
        $entityManager = $this->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();
        $queryBuilder->select('p')
            ->from(Setting::class, 'p');
        if ($filter != ''){
            $queryBuilder->where('p.id = ?1');
            $queryBuilder->setParameter('1', $filter );

            $queryBuilder->orWhere('p.meta like ?2');
            $queryBuilder->setParameter('2', '%'.$filter.'%' );
        }
        $queryBuilder->orderBy('p.id', 'DESC');

        return $queryBuilder->getQuery();
    }

}
