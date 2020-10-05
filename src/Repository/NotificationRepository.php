<?php

namespace App\Repository;

use App\Entity\Notification;
use App\Entity\Provider;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method null|Notification find($id, $lockMode = null, $lockVersion = null)
 * @method null|Notification findOneBy(array $criteria, array $orderBy = null)
 * @method Notification[]    findAll()
 * @method Notification[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NotificationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Notification::class);
    }

    public function findNotificationByProvider(Provider $provider)
    {
        return $this->createQueryBuilder('n')
            ->innerJoin('n.providers', 'p', 'WITH', 'p = :pid')
            ->setParameter('pid', $provider->getId())
            ->orderBy('n.createdAt', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function getNotifCount(Provider $provider)
    {
        return $this->createQueryBuilder('n')
            ->select('COUNT(n)')
            ->innerJoin('n.providers', 'p', 'WITH', 'p = :pid')
            ->setParameter('pid', $provider->getId())
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    public function getNotifs()
    {
        return $this->createQueryBuilder('n')->getQuery()->getResult();
    }

    // /**
    //  * @return Notification[] Returns an array of Notification objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('n.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Notification
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
