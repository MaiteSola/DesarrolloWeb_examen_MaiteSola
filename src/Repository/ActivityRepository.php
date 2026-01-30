<?php

namespace App\Repository;

use App\Entity\Activity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Booking;

/**
 * @extends ServiceEntityRepository<Activity>
 */
class ActivityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Activity::class);
    }

    //    /**
    //     * @return Activity[] Returns an array of Activity objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('a.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Activity
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function findFiltered(?string $type, bool $onlyfree, int $page, int $pageSize): array
    {
        $qb = $this->createQueryBuilder('a')
            // Seleccionamos la actividad y dejamos que Doctrine maneje el resto
            ->select('a');

        // 1. Filtro por tipo (si no es null) 
        if ($type !== null) {
            $qb->andWhere('a.type = :type')
                ->setParameter('type', $type);
        }

        // 2. Filtro de plazas libres (onlyfree) 
        if ($onlyfree) {
            // Subconsulta para contar cuántos bookings tiene cada actividad
            $subQuery = $this->getEntityManager()->createQueryBuilder()
                ->select('count(b.id)')
                ->from(Booking::class, 'b')
                ->where('b.activity = a.id')
                ->getDQL();

            $qb->andWhere('(' . $subQuery . ') < a.max_participants');
        }

        // 3. Orden obligatorio por fecha descendente [cite: 1478, 1481]
        $qb->orderBy('a.date_start', 'DESC');

        // 4. Paginación 
        $qb->setFirstResult(($page - 1) * $pageSize)
            ->setMaxResults($pageSize);

        return $qb->getQuery()->getResult();
    }
}
