<?php

namespace App\DataFixtures;

use App\Entity\Delivery;
use App\Entity\Lambda;
use App\Entity\Provider;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    protected $password;

    public function __construct(UserPasswordEncoderInterface $userPasswordEncoderInterface)
    {
        $this->password = $userPasswordEncoderInterface;
    }

    public function load(ObjectManager $manager)
    {
        $faker = (new Factory())->create('fr_FR');
        $env = 'dev' == $_ENV['APP_ENV'];

        // Create User as PROVIDER
        for ($i = 0; $i < 30; ++$i) {
            $user = new User();

            $user->setName($faker->company)
                ->setEmail($faker->companyEmail)
                ->setUsername($faker->userName)
                ->setNtahiti($faker->md5)
                ->setPassword($this->password->encodePassword($user, 'LACOMMANDE'))
                ->setCreatedAt($faker->dateTimeBetween())
                ;

            $rdm = mt_rand(0, 2);
            $roles = $user->getRoles();

            if (1 == $rdm) {
                $provider = new Provider();
                $roles[] = 'ROLE_PROVIDER';
                $provider->setName($user->getName())
                    ->setMinPriceDelivery(2500)
                    ->setBitly(['link' => $env ? 'https://bit.ly/2ZDJRpF' : 'https://bit.ly/2ZDJHyz'])
                    ->setOpenHours([
                        'monday' => ['09:00-12:00', '13:00-18:00'],
                        'tuesday' => ['09:00-12:00', '13:00-18:00'],
                        'wednesday' => ['09:00-12:00'],
                        'thursday' => ['09:00-12:00', '13:00-18:00'],
                        'friday' => ['09:00-12:00', '13:00-20:00'],
                        'saturday' => ['09:00-12:00', '13:00-16:00'],
                        'sunday' => [],
                    ])
                    ->setLinkfb('https://www.facebook.com')
                    ->setLinktwitter('https://www.twitter.com')
                    ->setLinkinsta('https://www.instagram.com')
                    ->setCreatedAt($user->getCreatedAt())
                ;
                $user->setRoles($roles)
                    ->setProvider($provider)
                ;
            }
            if (2 == $rdm) {
                $lambda = new Lambda();
                $roles[] = 'ROLE_LAMBDA';
                $lambda->setName($user->getName());
                $user->setRoles($roles)
                    ->setLambda($lambda)
                ;
            }
            if (0 == $rdm) {
                $delivery = new Delivery();
                $roles[] = 'ROLE_DELIVERY';
                $delivery->setName($user->getName())

                ;
                $user->setRoles($roles)
                    ->setDelivery($delivery)
                    ;
            }

            $manager->persist($user);
        }

        $user = new User();
        $provider = new Provider();
        $roles = $user->getRoles();
        $user->setName('Rao Nagos')
            ->setEmail('tetuaoropro@mgail.com')
            ->setUsername('raonagos98')
            ->setNtahiti('D75938')
            ->setPassword($this->password->encodePassword($user, 'LACOMMANDE'))
        ;
        $roles[] = 'ROLE_PROVIDER';
        $roles[] = 'ROLE_ADMIN';
        $roles[] = 'ROLE_SUPERADMIN';
        $provider->setName($user->getName())
            ->setMinPriceDelivery(1500)
            ->setBitly(['link' => $env ? 'https://bit.ly/2ZDJRpF' : 'https://bit.ly/2ZDJHyz'])
            ->setOpenHours([
                'monday' => ['09:00-12:00', '13:00-18:00'],
                'tuesday' => ['09:00-12:00', '13:00-18:00'],
                'wednesday' => ['09:00-12:00'],
                'thursday' => ['09:00-12:00', '13:00-18:00'],
                'friday' => ['09:00-12:00', '13:00-20:00'],
                'saturday' => ['09:00-12:00', '13:00-16:00'],
                'sunday' => [],
            ])
            ->setLinkfb('https://www.facebook.com')
            ->setLinktwitter('https://www.twitter.com')
            ->setLinkinsta('https://www.instagram.com')
        ;
        $user->setRoles($roles)
            ->setProvider($provider)
        ;
        $manager->persist($user);
        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            CityFixtures::class,
        ];
    }
}
