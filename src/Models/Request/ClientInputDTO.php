<?php

namespace App\Models\Request;

use App\Enum\ClientTypeEnum;
use Symfony\Component\Validator\Constraints as Assert;

class ClientInputDTO
{
    public function __construct(
        #[Assert\NotBlank(message: "El nombre es obligatorio")]
        public string $name,

        #[Assert\NotBlank]
        #[Assert\Email(message: "El formato de email no es válido")]
        public string $email,

        #[Assert\NotNull]
        public ClientTypeEnum $type
    ) {}
}
