<?php
// src/Controller/ActivityController.php
namespace App\Controller;

use App\Repository\ActivityRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use App\Enum\ActivityTypeEnum;

class ActivityController extends AbstractController
{
    #[Route('/activities', name: 'get_activities', methods: ['GET'])]
    public function index(
        ActivityRepository $repository,
        #[MapQueryParameter] ?string $type = null,
        #[MapQueryParameter] bool $onlyfree = true,
        #[MapQueryParameter] int $page = 1,
        #[MapQueryParameter] int $page_size = 10,
        #[MapQueryParameter] string $sort = 'date_start',
        #[MapQueryParameter] string $order = 'desc'
    ): JsonResponse {
        // Validación del tipo
        $typeEnum = null;
        if ($type !== null && $type !== '') {
            $typeEnum = ActivityTypeEnum::tryFrom($type);
            if (!$typeEnum) {
                return $this->json(['error' => 'Tipo de actividad no válido'], 400);
            }
        }

        // Normalización del orden
        $order = strtolower($order) === 'asc' ? 'asc' : 'desc';

        $activities = $repository->findFiltered($typeEnum, $onlyfree, $page, $page_size, $sort, $order);

        return $this->json([
            'data' => $activities,
            'meta' => [
                'page' => $page,
                'limit' => $page_size,
                'total-items' => count($activities)
            ]
        ], 200, [], ['groups' => ['activity:read']]);
    }
}
