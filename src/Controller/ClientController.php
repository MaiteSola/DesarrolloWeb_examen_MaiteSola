<?php

// src/Controller/ClientController.php
namespace App\Controller;

use App\Entity\Client;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;

class ClientController extends AbstractController
{
    #[Route('/clients/{id}', name: 'get_client', methods: ['GET'])]
    public function show(
        Client $client,
        #[MapQueryParameter] bool $with_bookings = false,
        #[MapQueryParameter] bool $with_statistics = false
    ): JsonResponse {
        $groups = ['client:read'];

        // La lógica de qué mostrar se controla con los SerializedGroups o condicionales
        // Para este examen, usaremos los grupos definidos en la Entidad
        return $this->json($client, 200, [], ['groups' => $groups]);
    }
}
