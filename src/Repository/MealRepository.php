<?php

namespace App\Repository;

use App\Entity\Meal;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method null|Meal find($id, $lockMode = null, $lockVersion = null)
 * @method null|Meal findOneBy(array $criteria, array $orderBy = null)
 * @method Meal[]    findAll()
 * @method Meal[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MealRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Meal::class);
    }

    public function findLastMeal($how = 3)
    {
        return $this->createQueryBuilder('m')
            ->where('m.stock > 0')
            ->orderBy('m.createdAt', 'DESC')
            ->setMaxResults($how)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findPopular($how = 8)
    {
        return $this->createQueryBuilder('m')
            ->select('COUNT(c) AS HIDDEN cmds', 'm')
            ->leftJoin('m.commands', 'c')
            ->where('m.stock > 0')
            ->orderBy('cmds', 'DESC')
            ->groupBy('m')
            ->setMaxResults($how)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findMealWithOutMenu($id)
    {
        return $this->createQueryBuilder('m')
            ->leftJoin('m.provider', 'p')
            ->andWhere('p.id = :id')
            ->setParameter('id', $id)
            ->orderBy('m.name', 'ASC')
        ;
    }

    public function paginator()
    {
        return $this->createQueryBuilder('m')->getQuery();
    }

    // /**
    //  * @return Meal[] Returns an array of Meal objects
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
    public function findOneBySomeField($value): ?Meal
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
