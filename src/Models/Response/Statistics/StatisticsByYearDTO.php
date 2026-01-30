<?php
// src/DTO/StatisticsByYearDTO.php
namespace App\Models\Response\Statistics;

use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;

class StatisticsByYearDTO
{
    /** @var StatisticsByTypeDTO[] */
    #[SerializedName("statistics_by_type")]
    #[Groups(['client:read'])]
    public array $statistics;

    public function __construct(
        #[Groups(['client:read'])]
        public int $year,
        array $statistics = []
    ) {
        $this->statistics = $statistics;
    }
}
