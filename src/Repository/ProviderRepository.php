<?php

namespace App\Repository;

use App\Entity\Provider;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method null|Provider find($id, $lockMode = null, $lockVersion = null)
 * @method null|Provider findOneBy(array $criteria, array $orderBy = null)
 * @method Provider[]    findAll()
 * @method Provider[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProviderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Provider::class);
    }

    public function getAVGPriceMeal(Provider $provider)
    {
        return $this->createQueryBuilder('p')
            ->select('AVG(m.price)')
            ->leftJoin('p.meals', 'm')
            ->where('p = :id')
            ->andWhere('m.isDelete = FALSE')
            ->setParameter('id', $provider->getId())
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    public function getTotalCommand(Provider $provider)
    {
        return $this->createQueryBuilder('p')
            ->select('COUNT(c)')
            ->leftJoin('p.commands', 'c')
            ->where('p = :id')
            ->setParameter('id', $provider->getId())
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    public function paginator()
    {
        return $this->getAllowProvider()->getQuery();
    }

    public function findLastProvider($how = 3)
    {
        return $this->getAllowProvider()
            ->orderBy('p.createdAt', 'DESC')
            ->setMaxResults($how)
            ->getQuery()
            ->getResult()
        ;
    }

    public function getRandomProvider()
    {
        return $this->getAllowProvider()
            ->orderBy('p.createdAt', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function getSubUsers()
    {
        return $this->createQueryBuilder('p')
            ->select('s')
            ->innerJoin('p.subusers', 's')
            ->getQuery()
        ;
    }

    private function getAllowProvider()
    {
        return $this->createQueryBuilder('p')
            ->leftJoin('p.user', 'u')
            ->andWhere('u.validate = true')
        ;
    }

    // /**
    //  * @return Provider[] Returns an array of Provider objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Provider
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
