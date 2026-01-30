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
        // 1. CANCIONES
        $song1 = (new Song())->setName('La Morocha')->setDurationSeconds(245);
        $song2 = (new Song())->setName('Despacito')->setDurationSeconds(210);
        $manager->persist($song1);
        $manager->persist($song2);

        // 2. ACTIVIDADES (Happy Path y Límite de Cupo)
        $act1 = (new Activity())
            ->setType(ActivityTypeEnum::BODYPUMP)
            ->setMaxParticipants(25)
            ->setDateStart(new \DateTimeImmutable('next monday 10:00'))
            ->setDateEnd(new \DateTimeImmutable('next monday 11:00'));
        $act1->addSong($song1);
        $act1->addSong($song2); // Added song2 to act1 as well or separate? Let's just fix the method calls first.

        $actFull = (new Activity())
            ->setType(ActivityTypeEnum::SPINNING)
            ->setMaxParticipants(1) // Para probar error de cupo
            ->setDateStart(new \DateTimeImmutable('next tuesday 18:00'))
            ->setDateEnd(new \DateTimeImmutable('next tuesday 19:00'));

        $manager->persist($act1);
        $manager->persist($actFull);

        // 3. CLIENTES (Standard vs Premium)
        $clientStd = (new Client())
            ->setName('Juan Standard')
            ->setEmail('juan@gym.com')
            ->setType(ClientTypeEnum::STANDARD);

        $clientPre = (new Client())
            ->setName('Marta Premium')
            ->setEmail('marta@gym.com')
            ->setType(ClientTypeEnum::PREMIUM);

        $manager->persist($clientStd);
        $manager->persist($clientPre);

        // 4. RESERVAS PREVIAS (Para estadísticas y límites)
        // Juan ya tiene una reserva esta semana
        $booking = (new Booking())
            ->setClient($clientStd)
            ->setActivity($act1);
        $manager->persist($booking);

        $manager->flush();
    }
}
