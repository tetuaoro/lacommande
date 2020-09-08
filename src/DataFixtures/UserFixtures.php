<?php

namespace App\DataFixtures;

use App\Entity\Provider;
use App\Entity\User;
use App\Repository\CityRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    protected $password;
    protected $cityRepo;

    public function __construct(CityRepository $cityRepository, UserPasswordEncoderInterface $userPasswordEncoderInterface)
    {
        $this->password = $userPasswordEncoderInterface;
        $this->cityRepo = $cityRepository;
    }

    public function load(ObjectManager $manager)
    {
        $faker = (new Factory())->create('fr_FR');
        $cities = $this->cityRepo->findAll();

        // Create User as PROVIDER
        for ($i = 0; $i < 6; ++$i) {
            $user = new User();
            $provider = new Provider();

            $user->setName($faker->company)
                ->setEmail($faker->companyEmail)
                ->setUsername($faker->userName)
                ->setNtahiti($faker->md5)
                ->setPassword($this->password->encodePassword($user, 'LACOMMANDE'))
                ->setCreatedAt($faker->dateTimeBetween())
                ;

            if (1 == mt_rand(0, 1)) {
                $roles = $user->getRoles();
                $roles[] = 'ROLE_PROVIDER';
                $user->setRoles($roles);
            }

            $provider->setName($user->getName())
                ->setUrl('http://www.google.com')
                ->setCode('#'.$faker->ean8)
                ->setCity($faker->randomElement($cities))
                ->setOpentime($faker->dateTimeBetween())
                ->setClosetime($faker->dateTimeBetween())
                ->setCreatedAt($user->getCreatedAt())
                ;

            $user->setProvider($provider);

            $manager->persist($user);
        }
        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            CityFixtures::class,
        ];
    }
}
