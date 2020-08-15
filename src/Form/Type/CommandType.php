<?php

namespace App\Form\Type;

use App\Entity\Command;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Regex;

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
                    'placeholder' => 'N° vini',
                    'pattern' => '(87|89)[0-9]{6}',
                ],
            ])
            ->add('email', EmailType::class, [
                'translation_domain' => 'form',
                'required' => false,
                'label' => false,
                'attr' => [
                    'placeholder' => 'email',
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
