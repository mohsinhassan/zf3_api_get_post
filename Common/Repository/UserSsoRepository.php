<?php
namespace Common\Repository;

use Common\Entity\Activitylog;
use Common\Entity\Userssometa;
use Doctrine\ORM\EntityRepository;
use Common\Entity\Usersso;

/**
 * This is the custom repository class for Post entity.
 */
class UserSsoRepository extends EntityRepository
{
    public function getAllSsoUsers($email)
    {
        $entityManager = $this->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();
        $queryBuilder->select('p')
            ->from(Usersso::class, 'p');
            //->where('p.status = ?1');
            if ($email != '') {
                $queryBuilder->andWhere('p.email LIKE ?2');
                $queryBuilder->orWhere('p.ssoId LIKE ?3');
                $queryBuilder->orWhere('p.userName LIKE ?4');
                $queryBuilder->orWhere('p.fname LIKE ?5');
                $queryBuilder->orWhere('p.lname LIKE ?6');
                $queryBuilder->orWhere('p.dob LIKE ?7');
                $queryBuilder->orWhere('p.mobileNumber LIKE ?8');
                $queryBuilder->setParameter('2', $email . '%');
                $queryBuilder->setParameter('3', $email . '%');
                $queryBuilder->setParameter('4', $email . '%');
                $queryBuilder->setParameter('5', $email . '%');
                $queryBuilder->setParameter('6', $email . '%');
                $queryBuilder->setParameter('7', $email . '%');   
                $queryBuilder->setParameter('8', $email . '%');
            }
            $queryBuilder->orderBy('p.ssoId', 'DESC');
            //->setParameter('1', Usersso::STATUS_ACTIVE);

            return $queryBuilder->getQuery();
    } 

    public function getCount()
    {
        $entityManager = $this->getEntityManager();
        $qb = $entityManager->createQueryBuilder();
        $qb->select('count(p.id)');
        $qb->from(Usersso::class, 'p');

        return $count = $qb->getQuery()->getSingleScalarResult();
    }

    public function getMax()
    {
        $entityManager = $this->getEntityManager();
        $qb = $entityManager->createQueryBuilder();
        $qb->select('max(p.ssoId)');
        $qb->from(Usersso::class, 'p');

        return $count = $qb->getQuery()->getSingleScalarResult();
    }

    public function getUser($criteria,$value)
    {
        $entityManager = $this->getEntityManager();
        $qb = $entityManager->createQueryBuilder();
        //$qb->select('p.ssoId as id , CONCAT(p.ssoId," ",p.userName) as text');
        //$qb->select("CONCAT(p.ssoId,'^^^',p.fname,' ',p.lname) as id, CONCAT(p.userName,' [',p.email,']') as text");
        $qb->select("p.ssoId as id, CONCAT(p.userName,' [',p.email,']') as text");
        $qb->from(Usersso::class, 'p');
        $qb->andWhere('p.'.$criteria.' LIKE ?2');
        $qb->setParameter('2', $value . '%');

        return $qb->getQuery()->getResult();
    }

    public function getUserById($criteria,$value)
    {
        $entityManager = $this->getEntityManager();
        $qb = $entityManager->createQueryBuilder();
        $qb->select("p.ssoId,p.email,p.userName,p.status,p.fname,p.lname,p.gender,p.mobileNumber,p.addressUser,p.suburb,p.stateUser,p.role,p.postcode,p.dob,p.dateCreated");
        $qb->from(Usersso::class, 'p');
        $qb->where('p.'.$criteria.'  = ?1');
        $qb->setParameter('1', $value);

        return $qb->getQuery()->getResult();

    }

    public function checkDiffrentUserExists($email,$userName)
    {
        $entityManager = $this->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();
        $queryBuilder->select('count(p.email)')
            ->from(Usersso::class, 'p')
            ->where('p.email = ?1')
            ->andWhere('p.userName != ?2');
            $queryBuilder->setParameter('1', $email);
            $queryBuilder->setParameter('2', $userName);

        return $count = $queryBuilder->getQuery()->getSingleScalarResult();
    }

    public function checkResetPasswordToken($passwordResetToken)
    {

        $entityManager = $this->getEntityManager();
        $qb = $entityManager->createQueryBuilder();
        $qb->select("p.email");
        $qb->from(Usersso::class, 'p');
        $qb->where('p.passwordResetToken  = ?1');
        $qb->andWhere('p.passwordResetExpiry  >  ?2');
        $qb->setParameter('1', $passwordResetToken);

        $date = new \DateTime(date('Y-m-d H:i:s'));
        $date->format('Y-m-d H:i:s');

        $qb->setParameter('2', $date->format('Y-m-d H:i:s'));

        return $qb->getQuery()->getResult(2);
    }

    public function getUsersPerDay() {
        $conn = $this->getEntityManager()
                ->getConnection();
        $sql = "SELECT date(date_created) as day, COUNT(*) as users FROM user_sso where date_created BETWEEN (NOW() - INTERVAL 30 DAY) AND (NOW()+INTERVAL 1 DAY) GROUP BY date(date_created) order by date_created ASC";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    } 
    public function getUsersForDashboard() {
        $conn = $this->getEntityManager()
                ->getConnection();
        $sql = "SELECT (SELECT COUNT(u.id) FROM `user_sso` u INNER JOIN user_sso_meta m ON u.id =  m.user_sso_id WHERE m.meta_key = 'kiosk_user' AND m.meta_value = 1  AND u.status=1) AS kiosk,
        (SELECT COUNT(u.id) AS apa_nsw_users FROM `user_sso` u INNER JOIN user_sso_meta m ON u.id =  m.user_sso_id WHERE m.meta_key = 'organisation' AND m.meta_value = 'APA NSW'  AND u.status=1) AS APA_NSW,
        (SELECT COUNT(u.id) AS apa_nsw_users FROM `user_sso` u INNER JOIN user_sso_meta m ON u.id =  m.user_sso_id WHERE m.meta_key = 'organisation' AND m.meta_value = 'TPAV'  AND u.status=1) AS TPAV,
        (SELECT COUNT(u.id) AS apa_nsw_users FROM `user_sso` u INNER JOIN user_sso_meta m ON u.id =  m.user_sso_id WHERE m.meta_key = 'organisation' AND m.meta_value = 'APA QLD'  AND u.status=1) AS APA_QLD,
        (SELECT COUNT(u.id) AS apa_nsw_users FROM `user_sso` u INNER JOIN user_sso_meta m ON u.id =  m.user_sso_id WHERE m.meta_key = 'organisation' AND m.meta_value = 'APA VIC'  AND u.status=1) AS APA_VIC";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getSsoUserMetaActivity($filter)
    {
        $entityManager = $this->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();

        $queryBuilder->select('u.email','p.name' )
            ->from(Usersso::class, 'u')
            ->innerJoin(
                UserRole::class, 'm', \Doctrine\ORM\Query\Expr\Join::WITH, 'u.id = m.userssoId'
            )
            ->innerJoin(
                Activitylog::class, 'a', \Doctrine\ORM\Query\Expr\Join::WITH, 'u.id = a.loggedFor'
            );
        $queryBuilder->where('u.id = ' . $filter);
        /*if (count($filter) > 0) {
            $queryBuilder->where('p.orderNo = ' . $filter['order']);
        }*/
        //$queryBuilder->orderBy('a.order', 'ASC');
        return $queryBuilder->getQuery()->getResult(\Doctrine\ORM\AbstractQuery::HYDRATE_ARRAY);
        //return $queryBuilder->getQuery()->getScalarResult();


        /*$sql = "SELECT * FROM user_sso u LEFT JOIN user_sso_meta m ON u.id = m.user_sso_id
        LEFT JOIN activitylog a ON u.id = a.logged_for
        WHERE u.id = ";*/


    }


    public function getUserIdName($user,$fname,$lname,$mobile) {
        $entityManager = $this->getEntityManager();
        $qb = $entityManager->createQueryBuilder();



        $qb->select("sso.ssoId as id, sso.userName as text, sso.email ,sso.dob , concat(sso.fname, ' ', sso.lname) as name ,sso.mobileNumber ,sso.addressUser ");
        $qb->from(Usersso::class, 'sso');

        if(!empty($user))
        {
            $qb->where('sso.email like ?1');
            $qb->setParameter('1', '%' .$user . '%');
            
            $qb->orWhere('sso.userName like ?4');
            $qb->setParameter('4', '%' .$user . '%');
        }
        
        if(!empty($fname))
        {
            $qb->andWhere('sso.fname like ?2');
            $qb->setParameter('2', '%' .$fname . '%');

        }
        if(!empty($lname))
        {
            $qb->andWhere('sso.lname like ?3');
            $qb->setParameter('3', '%' .$lname . '%');
        }
        
        if(!empty($mobile))
        {
            $qb->andWhere('sso.mobileNumber like ?5');
            $qb->setParameter('5', '%' .$mobile . '%');
        }
        return $qb->getQuery()->getResult();
    }

    public function isUserAllowed($email,$featureName) {
        $conn = $this->getEntityManager()
                ->getConnection();
        $sql = "SELECT count(*) as canAccess FROM user_sso u 
        INNER JOIN user_role ur ON u.id= ur.user_id 
        INNER JOIN role_permission rm ON ur.role_id = rm.role_id 
        INNER JOIN permission p
        ON rm.permission_id = p.id 
        WHERE u.email = '".$email."' AND p.name = '".$featureName."'";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

}