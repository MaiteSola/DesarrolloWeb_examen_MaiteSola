<?php

// src/Service/BookingService.php
namespace App\Service;

use App\Entity\Booking;
use App\Enum\ClientTypeEnum;
use App\Repository\ActivityRepository;
use App\Repository\BookingRepository;
use App\Repository\ClientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use App\Models\Request\BookingInputDTO;

class BookingService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ClientRepository $clientRepository,
        private ActivityRepository $activityRepository,
        private BookingRepository $bookingRepository
    ) {}

    public function createBooking(BookingInputDTO $dto): Booking
    {
        // 1. Validar existencia de Cliente y Actividad [cite: 1496]
        $client = $this->clientRepository->find($dto->clientId);
        $activity = $this->activityRepository->find($dto->activityId);

        if (!$client || !$activity) {
            throw new \Exception("Cliente o Actividad no encontrados", 404);
        }

        // 2. Validar plazas disponibles [cite: 1497]
        // Suponiendo que implementamos el conteo de reservas actuales
        $currentBookings = count($this->bookingRepository->findBy(['activity' => $activity]));
        if ($currentBookings >= $activity->getMaxParticipants()) {
            throw new \Exception("No hay plazas disponibles para esta actividad", 400);
        }

        // 3. Validar reglas por tipo de usuario [cite: 1498]
        if ($client->getType() === ClientTypeEnum::STANDARD) {
            // Límite de 2 actividades por semana (Lunes-Domingo) 
            $weeklyCount = $this->bookingRepository->countByClientInWeek($client, $activity->getDateStart());

            if ($weeklyCount >= 2) {
                throw new \Exception("Los usuarios standard no pueden reservar más de 2 actividades por semana", 400);
            }
        }

        // 4. Crear y persistir la reserva 
        $booking = new Booking();
        $booking->setClient($client);
        $booking->setActivity($activity);

        $this->entityManager->persist($booking);
        $this->entityManager->flush();

        return $booking;
    }
}
