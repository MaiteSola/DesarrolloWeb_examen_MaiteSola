<?php

use App\Entity\Client;
use App\Kernel;
use App\Service\StatisticsService;

require_once __DIR__ . '/vendor/autoload_runtime.php';

return function (array $context) {
    $kernel = new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
    $kernel->boot();
    $container = $kernel->getContainer();
    $repo = $container->get('doctrine')->getRepository(Client::class);
    $statsService = $container->get(StatisticsService::class);

    $clients = $repo->findAll();
    echo "--- Clientes Encontrados: " . count($clients) . " ---\n";

    foreach ($clients as $client) {
        $stats = $statsService->getClientStatistics($client->getId());
        $count = count($stats);
        echo "ID: " . $client->getId() . " | Nombre: " . $client->getName() . " | Stats Count: " . $count . "\n";

        if ($count > 0) {
            echo "   >>> ¡ENCONTRADO CLIENTE CON DATOS! USAR ID: " . $client->getId() . "\n";
            print_r($stats);
        }
    }
};
