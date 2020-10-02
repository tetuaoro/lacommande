<?php

namespace App\Form\Type;

use App\Entity\Newletter;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NewLetterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'bo-rad-10 txt10 p-l-20',
                    'placeholder' => 'Email Adrress',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Newletter::class,
            'attr' => [
                'class' => 'flex-c-m flex-w flex-col-c-m-lg p-l-5 p-r-5',
            ],
        ]);
    }
}
