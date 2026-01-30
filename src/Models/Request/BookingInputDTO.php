<?php

namespace App\Models\Request;

use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

class BookingInputDTO
{
    public function __construct(
        #[Assert\NotBlank(message: "El ID del cliente no puede estar vacío")]
        #[Assert\Type('integer')]
        #[SerializedName("client_id")]
        public int $clientId,

        #[Assert\NotBlank(message: "El ID de la actividad no puede estar vacío")]
        #[Assert\Type('integer')]
        #[SerializedName("activity_id")]
        public int $activityId
    ) {}
}
