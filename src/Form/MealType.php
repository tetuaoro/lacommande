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
use Symfony\Component\Validator\Constraints\Image;

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
                'required' => false,
            ])
            ->add('recipe', TextareaType::class, [
                'translation_domain' => 'form',
                'required' => false,
            ])
            ->add('image', FileType::class, [
                'translation_domain' => 'form',
                'required' => true,
                'mapped' => false,
                'data_class' => null,
                'label' => false,
                'label_attr' => [
                    'class' => 'custom-label-bfi'
                ],
                'attr' => [
                    'class' => 'custom-input-bfi',
                ],
                'help' => 'image : carré, 1920px max et 480 min',
                'constraints' => [
                    new Image([
                        'maxSize' => '5M',
                        'minWidth' => '480',
                        'minHeight' => '480',
                        'maxWidth' => '1920',
                        'maxHeight' => '1920',
                        'allowLandscape' => false,
                        'allowPortrait' => false,
                    ]),
                ],
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
