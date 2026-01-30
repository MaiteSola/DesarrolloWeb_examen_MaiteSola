<?php
// src/Service/StatisticsService.php
namespace App\Service;

use App\Models\Response\Statistics\StatisticsByYearDTO;
use App\Models\Response\Statistics\StatisticsByTypeDTO;
use App\Models\Response\Statistics\StatisticsDTO;
use App\Repository\BookingRepository;

class StatisticsService
{
    public function __construct(private BookingRepository $bookingRepository) {}

    public function getClientStatistics(int $clientId): array
    {
        $rawData = $this->bookingRepository->getStatisticsByClient($clientId);
        $statsByYear = [];

        foreach ($rawData as $row) {
            $year = (int)$row['yearGroup'];

            if (!isset($statsByYear[$year])) {
                $statsByYear[$year] = new StatisticsByYearDTO($year);
            }

            // Creamos el DTO de información (minutos y conteo)
            $infoDto = new StatisticsDTO(
                (int)$row['numActivities'],
                (int)$row['totalMinutes']
            );

            // Creamos el DTO por tipo (Spinning, etc)
            $typeDto = new StatisticsByTypeDTO($row['activityType']->value, $infoDto);

            // Añadimos al array del año correspondiente
            $statsByYear[$year]->statistics[] = $typeDto;
        }

        return array_values($statsByYear); // Reindexamos para que sea un array JSON limpio
    }
}
