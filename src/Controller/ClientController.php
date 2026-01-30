<?php

// src/Controller/ClientController.php
namespace App\Controller;

use App\Entity\Client;
use App\Repository\ClientRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use App\Service\StatisticsService;

class ClientController extends AbstractController
{


    #[Route('/clients/{id}', name: 'get_client', methods: ['GET'])]
    public function show(
        int $id,
        ClientRepository $clientRepository,
        StatisticsService $statsService,
        #[MapQueryParameter] bool $with_statistics = false,
        #[MapQueryParameter] bool $with_bookings = false
    ): JsonResponse {
        $client = $clientRepository->find($id);

        if (!$client) {
            return $this->json(['code' => 404, 'description' => 'Cliente no encontrado'], 404);
        }

        // 1. Datos básicos (siempre se devuelven)
        $data = [
            'id' => $client->getId(),
            'name' => $client->getName(),
            'email' => $client->getEmail(),
            'type' => $client->getType()
        ];

        // 2. Lógica para Reservas
        if ($with_bookings) {
            // Symfony serializará la colección de bookings automáticamente
            $data['activities_booked'] = $client->getBookings();
        }

        // 3. Lógica para Estadísticas
        if ($with_statistics) {
            $data['activity_statistics'] = $statsService->getClientStatistics($client->getId());
        }

        // Usamos los grupos de serialización para formatear la respuesta según el YAML
        return $this->json($data, 200, [], [
            'groups' => ['client:read', 'booking:read', 'activity:read']
        ]);
    }
}
