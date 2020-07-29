<?php

namespace App\Form;

use App\Entity\Meal;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MealType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'translation_domain' => 'form',
            ])
            ->add('price', NumberType::class, [
                'translation_domain' => 'form',
            ])
            ->add('description', TextareaType::class, [
                'translation_domain' => 'form',
            ])
            ->add('picture', FileType::class, [
                'translation_domain' => 'form',
                'required' => false,
                'multiple' => true,
                'label' => false,
                'label_attr' => [
                    'data-browse' => 'Choisir',
                ],
                'attr' => [
                    'class' => 'custom-form-am',
                    'placeholder' => 'Choisir une image',
                    'accept' => 'image/*',
                ],
            ])
            ->add('recipe', TextareaType::class, [
                'translation_domain' => 'form',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Meal::class,
        ]);
    }
}
