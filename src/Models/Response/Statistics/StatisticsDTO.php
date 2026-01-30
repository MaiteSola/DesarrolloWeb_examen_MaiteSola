<?php

namespace App\Models\Response\Statistics;

use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;

class StatisticsDTO
{
    public function __construct(
        #[SerializedName("num_activities")]
        #[Groups(['client:read'])]
        public int $numActivities,

        #[SerializedName("num_minutes")]
        #[Groups(['client:read'])]
        public int $numMinutes
    ) {}
}
