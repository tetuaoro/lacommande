<?php

namespace App\DataFixtures;

use App\Entity\Gallery;
use App\Entity\Meal;
use App\Entity\Provider;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class MealFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = (new Factory())->create('fr_FR');
        $nb_providers = 10;
        $providers = [];
        $images = [];

        for ($i = 0; $i < 11; ++$i) {
            $images[] = 'https://lorempixel.com/640/480/food/'.$i.'/';
        }

        for ($i = 0; $i < $nb_providers; ++$i) {
            $provider = new Provider();

            $company = $faker->company;
            $provider->setName($company)
                ->setUrl('http://www.google.com')
                ->setCode('#'.$faker->ean8)
                ->setOpentime($faker->dateTimeBetween())
                ->setClosetime($faker->dateTimeBetween())
                ->setCreatedAt($faker->dateTimeBetween())
            ;

            $providers[] = $provider;

            $manager->persist($provider);
        }

        for ($i = 0; $i < 30; ++$i) {
            $meal = new Meal();
            $gallery = new Gallery();

            $paragraphs_recipe = '';
            foreach ($faker->paragraphs(mt_rand(1, 6)) as $key => $value) {
                $paragraphs_recipe .= '<p>'.$value.'</p>';
            }

            $paragraphs_description = '';
            foreach ($faker->paragraphs(mt_rand(1, 3)) as $key => $value) {
                $paragraphs_description .= '<p>'.$value.'</p>';
            }

            $name = ucfirst($faker->word);
            $images_ = $faker->randomElements($images, 4);
            $meal->setName($name)
                ->setProvider($faker->randomElement($providers))
                ->setPrice(mt_rand(500, 2390))
                ->setRecipe($paragraphs_recipe)
                ->setDescription($paragraphs_description)
                ->setPicture($images_)
                ->setCreatedAt($faker->dateTimeBetween())
            ;

            $gallery->setName($faker->sha1)
                ->setType('image')
                ->setUrl($images_[0])
                ->setCreatedAt($faker->dateTimeBetween())
            ;

            $manager->persist($meal);
            $manager->persist($gallery);
        }

        $manager->flush();
    }
}
