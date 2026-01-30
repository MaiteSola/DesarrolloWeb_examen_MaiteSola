<?php

use App\Entity\Activity;
use App\Enum\ActivityTypeEnum;
use App\Kernel;

require_once dirname(__DIR__) . '/vendor/autoload_runtime.php';

return function (array $context) {
    $kernel = new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
    $kernel->boot();
    $container = $kernel->getContainer();
    $repo = $container->get('doctrine')->getRepository(Activity::class);

    // Test 1: Query with null type
    echo "--- Querying with TYPE = NULL ---\n";
    $activities = $repo->findFiltered(null, false, 1, 50, 'date_start', 'desc');

    $typesFound = [];
    foreach ($activities as $act) {
        $typesFound[$act->getType()->value] = ($typesFound[$act->getType()->value] ?? 0) + 1;
        // echo "ID: " . $act->getId() . " - Type: " . $act->getType()->value . " - Date: " . $act->getDateStart()->format('Y-m-d H:i') . "\n";
    }

    print_r($typesFound);

    // Test 2: Query with 'bodypump' type manually to compare
    // $enum = ActivityTypeEnum::BODYPUMP;
    // echo "\n--- Querying with TYPE = BODYPUMP ---\n";
    // $activitiesBodyPump = $repo->findFiltered($enum, false, 1, 10, 'date_start', 'desc');
    // echo "Count: " . count($activitiesBodyPump) . "\n";
};
