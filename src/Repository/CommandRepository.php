<?php

namespace App\Repository;

use App\Entity\Command;
use App\Entity\Provider;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Form\FormInterface;

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

    public function findByProviderOrderByCommandDate(Provider $provider, FormInterface $form)
    {
        return $this->createQueryBuilder('c')
            ->select('c')
            ->leftJoin('c.providers', 'p')
            ->where('p.id = :id')
            ->andWhere('c.commandAt '.$form->get('compare')->getData().' :date')
            ->orderBy('c.commandAt', $form->get('order')->getData())
            ->setMaxResults($form->get('limit')->getData())
            ->setParameters([
                'id' => $provider->getId(),
                'date' => $form->get('date')->getData(),
            ])
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
