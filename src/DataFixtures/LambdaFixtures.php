<?php

namespace App\DataFixtures;

use App\Repository\LambdaRepository;
use App\Repository\MealRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class LambdaFixtures extends Fixture implements DependentFixtureInterface
{
    protected $mealRepo;
    protected $lambdaRepo;

    public function __construct(MealRepository $mealRepository, LambdaRepository $lambdaRepository)
    {
        $this->mealRepo = $mealRepository;
        $this->lambdaRepo = $lambdaRepository;
    }

    public function load(ObjectManager $manager)
    {
        $faker = (new Factory())->create('fr_FR');
        $meals = $this->mealRepo->findAll();
        $lambdas = $this->lambdaRepo->findAll();

        /** @var \App\Entity\Lambda $lambda */
        foreach ($lambdas as $lambda) {
            /** @var \App\Entity\Meal $meal */
            foreach ($faker->randomElements($meals, mt_rand(1, 80)) as $meal) {
                $lambda->addMeal($meal);
            }
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            MealFixtures::class,
        ];
    }
}
