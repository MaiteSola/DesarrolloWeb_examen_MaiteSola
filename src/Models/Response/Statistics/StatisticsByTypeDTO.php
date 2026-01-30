<?php

namespace App\Models\Response\Statistics;

use Symfony\Component\Serializer\Annotation\SerializedName;

class StatisticsByTypeDTO
{
    public function __construct(
        public string $type,

        #[SerializedName("statistics")]
        public StatisticsDTO $statistics
    ) {}
}
