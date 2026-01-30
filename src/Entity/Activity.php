<?php

namespace App\Entity;

use App\Enum\ActivityTypeEnum;
use App\Repository\ActivityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ActivityRepository::class)]
class Activity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['activity:read', 'client:read', 'booking:read'])]
    private ?int $id = null;

    // Usamos el Enum para el tipado estricto que pide el examen [cite: 46, 47]
    #[ORM\Column(length: 20, type: 'string', enumType: ActivityTypeEnum::class)]
    #[Assert\NotBlank(message: "El tipo de actividad es obligatorio")]
    #[Groups(['activity:read', 'client:read', 'booking:read'])]
    private ?ActivityTypeEnum $type = null;

    #[ORM\Column]
    #[Assert\NotNull]
    #[Assert\Positive(message: "El número de participantes debe ser mayor a 0")]
    #[Groups(['activity:read'])]
    private ?int $max_participants = null;

    #[ORM\Column]
    #[Assert\NotNull]
    #[Assert\Type("\DateTimeImmutable")]
    #[Groups(['activity:read', 'booking:read'])]
    private ?\DateTimeImmutable $date_start = null;

    #[ORM\Column]
    #[Assert\NotNull]
    #[Assert\Type("\DateTimeImmutable")]
    #[Assert\GreaterThan(propertyPath: "date_start", message: "La fecha de fin debe ser posterior a la de inicio")]
    #[Groups(['activity:read'])]
    private ?\DateTimeImmutable $date_end = null;

    // Relación 1-M con Song (Una actividad tiene muchas canciones) [cite: 37, 517]
    #[ORM\OneToMany(mappedBy: 'activity', targetEntity: Song::class, orphanRemoval: true, cascade: ['persist'])]
    #[Groups(['activity:read'])]
    #[SerializedName("play_list")]
    private Collection $songs;

    public function __construct()
    {
        $this->songs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?ActivityTypeEnum
    {
        return $this->type;
    }

    public function setType(ActivityTypeEnum $type): static
    {
        $this->type = $type;
        return $this;
    }

    public function getMaxParticipants(): ?int
    {
        return $this->max_participants;
    }

    public function setMaxParticipants(int $max_participants): static
    {
        $this->max_participants = $max_participants;
        return $this;
    }

    public function getDateStart(): ?\DateTimeImmutable
    {
        return $this->date_start;
    }

    public function setDateStart(\DateTimeImmutable $date_start): static
    {
        $this->date_start = $date_start;
        return $this;
    }

    public function getDateEnd(): ?\DateTimeImmutable
    {
        return $this->date_end;
    }

    public function setDateEnd(\DateTimeImmutable $date_end): static
    {
        $this->date_end = $date_end;
        return $this;
    }

    /**
     * @return Collection<int, Song>
     */
    public function getSongs(): Collection
    {
        return $this->songs;
    }

    public function addSong(Song $song): static
    {
        if (!$this->songs->contains($song)) {
            $this->songs->add($song);
            $song->setActivity($this);
        }
        return $this;
    }

    public function removeSong(Song $song): static
    {
        if ($this->songs->removeElement($song)) {
            if ($song->getActivity() === $this) {
                $song->setActivity(null);
            }
        }
        return $this;
    }

    // Helper para el requisito de mostrar cuántos clientes hay apuntados [cite: 78]
    #[Groups(['activity:read'])]
    public function getClientsSigned(): int
    {
        // Esto lo vincularemos cuando creemos la entidad Booking
        return 0;
    }
}
