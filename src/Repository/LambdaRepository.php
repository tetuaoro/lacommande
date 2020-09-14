<?php

namespace App\Repository;

use App\Entity\Lambda;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Lambda|null find($id, $lockMode = null, $lockVersion = null)
 * @method Lambda|null findOneBy(array $criteria, array $orderBy = null)
 * @method Lambda[]    findAll()
 * @method Lambda[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LambdaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Lambda::class);
    }

    // /**
    //  * @return Lambda[] Returns an array of Lambda objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('l.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Lambda
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
