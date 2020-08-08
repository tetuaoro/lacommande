<?php

namespace App\DataFixtures;

use App\Entity\Command;
use App\Entity\Gallery;
use App\Entity\Meal;
use App\Entity\Provider;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = (new Factory())->create('fr_FR');
        $nb_users = 6;
        $nb_meals = 39;
        $nb_commands = 90;
        $users = [];
        $providers = [];
        $images = [];
        $meals = [];

        // Random LoremPixel Images
        for ($i = 0; $i < 11; ++$i) {
            $images[] = 'https://lorempixel.com/1920/1920/food/'.$i.'/';
        }

        // Create User
        for ($i = 0; $i < $nb_users; ++$i) {
            $user = new User();
            $provider = new Provider();
            $user->setName($faker->company)
                ->setEmail($faker->companyEmail)
                ->setUsername($faker->userName)
                ->setNtahiti($faker->md5)
                ->setPassword($faker->password)
                ->setCreatedAt($faker->dateTimeBetween())
                ;
            $users[] = $user;

            $provider->setName($user->getName())
                ->setUrl('http://www.google.com')
                ->setCode('#'.$faker->ean8)
                ->setOpentime($faker->dateTimeBetween())
                ->setClosetime($faker->dateTimeBetween())
                ->setCreatedAt($user->getCreatedAt())
                ;

            $providers[] = $provider;
            $user->setProvider($provider);

            $manager->persist($user);
        }

        for ($i = 0; $i < $nb_meals; ++$i) {
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
            $img = $faker->randomElement($images_);

            $meal->setName($name)
                ->setProvider($faker->randomElement($providers))
                ->setPrice($faker->numberBetween(500, 7000))
                ->setRecipe($paragraphs_recipe)
                ->setDescription($paragraphs_description)
                ->setImg($img)
                ->setImgInfo([
                    'name' => 'Aito.jpg',
                    'size' => '200px',
                ])
                ->setPicture($images_)
                ->setCreatedAt($faker->dateTimeBetween())
            ;

            $gallery->setName($faker->sha1)
                ->setType('image')
                ->setUrl($images_[0])
                ->setCreatedAt($faker->dateTimeBetween())
            ;

            $meals[] = $meal;
            $meal->setGallery($gallery);

            $manager->persist($meal);
        }

        for ($i = 0; $i < $nb_commands; ++$i) {
            $command = new Command();

            $meal_ = $faker->randomElement($meals);

            $command->setName($faker->sha1)
                ->setItems($faker->numberBetween(1, 10))
                ->setCreatedAt($faker->dateTimeBetween())
                ->addMeals($meal_)
            ;

            $manager->persist($command);
        }

        $manager->flush();
    }
}
