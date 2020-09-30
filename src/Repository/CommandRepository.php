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
        $q = $this->createQueryBuilder('c')
            ->select('c')
            ->leftJoin('c.providers', 'p')
            ->where('p = :id')
            ;

        $compare = $form->get('compare')->getData();
        $date = $form->get('date')->getData();

        // https://stackoverflow.com/questions/13421635/failed-to-parse-time-string-at-position-41-i-double-timezone-specification
        /* $today = date_create_from_format('D M d Y H:i:s e+', $date)->setTimezone(new \DateTimeZone('Pacific/Honolulu'))->setTime(0, 0);
        $tomorrow = date_create_from_format('D M d Y H:i:s e+', $date)->setTimezone(new \DateTimeZone('Pacific/Honolulu'))->modify('+1 day')->setTime(0, 0);
        */

        //https://stackoverflow.com/questions/33035497/php-comparing-two-datetime-objects-with-different-timezones
        $tz = new \DateTimeZone('UTC');
        $today = (new \DateTime($date))->setTime(0, 0)->setTimezone($tz);
        $tomorrow = clone $today;
        $tomorrow->modify('+1 day');

        if ('=' == $compare) {
            $q->andWhere('c.commandAt BETWEEN :today AND :tomorrow')
                ->setParameters([
                    'today' => $today,
                    'tomorrow' => $tomorrow,
                ])
            ;
        } elseif ('<' == $compare) {
            $q->andWhere('c.commandAt < :today')
                ->setMaxResults(20)
                ->setParameter('today', $today)
            ;
        } else {
            $q->andWhere('c.commandAt > :tomorrow')
                ->setParameter('tomorrow', $tomorrow)
            ;
        }

        $q->orderBy('c.commandAt', $form->get('order')->getData());

        return $q
            ->setParameter('id', $provider->getId())
            ->getQuery()
            ->getResult()
        ;
    }

    public function getFiltererByProvider($id, Provider $provider)
    {
        return $this->createQueryBuilder('c')
            ->select('c, m')
            ->leftJoin('c.meals', 'm')
            ->where('m.provider = :pid')
            ->andWhere('c = :cid')
            ->setParameters([
                'pid' => $provider->getId(),
                'cid' => $id,
            ])
            ->getQuery()
            ->getSingleResult()
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
            ->where('c = :id')
            ->setParameter('id', $id)
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
