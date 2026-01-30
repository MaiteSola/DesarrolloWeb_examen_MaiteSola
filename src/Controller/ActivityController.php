<?php
// src/Controller/ActivityController.php
namespace App\Controller;

use App\Repository\ActivityRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;

class ActivityController extends AbstractController
{
    #[Route('/activities', name: 'get_activities', methods: ['GET'])]
    public function index(
        ActivityRepository $repository,
        #[MapQueryParameter] ?string $type = null,
        #[MapQueryParameter] bool $onlyfree = true,
        #[MapQueryParameter] int $page = 1,
        #[MapQueryParameter] int $page_size = 10
    ): JsonResponse {
        // El repositorio se encargará del filtrado real por BBDD
        $activities = $repository->findFiltered($type, $onlyfree, $page, $page_size);

        return $this->json([
            'data' => $activities,
            'meta' => [
                'page' => $page,
                'limit' => $page_size,
                'total-items' => count($activities) // Ajustar con count real
            ]
        ], 200, [], ['groups' => ['activity:read']]);
    }
}