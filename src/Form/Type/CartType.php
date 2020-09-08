<?php

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CartType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('quantity', IntegerType::class, [
                'translation_domain' => 'form',
                'label' => false,
                'data' => 1,
                'attr' => [
                    'min' => 1,
                    'max' => 10,
                    'placeholder' => '1 quantite',
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
    }
}
