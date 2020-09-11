<?php

namespace App\Repository;

use App\Entity\Command;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method null|Command find($id, $lockMode = null, $lockVersion = null)
 * @method null|Command findOneBy(array $criteria, array $orderBy = null)
 * @method Command[]    findAll()
 * @method Command[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommandRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Command::class);
    }

    public function findByProviderOrderByDate(int $id)
    {
        return $this->createQueryBuilder('c')
            ->select('c')
            ->leftJoin('c.providers', 'p')
            ->where('p.id = :id')
            ->orderBy('c.commandAt', 'DESC')
            ->setParameter('id', $id)
            ->getQuery()
            ->getResult()
        ;
    }

    // /**
    //  * @return Command[] Returns an array of Command objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    public function findCommandGroupByProvider(int $id)
    {
        return $this->createQueryBuilder('c')
            ->select('COUNT(p) AS HIDDEN cmds', 'm', 'c')
            ->leftJoin('c.providers', 'p')
            ->leftJoin('p.meals', 'm')
            ->where('c.id = :idp')
            ->setParameter('idp', $id)
            ->getQuery()
            ->getResult()
        ;
    }

    /*
    public function findOneBySomeField($value): ?Command
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
