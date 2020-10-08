<?php

namespace App\DataFixtures;

use App\Entity\Notification;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $notif = (new Notification())
            ->setTitle('Bienvenue')
            ->setMessage(`<p>Iaorana,</p>
        <p></p>
        <p>Arii Food vous souhaite la bienvenue et vous remercie d'avoir choisi nos services. Jusqu'en février 2021, la plateforme sera gratuite et nous permettra d'essayer la configuration en place afin de répondre à vos besoin. Nous restons à votre disposition via notre mail professionnel : support@ariifood.pf.</p>
        <p></p>
        <p>Arii Food,</p>`)
        ;

        $manager->persist($notif);
        $manager->flush();
    }
}
