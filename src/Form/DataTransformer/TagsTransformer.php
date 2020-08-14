<?php

namespace App\Form\DataTransformer;

use App\Entity\Tags;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;

class TagsTransformer implements DataTransformerInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    public function transform($value): string
    {
        return implode(',', $value);
    }

    public function reverseTransform($string): array
    {
        $names = array_unique(array_filter(array_map('trim', explode(',', $string))));
        $tags = $this->manager->getRepository(Tags::class)->findBy([
            'name' => $names,
        ]);
        $newNames = array_diff($names, $tags);
        foreach ($newNames as $name) {
            $tag = new Tags();
            $tag->setName($name);
            $tags[] = $tag;
        }

        return $tags;
    }
}
