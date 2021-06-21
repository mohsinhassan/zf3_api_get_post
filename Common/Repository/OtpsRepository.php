<?php

namespace Common\Repository;
use Doctrine\ORM\EntityRepository;
use Common\Entity\Otps;

/** Author : Mohsin Hassan
 
 * This is the custom repository class for Post entity.
 */
class OtpsRepository extends EntityRepository
{

    /*
        Author : Mohsin Hassan
        This function will make in-active all otps of the user
        Parameters : username
    */

    public function setPreviousOtpsDeactivate($username)
    {
        $entityManager = $this->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();

        $q = $queryBuilder->update(Otps::class, 'p')
            ->set('p.status', '?2')
            ->where('p.username = ?1')
            ->setParameter(1, $username)
            ->setParameter(2, 0)
            ->getQuery();
        $q->execute();
    }

    /*
        Author : Mohsin Hassan
        This function will make in-active all otps of the user
        Parameters : username
    */

    public function setPreviousOtpsDeactivateMobile($username)
    {
        $entityManager = $this->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();

        $q = $queryBuilder->update(Otps::class, 'p')
            ->set('p.status', '?2')
            ->where('p.username = ?1')
            ->andWhere('p.otpType = ?3')
            ->setParameter(1, $username)
            ->setParameter(2, 0)
            ->setParameter(3, 2)
            ->getQuery();
        $q->execute();
    }

    /*
        Author : Mohsin Hassan
        Parameters : array
        Returns : This function will return query result of user's active and not expired OTP        
    */

    public function getUserActiveOtp(array $data = []) : array
    {
        $entityManager = $this->getEntityManager();

        $queryBuilder = $entityManager->createQueryBuilder();

        $queryBuilder->select('p')
            ->from(Otps::class, 'p')
            ->where('p.username = ?1')
            ->andWhere('p.ipAddress =  ?2')
            ->andWhere('p.status = ?3')
            ->andWhere('p.otpExpiry >= ?4')
            ->andWhere('p.otp = ?5')
            ->andWhere('p.otpType = ?6')
            ->setParameter('1', $data['username'])
            ->setParameter('2', $data['ipAddress'])
            ->setParameter('3', 1)
            ->setParameter('4', $data['otpExpiry'])
            ->setParameter('5', $data['otp'])
            ->setParameter('6', $data['type']);
            return $queryBuilder->getQuery()->getResult();
    }
}