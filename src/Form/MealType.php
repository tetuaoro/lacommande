<?php

namespace App\Form;

use App\Entity\Meal;
use App\Entity\Tags;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;

class MealType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'translation_domain' => 'form',
                'empty_data' => 'Arii Food',
            ])
            ->add('price', NumberType::class, [
                'translation_domain' => 'form',
            ])
            ->add('tags', CollectionType::class, [
                'translation_domain' => 'form',
                'entry_type' => EntityType::class,
                'entry_options' => [
                    'class' => Tags::class,
                    'choice_label' => 'name',
                ],
                'allow_add' => true,
                'allow_delete' => true,
                'prototype_name' => 'select',
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
                'attr' => [
                    'placeholder' => 'une image',
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
                        'maxWidth' => '1920',
                        'maxHeight' => '1920',
                        'allowLandscape' => false,
                        'allowPortrait' => false,
                    ]),
                ],
            ])
            ->add('recaptcha', HiddenType::class, [
                'mapped' => false,
                'required' => true,
                'error_bubbling' => false,
                'attr' => [
                    'class' => 'recaptcha-check',
                    'data-sitekey' => $_ENV['RECAPTCHA_KEY_3'],
                ],
            ])
        ;

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            /** @var \App\Entity\Meal $meal */
            $meal = $event->getData();

            if ($meal->getId()) {
                $event->getForm()->remove('image')
                    ->add('image', FileType::class, [
                        'translation_domain' => 'form',
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
                                'maxWidth' => '1920',
                                'maxHeight' => '1920',
                                'allowLandscape' => false,
                                'allowPortrait' => false,
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
            'data_class' => Meal::class,
        ]);
    }
}
