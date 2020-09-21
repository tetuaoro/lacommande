<?php

namespace App\Repository;

use App\Entity\Menu;
use App\Entity\Provider;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method null|Menu find($id, $lockMode = null, $lockVersion = null)
 * @method null|Menu findOneBy(array $criteria, array $orderBy = null)
 * @method Menu[]    findAll()
 * @method Menu[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MenuRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Menu::class);
    }

    public function findGrouped()
    {
        return $this->createQueryBuilder('m')
            ->select('COUNT(c) AS HIDDEN ct', 'm')
            ->leftJoin('m.category', 'c')
            ->groupBy('m')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findMyMenu(Provider $provider)
    {
        return $this->createQueryBuilder('m')
            ->leftJoin('m.provider', 'p')
            ->where('p = :id')
            ->setParameter('id', $provider->getId())
            ->getQuery()
            ->getResult()
        ;
    }

    // /**
    //  * @return Menu[] Returns an array of Menu objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Menu
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
