<?php

namespace App\Repository;

use App\Entity\Activity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Booking;
use App\Enum\ActivityTypeEnum;

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

    public function findFiltered(?ActivityTypeEnum $type, bool $onlyfree, int $page, int $pageSize, string $sort, string $order): array
    {
        $qb = $this->createQueryBuilder('a')

            ->select('a');

        // 1. Filtro por tipo (si no es null)
        if ($type !== null) {
            $qb->andWhere('a.type = :type')
                ->setParameter('type', $type);
        }

        // 2. Filtro de plazas libres (onlyfree) 
        if ($onlyfree) {

            $subQuery = $this->getEntityManager()->createQueryBuilder()
                ->select('count(b.id)')
                ->from(Booking::class, 'b')
                ->where('b.activity = a.id')
                ->getDQL();

            $qb->andWhere('(' . $subQuery . ') < a.max_participants');
        }

        // 3. Orden dinámico (Requisito Antigravity)
        $allowedSorts = ['date_start', 'max_participants', 'type'];

        $sortField = in_array($sort, $allowedSorts) ? "a.$sort" : 'a.date_start';

        $qb->orderBy($sortField, $order);

        // 4. Paginación 
        $qb->setFirstResult(($page - 1) * $pageSize)
            ->setMaxResults($pageSize);

        return $qb->getQuery()->getResult();
    }
}
