<?php

namespace App\DataFixtures;

use App\Entity\Gallery;
use App\Entity\Meal;
use App\Repository\ProviderRepository;
use App\Repository\TagsRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class MealFixtures extends Fixture implements DependentFixtureInterface
{
    protected $providerRepo;
    protected $tagsRepo;

    public function __construct(ProviderRepository $providerRepository, TagsRepository $tagsRepository)
    {
        $this->providerRepo = $providerRepository;
        $this->tagsRepo = $tagsRepository;
    }

    public function load(ObjectManager $manager)
    {
        // $product = new Product();
        // $manager->persist($product);

        $faker = (new Factory())->create('fr_FR');
        $images = [];

        for ($i = 0; $i < 11; ++$i) {
            $images[] = 'https://lorempixel.com/1920/1920/food/'.$i.'/';
        }

        $providers = $this->providerRepo->findAll();
        $tags = $this->tagsRepo->findAll();

        for ($i = 0; $i < 250; ++$i) {
            $meal = new Meal();
            $gallery = new Gallery();

            $paragraphs_recipe = '';
            foreach ($faker->paragraphs(mt_rand(1, 3)) as $value) {
                $paragraphs_recipe .= '<p>'.$value.'</p>';
            }

            $paragraphs_description = '';
            foreach ($faker->paragraphs(mt_rand(1, 3)) as $key => $value) {
                $paragraphs_description .= '<p>'.$value.'</p>';
            }

            $name = ucfirst($faker->word);

            $meal->setName($name)
                ->setProvider($faker->randomElement($providers))
                ->setPrice($faker->numberBetween(100, 7000))
                ->setStock($faker->numberBetween(0, 500))
                ->setRecipe($paragraphs_recipe)
                ->setDescription($paragraphs_description)
                ->setImg($faker->randomElement($images))
                ->setImgInfo([
                    'name' => 'Aito.jpg',
                    'size' => '200px',
                    'metadata' => [
                        'fullname' => 'Aito.jpg',
                    ],
                ])
                ->setCreatedAt($faker->dateTimeBetween())
            ;

            $tags_ = $faker->randomElements($tags, mt_rand(1, 5));
            /** @var \App\Entity\Tags $tag */
            foreach ($tags_ as $tag) {
                $meal->addTag($tag);
            }

            $gallery
                ->setUrl($meal->getImg())
                ->setCreatedAt($faker->dateTimeBetween())
            ;

            $meal->setGallery($gallery);

            $manager->persist($meal);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            TagsFixtures::class,
            UserFixtures::class,
        ];
    }
}
