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
use Symfony\Component\Validator\Constraints\File;

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
                'attr' => [
                    'class' => 'custom-input-bfi',
                ],
                'help' => 'résolution de 1920x1080 recommandé',
                'constraints' => [
                    new File([
                        'maxSize' => '5M',
                        'maxSizeMessage' => 'Doit peser moins de 5M',
                        'mimeTypes' => [
                            'image/png',
                            'image/jpeg',
                            'image/jpg',
                        ],
                        'mimeTypesMessage' => 'Doit être une image',
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
