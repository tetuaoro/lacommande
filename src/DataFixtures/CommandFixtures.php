<?php

namespace App\DataFixtures;

use App\Entity\Command;
use App\Repository\MealRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class CommandFixtures extends Fixture implements DependentFixtureInterface
{
    protected $mealRepo;

    public function __construct(MealRepository $mealRepository)
    {
        $this->mealRepo = $mealRepository;
    }

    public function load(ObjectManager $manager)
    {
        // $product = new Product();
        // $manager->persist($product);
        $faker = (new Factory())->create('fr_FR');
        $meals = $this->mealRepo->findAll();

        for ($i = 0; $i < 300; ++$i) {
            $command = new Command();
            $meals_ = $faker->randomElements($meals, mt_rand(1, 6));
            $details = [];

            $price = 0;
            /** @var \App\Entity\Meal $meal */
            foreach ($meals_ as $meal) {
                $price += $meal->getPrice();

                $command->addMeal($meal)
                    ->addProvider($meal->getProvider())
                ;

                $details[] = [
                    $meal->getId() => mt_rand(1, 6),
                ];

                $meal->commandPlus();
            }

            $string = str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ');
            $ref = $i.'-'.substr($string, 24).'-'.substr($string, 1, 2);

            $command->setName($faker->name)
                ->setDetails(array_replace(...$details))
                ->setReference($ref)
                ->setAddress('Paea')
                ->setEmail($faker->email)
                ->setPhone('87423498')
                ->setComment($faker->paragraph)
                ->setPrice($price)
                ->setCommandAt($faker->dateTimeBetween('-1 years', '+10 days', 'UTC'))
                ->setTimezone($faker->dateTime)
            ;

            $manager->persist($command);
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
