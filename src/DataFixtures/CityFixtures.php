<?php

namespace App\DataFixtures;

use App\Entity\City;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class CityFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        // $product = new Product();
        // $manager->persist($product);
        $faker = (new Factory())->create('fr_FR');

        for ($i = 0; $i < 10; ++$i) {
            $city = new City();

            $city->setName($faker->country)
                ->setCode($faker->countryCode)
            ;

            $manager->persist($city);
        }

        $manager->flush();
    }
}
