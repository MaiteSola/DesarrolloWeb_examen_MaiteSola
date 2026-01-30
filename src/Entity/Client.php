<?php

namespace App\Entity;

use App\Enum\ClientTypeEnum;
use App\Repository\ClientRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ClientRepository::class)]
class Client
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['client:read', 'booking:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "El nombre es obligatorio")]
    #[Groups(['client:read', 'booking:read'])]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Email(message: "El formato del email no es válido")]
    #[Groups(['client:read'])]
    private ?string $email = null;

    #[ORM\Column(length: 20, type: 'string', enumType: ClientTypeEnum::class)]
    #[Assert\NotNull]
    #[Groups(['client:read'])]
    private ?ClientTypeEnum $type = ClientTypeEnum::STANDARD;


    #[ORM\OneToMany(mappedBy: 'client', targetEntity: Booking::class, orphanRemoval: true)]
    #[Groups(['client:read'])]
    private Collection $bookings;



    public function __construct()
    {
        $this->bookings = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;
        return $this;
    }

    public function getType(): ?ClientTypeEnum
    {
        return $this->type;
    }

    public function setType(ClientTypeEnum $type): static
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return Collection<int, Booking>
     */
    public function getBookings(): Collection
    {
        return $this->bookings;
    }

    // Cambio clave: Renombrado para cumplir el YAML
    #[Groups(['client:read'])]
    #[SerializedName("activities_booked")]
    public function getActivitiesBooked(): Collection
    {
        return $this->bookings;
    }

    // Campo virtual para cumplir con el YAML de estadísticas
    #[Groups(['client:read'])]
    #[SerializedName("activity_statistics")]
    public function getActivityStatistics(): array
    {
        return [];
    }
}
