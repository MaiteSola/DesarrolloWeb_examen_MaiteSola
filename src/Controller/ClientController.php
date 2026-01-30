<?php

// src/Controller/ClientController.php
namespace App\Controller;

use App\Entity\Client;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use App\Service\StatisticsService;

class ClientController extends AbstractController
{
    // src/Controller/ClientController.php
    #[Route('/clients/{id}', name: 'get_client', methods: ['GET'])]
    public function show(
        Client $client,
        StatisticsService $statsService,
        #[MapQueryParameter] bool $with_statistics = false,
        #[MapQueryParameter] bool $with_bookings = false
    ): JsonResponse {
        // Convertimos la entidad a array para manipularla o usamos Serializer dinámico
        $data = [
            'id' => $client->getId(),
            'name' => $client->getName(),
            'email' => $client->getEmail(),
            'type' => $client->getType()
        ];

        if ($with_bookings) {
            $data['activities_booked'] = $client->getBookings();
        }

        if ($with_statistics) {
            $data['activity_statistics'] = $statsService->getClientStatistics($client->getId());
        }

        return $this->json($data, 200, [], ['groups' => ['client:read', 'booking:read']]);
    }
}
