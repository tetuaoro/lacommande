<?php

namespace App\Form\Type;

use App\Entity\Command;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
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
            ->add('phone', NumberType::class, [
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
                'data' => new \DateTime('+1 hours'),
                'constraints' => [
                    new Assert\GreaterThan('+1 hours'),
                    new Assert\Range([
                        'min' => 'today',
                        'max' => 'tomorrow',
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
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Command::class,
        ]);
    }
}
