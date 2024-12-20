<?php

namespace App\Form\Type;

use App\Entity\Tags;
use App\Form\EventListener\TagsSubscriber;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TagsType extends AbstractType
{
    /**
     * @var EntityManagerInterface
     */
    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->addEventSubscriber(new TagsSubscriber($this->manager))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'required' => false,
            'class' => Tags::class,
            'multiple' => true,
            'attr' => [
                'class' => 'tags-input',
            ],
        ]);
    }

    public function getParent(): string
    {
        return EntityType::class;
    }
}
