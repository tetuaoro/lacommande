<?php

namespace App\Form\Type;

use App\Entity\Command;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommandType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('items', null, [
                'translation_domain' => 'form',
                'label' => false,
                'attr' => [
                    'min' => 1,
                    'max' => 10,
                    'placeholder' => '1 commande',
                ],
            ])
            ->add('phone', NumberType::class, [
                'translation_domain' => 'form',
                'required' => false,
                'label' => false,
                'attr' => [
                    'placeholder' => 'N° téléphone',
                    'pattern' => '(87|89|40)[0-9]{6}',
                ],
            ])
            ->add('email', EmailType::class, [
                'translation_domain' => 'form',
                'required' => true,
                'label' => false,
                'attr' => [
                    'placeholder' => 'email *',
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
