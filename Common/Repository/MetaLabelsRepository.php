<?php

namespace Common\Repository;

use Doctrine\ORM\EntityRepository;
use Common\Entity\MetaLabels;
/**
 * This is the custom repository class for Post entity.
 */
class MetaLabelsRepository extends EntityRepository {

    public function getAllMetaLabels($filter = '') {
        $entityManager = $this->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();
        $queryBuilder->select('p')
            ->from(MetaLabels::class, 'p');
        if ($filter != ''){
            $queryBuilder->where('p.id = ?1');
            $queryBuilder->setParameter('1', $filter );

            $queryBuilder->orWhere('p.metaLabel like ?2');
            $queryBuilder->setParameter('2', '%'.$filter.'%' );

            $queryBuilder->orWhere('p.metaKey like ?3');
            $queryBuilder->setParameter('3', '%'.$filter.'%' );

            $queryBuilder->orWhere('p.status = ?4');
            $queryBuilder->setParameter('4', 'Active' );

            $queryBuilder->orWhere('p.category like ?5');
            $queryBuilder->setParameter('5', '%'.$filter.'%' );
        }
        $queryBuilder->orderBy('p.id', 'DESC');

        return $queryBuilder->getQuery();
    }

    public function getMetaLabelsEx($exArr , $category='User')
    {
        $entityManager = $this->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();
        $queryBuilder->select('p')
            ->from(MetaLabels::class, 'p');

        if ($exArr != ''){

            $queryBuilder->where('p.metaKey NOT IN ( ?1 )');
            $queryBuilder->setParameter('1', $exArr );

            $queryBuilder->andWhere('p.category = ?2');
            $queryBuilder->setParameter('2', $category );

        }
        else{
            $queryBuilder->where('p.category = ?1');
            $queryBuilder->setParameter('1', $category );
        }
        $queryBuilder->andWhere('p.status = ?3');
        $queryBuilder->setParameter('3', 'Active' );


        $queryBuilder->orderBy('p.id', 'DESC');

        return $queryBuilder->getQuery();
    }

    public function getMetaLabelsByCat($category)
    {
        $entityManager = $this->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();
        $queryBuilder->select('p')
            ->from(MetaLabels::class, 'p');

        if ($category != '') {
            $queryBuilder->where('p.category = ?1');
            $queryBuilder->setParameter('1', $category);
        }
        $queryBuilder->andWhere('p.status = ?3');
        $queryBuilder->setParameter('3', 'Active' );

        $queryBuilder->orderBy('p.id', 'DESC');

        return $queryBuilder->getQuery();
    }
}
