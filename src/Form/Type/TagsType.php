<?php

namespace App\Form\Type;

use App\Form\DataTransformer\TagsTransformer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\DataTransformer\CollectionToArrayTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
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
            ->addModelTransformer(new CollectionToArrayTransformer(), true)
            ->addModelTransformer(new TagsTransformer($this->manager), true)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('attr', [
            'class' => 'tags-input',
            'data-role' => 'tagsinput',
        ]);
        $resolver->setDefault('required', false);
    }

    public function getParent(): string
    {
        return TextType::class;
    }
}
