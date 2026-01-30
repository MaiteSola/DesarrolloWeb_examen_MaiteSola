<?php

namespace App\Models\Response\Statistics;

use Symfony\Component\Serializer\Annotation\SerializedName;

class StatisticsDTO
{
    public function __construct(
        #[SerializedName("num_activities")]
        public int $numActivities,

        #[SerializedName("num_minutes")]
        public int $numMinutes
    ) {}
}
