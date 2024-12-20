<?php

namespace App\Form\Type;

use App\Entity\Command;
use App\Entity\User;
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
        /** @var \App\Entity\User $user */
        $user = $options['user'];

        $builder
            ->add('name', TextType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'nom',
                ],
                'data' => $user ? $user->getName() : '',
            ])
            ->add('phone', TelType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'tel',
                    'pattern' => '^(?:\+689)?(87|89|92|40)(\d{6})$',
                ],
                'data' => $user ? $user->getLambda()->getPhone() : '',
            ])
            ->add('email', EmailType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'email',
                ],
                'data' => $user ? $user->getEmail() : '',
            ])
            ->add('address', TextType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'address',
                ],
            ])
            ->add('commandAt', DateTimeType::class, [
                'label' => false,
                'date_widget' => 'single_text',
                'time_widget' => 'single_text',
                'view_timezone' => 'Pacific/Honolulu',
                'data' => (new \DateTime('+1 hours'))
                    ->setTime((new \DateTime('+1 hours'))->format('H'), 0),
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
            ->add('comment', TextareaType::class, [
                'required' => false,
                'label' => false,
                'attr' => [
                    'placeholder' => 'Information pour la commande',
                ],
            ])
            ->add('code', TextType::class, [
                'required' => false,
                'attr' => [
                    'class' => 'mx-lg-2',
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
            ->add('minTime', HiddenType::class, [
                'mapped' => false,
                'required' => false,
                'error_bubbling' => false,
                'attr' => [
                    'class' => 'minTime',
                ],
            ])
            ->add('openHours', HiddenType::class, [
                'mapped' => false,
                'required' => false,
                'error_bubbling' => false,
                'attr' => [
                    'class' => 'openHours',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'translation_domain' => 'form',
            'user' => false,
            'data_class' => Command::class,
        ])
            ->setAllowedTypes('user', [User::class, 'null'])
        ;
    }
}
