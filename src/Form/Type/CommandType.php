<?php

namespace App\Form\Type;

use App\Entity\Command;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class CommandType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'translation_domain' => 'form',
                'required' => false,
                'label' => false,
                'attr' => [
                    'placeholder' => 'nom',
                ],
            ])
            ->add('address', TextType::class, [
                'translation_domain' => 'form',
                'required' => true,
                'label' => false,
                'attr' => [
                    'placeholder' => 'address',
                ],
            ])
            ->add('phone', TelType::class, [
                'translation_domain' => 'form',
                'required' => true,
                'label' => false,
                'attr' => [
                    'placeholder' => 'tel',
                    'pattern' => '(87|89|40)[0-9]{6}',
                ],
            ])
            ->add('email', EmailType::class, [
                'translation_domain' => 'form',
                'required' => true,
                'label' => false,
                'attr' => [
                    'placeholder' => 'email',
                ],
            ])
            ->add('commandAt', DateTimeType::class, [
                'translation_domain' => 'form',
                'required' => true,
                'label' => false,
                'date_widget' => 'single_text',
                'time_widget' => 'single_text',
                'view_timezone' => 'Pacific/Honolulu',
                'data' => new \DateTime('+1 hours'),
                'constraints' => [
                    new Assert\GreaterThan('+1 hours'),
                    new Assert\Range([
                        'min' => 'today',
                        'max' => '+1 month',
                    ]),
                ],
                'help' => 'help order',
                'help_attr' => [
                    'class' => 'mt-0 mb-2',
                ],
            ])
            ->add('comment', TextareaType::class, [
                'translation_domain' => 'form',
                'required' => false,
                'label' => false,
                'attr' => [
                    'placeholder' => 'Information pour la commande',
                ],
            ])
            ->add('code', TextType::class, [
                'translation_domain' => 'form',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Entrer votre code promo',
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
            ->add('stock', HiddenType::class, [
                'mapped' => false,
                'required' => false,
                'error_bubbling' => false,
                'attr' => [
                    'class' => 'stock',
                ],
            ])
            ->add('min', HiddenType::class, [
                'mapped' => false,
                'required' => false,
                'error_bubbling' => false,
                'attr' => [
                    'class' => 'min',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Command::class,
        ]);
    }
}
