<?php

namespace App\Repository;

use App\Entity\Booking;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Client;

/**
 * @extends ServiceEntityRepository<Booking>
 */
class BookingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Booking::class);
    }

    //    /**
    //     * @return Booking[] Returns an array of Booking objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('b')
    //            ->andWhere('b.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('b.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Booking
    //    {
    //        return $this->createQueryBuilder('b')
    //            ->andWhere('b.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function countByClientInWeek(Client $client, \DateTimeImmutable $date): int
    {
        // Calculamos el lunes y domingo de la semana de la actividad
        $monday = $date->modify('monday this week')->setTime(0, 0, 0);
        $sunday = $date->modify('sunday this week')->setTime(23, 59, 59);

        return $this->createQueryBuilder('b')
            ->select('count(b.id)')
            ->join('b.activity', 'a')
            ->where('b.client = :client')
            ->andWhere('a.date_start BETWEEN :start AND :end')
            ->setParameter('client', $client)
            ->setParameter('start', $monday)
            ->setParameter('end', $sunday)
            ->getQuery()
            ->getSingleScalarResult();
    }
}
