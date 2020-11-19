<?php

namespace App\Form\Type;

use App\Entity\Meal;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Image;

class MealType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'empty_data' => 'Arii Food',
            ])
            ->add('price', IntegerType::class, [
            ])
            ->add('preOrder', CheckboxType::class, [
                'required' => false,
            ])
            ->add('preOrderAt', DateTimeType::class, [
                'label' => false,
                'date_widget' => 'single_text',
                'view_timezone' => 'Pacific/Honolulu',
                'data' => new \DateTime('+1 hours'),
                'constraints' => [
                    new Assert\Range([
                        'min' => 'now',
                        'max' => '+2 month',
                    ]),
                ],
                'help' => 'help order',
                'help_attr' => [
                    'class' => 'mt-0 mb-2',
                ],
            ])
            ->add('stock', IntegerType::class, [
            ])
            ->add('tags', TagsType::class, [
            ])
            ->add('description', TextareaType::class, [
                'required' => false,
                'attr' => [
                    'class' => 'richTextEditor-hide',
                ],
            ])
            ->add('image', FileType::class, [
                'required' => true,
                'mapped' => false,
                'data_class' => null,
                'attr' => [
                    'placeholder' => 'une image carré',
                ],
                'label' => false,
                'label_attr' => [
                    'data-browse' => 'Ajouter une image',
                ],
                'help' => 'image : carré, 1920px max et 480 min',
                'help_attr' => [
                    'class' => 'pt-2',
                ],
                'constraints' => [
                    new Image([
                        'maxSize' => '5M',
                        'minWidth' => '480',
                        'minHeight' => '480',
                    ]),
                ],
            ])
        ;

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            /** @var \App\Entity\Meal $meal */
            $meal = $event->getData();

            if ($meal->getId()) {
                $event->getForm()->remove('image')
                    ->add('image', FileType::class, [
                        'required' => false,
                        'mapped' => false,
                        'data_class' => null,
                        'attr' => [
                            'placeholder' => $meal->getImgInfo()['metadata']['fullname'],
                        ],
                        'label' => false,
                        'label_attr' => [
                            'data-browse' => 'Modifier l\'image',
                        ],
                        'help' => 'image : carré, 1920px max et 480 min',
                        'constraints' => [
                            new Image([
                                'maxSize' => '5M',
                                'minWidth' => '480',
                                'minHeight' => '480',
                            ]),
                        ],
                    ])
                ;
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'translation_domain' => 'form',
            'data_class' => Meal::class,
            'attr' => [
                'id' => 'mealForm',
            ],
        ]);
    }
}
