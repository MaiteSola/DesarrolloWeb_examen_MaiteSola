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

            $infoDto = new StatisticsDTO(
                (int)$row['numActivities'],
                (int)($row['totalSeconds'] / 60) // Cálculo correcto de minutos
            );

            // Usamos ->value para extraer el string del Enum si Doctrine devuelve el objeto Enum
            // Si devuelve string directo, quitar ->value (pero asumimos Enum por el mapeo)
            $typeVal = $row['activityType'] instanceof \UnitEnum ? $row['activityType']->value : $row['activityType'];

            $typeDto = new StatisticsByTypeDTO($typeVal, $infoDto);

            // Añadimos al array de estadísticas usando la propiedad pública
            $statsByYear[$year]->statistics[] = $typeDto;
        }

        return array_values($statsByYear); // Reindexamos para que sea un array JSON limpio
    }
}
