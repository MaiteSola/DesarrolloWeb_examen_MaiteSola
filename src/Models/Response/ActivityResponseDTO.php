<?php

namespace App\Models\Response;

class ActivityResponseDTO
{
    public function __construct(
        public int $id,
        public string $type,
        public string $date_start,
        public int $max_participants,
        public int $clients_signed
    ) {}
}
