<?php

namespace App\Models\Response\Statistics;

use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;

class StatisticsByTypeDTO
{
    public function __construct(
        #[Groups(['client:read'])]
        public string $type,

        #[SerializedName("statistics")]
        #[Groups(['client:read'])]
        public StatisticsDTO $statistics
    ) {}
}
