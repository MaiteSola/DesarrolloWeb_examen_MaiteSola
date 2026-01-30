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
use App\Entity\Booking;


#[ORM\Entity(repositoryClass: ActivityRepository::class)]
class Activity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['activity:read', 'client:read', 'booking:read'])]
    private ?int $id = null;

    // Usamos el Enum para el tipado estricto
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

    // Relación 1-M con Song (Una actividad tiene muchas canciones)
    #[ORM\OneToMany(mappedBy: 'activity', targetEntity: Song::class, orphanRemoval: true, cascade: ['persist'])]
    #[Groups(['activity:read'])]
    #[SerializedName("play_list")]
    private Collection $songs;

    #[ORM\OneToMany(mappedBy: 'activity', targetEntity: Booking::class)]
    private Collection $bookings;



    public function __construct()
    {
        $this->songs = new ArrayCollection();
        $this->bookings = new ArrayCollection();
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

    /**
     * @return Collection<int, Booking>
     */
    public function getBookings(): Collection
    {
        return $this->bookings;
    }

    public function addBooking(Booking $booking): static
    {
        if (!$this->bookings->contains($booking)) {
            $this->bookings->add($booking);
            $booking->setActivity($this);
        }

        return $this;
    }

    public function removeBooking(Booking $booking): static
    {
        if ($this->bookings->removeElement($booking)) {
            // set the owning side to null (unless already changed)
            if ($booking->getActivity() === $this) {
                $booking->setActivity(null);
            }
        }

        return $this;
    }

    #[Groups(['activity:read'])]
    #[SerializedName("clients_signed")]
    public function getClientsSigned(): int
    {
        return $this->bookings->count();
    }
}
