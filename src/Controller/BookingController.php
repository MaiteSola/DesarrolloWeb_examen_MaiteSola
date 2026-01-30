<?php

// src/Controller/BookingController.php
namespace App\Controller;

use App\Models\Request\BookingInputDTO;
use App\Service\BookingService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;

class BookingController extends AbstractController
{
    #[Route('/bookings', name: 'post_booking', methods: ['POST'])]
    public function create(
        #[MapRequestPayload] BookingInputDTO $bookingInputDto,
        BookingService $bookingService
    ): JsonResponse {
        try {
            $booking = $bookingService->createBooking($bookingInputDto);

            // Usamos grupos para que la respuesta cumpla el YAML (id, activity, client_id)
            return $this->json($booking, 200, [], ['groups' => ['booking:read']]);
        } catch (\Exception $e) {
            return $this->json([
                'code' => $e->getCode() ?: 400,
                'description' => $e->getMessage()
            ], 400);
        }
    }
}
