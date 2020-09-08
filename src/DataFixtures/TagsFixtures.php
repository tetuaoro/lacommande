<?php

namespace App\DataFixtures;

use App\Entity\Tags;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class TagsFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        // $product = new Product();
        // $manager->persist($product);
        $faker = (new Factory())->create('fr_FR');

        for ($i = 0; $i < 10; ++$i) {
            $tag = new Tags();
            $tag->setName($faker->word);

            $manager->persist($tag);
        }

        $manager->flush();
    }
}
