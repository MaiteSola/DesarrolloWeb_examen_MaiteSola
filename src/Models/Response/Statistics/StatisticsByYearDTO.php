<?php
// src/DTO/StatisticsByYearDTO.php
namespace App\Models\Response\Statistics;

use Symfony\Component\Serializer\Annotation\SerializedName;

class StatisticsByYearDTO
{
    /** @var StatisticsByTypeDTO[] */
    #[SerializedName("statistics_by_type")]
    public array $statistics;

    public function __construct(
        public int $year,
        array $statistics = []
    ) {
        $this->statistics = $statistics;
    }
}
