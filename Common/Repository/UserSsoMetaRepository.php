<?php
namespace Common\Repository;

use Common\Entity\Userssometa;
use Doctrine\ORM\EntityRepository;
use Common\Entity\Usersso;

/**
 * This is the custom repository class for Post entity.
 */
class UserSsoMetaRepository extends EntityRepository
{
    public function getSsoMeta($filter)
    {
        $entityManager = $this->getEntityManager();
        $qb = $entityManager->createQueryBuilder();
        $qb->select("p.id");
        $qb->from(Userssometa::class, 'p');
        $qb->where('p.key  = ?1');
        $qb->andWhere('p.userssoId  = ?2');
        $qb->setParameter('1', $filter['meta_key']);
        $qb->setParameter('2', $filter['user_sso_id']);
        return $qb->getQuery()->getResult();

    }
    public function getAllSsoMeta($meta)
    {
        $entityManager = $this->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();
        $queryBuilder->select('p')
            ->from(Userssometa::class, 'p');
            if ($meta != '') {

                $queryBuilder->andWhere('p.userssoId = ?2');
                $queryBuilder->orWhere('p.key LIKE ?3');
                $queryBuilder->orWhere('p.value LIKE ?4');
                $queryBuilder->setParameter('2', $meta );
                $queryBuilder->setParameter('3', $meta . '%');
                $queryBuilder->setParameter('4', $meta . '%');
            }
            $queryBuilder->orderBy('p.id', 'DESC');

            return $queryBuilder->getQuery();
    }
    /*
     * get all meta of user with users details
     */
    public function findMetaByUser($id)
    {
        $entityManager = $this->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();
        $queryBuilder->select('p')
            ->from(Usersso::class, 'p')
            ->join('p.userssometa', 'm')
            ->where('p.id = ?1')
            //->andWhere('m.name = ?2')
            ->orderBy('p.dateCreated', 'DESC')
            ->setParameter('1', $id);
        return $queryBuilder->getQuery();
    }

    public function checkExists($data, $type = '')
    {
        $entityManager = $this->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();
        $queryBuilder->select('p')
            ->from(Userssometa::class, 'p');
        $queryBuilder->where('p.userssoId = ?1');
        $queryBuilder->andWhere('p.key = ?2');
        $queryBuilder->setParameter('1', $data['user_sso_id']);
        $queryBuilder->setParameter('2', $data['meta_key']);

        if ($type)
            return $queryBuilder->getQuery()->getResult(2);

        return $queryBuilder->getQuery()->getResult();
    }

    public function getUserMetaDetails($id, $pageNo)
    {
        $totalRows = 10;
        $offset = $totalRows * ($pageNo - 1);

        $conn = $this->getEntityManager()
            ->getConnection();

        $sql = "SELECT um.id, um.meta_key, um.meta_value, ml.meta_label
            FROM user_sso_meta um
            JOIN user_sso u ON u.id = um.user_sso_id
            JOIN meta_labels ml ON um.meta_key = ml.meta_key
            WHERE u.id = $id
            ORDER BY um.id DESC
            LIMIT $totalRows
            OFFSET $offset";

        $stmt = $conn->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll();
    }
}
