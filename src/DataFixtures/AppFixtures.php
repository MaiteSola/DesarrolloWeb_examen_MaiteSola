<?php

namespace App\DataFixtures;

use App\Entity\Activity;
use App\Entity\Client;
use App\Entity\Song;
use App\Entity\Booking;
use App\Enum\ActivityTypeEnum;
use App\Enum\ClientTypeEnum;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // 1. REPERTORIO DE CANCIONES (Variedad de géneros y duraciones)
        $songs = [];
        $songData = [
            ['Eye of the Tiger', 300],
            ['Thunderstruck', 292],
            ['Physical', 223],
            ['Maniac', 244],
            ['Stronger', 311],
            ['Pump It', 213],
            ['Flowers', 200],
            ['Blinding Lights', 200],
            ['Despechá', 157]
        ];

        foreach ($songData as $data) {
            $song = (new Song())->setName($data[0])->setDurationSeconds($data[1]);
            $manager->persist($song);
            $songs[] = $song;
        }

        // 2. CLIENTES (Añadimos más perfiles para probar el GET /clients)
        $juan = (new Client())->setName('Juan Standard')->setEmail('juan@gym.com')->setType(ClientTypeEnum::STANDARD);
        $marta = (new Client())->setName('Marta Premium')->setEmail('marta@gym.com')->setType(ClientTypeEnum::PREMIUM);
        $pedro = (new Client())->setName('Pedro Novato')->setEmail('pedro@gym.com')->setType(ClientTypeEnum::STANDARD);
        $lucia = (new Client())->setName('Lucia Elite')->setEmail('lucia@gym.com')->setType(ClientTypeEnum::PREMIUM);

        $manager->persist($juan);
        $manager->persist($marta);
        $manager->persist($pedro);
        $manager->persist($lucia);

        // 3. GENERACIÓN MASIVA DE ACTIVIDADES PASADAS (2023, 2024, 2025)
        $years = [2023, 2024, 2025];
        $types = ActivityTypeEnum::cases();
        $pastActivities = [];

        foreach ($years as $year) {
            foreach ($types as $type) {
                // Creamos 3 sesiones de cada tipo por cada año
                for ($i = 1; $i <= 3; $i++) {
                    $activity = (new Activity())
                        ->setType($type)
                        ->setMaxParticipants(rand(10, 30))
                        ->setDateStart(new \DateTimeImmutable("$year-" . rand(1, 12) . "-" . rand(1, 28) . " 10:00:00"))
                        ->setDateEnd(new \DateTimeImmutable("$year-01-01 11:00:00")); // La hora final no afecta al cálculo de minutos de canciones

                    // Añadimos 2 canciones aleatorias a cada actividad
                    $activity->addSong($songs[array_rand($songs)]);
                    $activity->addSong($songs[array_rand($songs)]);

                    $manager->persist($activity);
                    $pastActivities[] = $activity;

                    // Asignamos reservas aleatorias a estas actividades pasadas para generar estadísticas
                    $clients = [$juan, $marta, $pedro, $lucia];
                    foreach ($clients as $client) {
                        if (rand(0, 1)) { // 50% de probabilidad de que el cliente asistiera
                            $manager->persist((new Booking())->setClient($client)->setActivity($activity));
                        }
                    }
                }
            }
        }

        // 4. ACTIVIDADES PARA PROBAR PAGINACIÓN Y FILTROS (Próxima semana)
        // Creamos 15 actividades de TIPOS VARIADOS para probar la paginación sin confundir el filtro
        for ($i = 1; $i <= 15; $i++) {
            $futureAct = (new Activity())
                ->setType($types[array_rand($types)]) // Tipo aleatorio
                ->setMaxParticipants(20)
                ->setDateStart(new \DateTimeImmutable("next monday +$i hours"))
                ->setDateEnd(new \DateTimeImmutable("next monday +" . ($i + 1) . " hours"));

            $futureAct->addSong($songs[0]);
            $manager->persist($futureAct);
        }

        // 5. CASO LÍMITE: ACTIVIDAD COMPLETAMENTE LLENA
        $fullAct = (new Activity())
            ->setType(ActivityTypeEnum::SPINNING)
            ->setMaxParticipants(2)
            ->setDateStart(new \DateTimeImmutable('next Wednesday 18:00'))
            ->setDateEnd(new \DateTimeImmutable('next Wednesday 19:00'));

        $manager->persist($fullAct);
        $manager->persist((new Booking())->setClient($juan)->setActivity($fullAct));
        $manager->persist((new Booking())->setClient($marta)->setActivity($fullAct));

        $manager->flush();
    }
}
