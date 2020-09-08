<?php

namespace App\Form\Type;

use App\Entity\Contact;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'translation_domain' => 'form',
            ])
            ->add('email', EmailType::class, [
                'translation_domain' => 'form',
            ])
            ->add('subject', ChoiceType::class, [
                'translation_domain' => 'form',
                'choices' => [
                    'Information' => '0',
                    'Informatique et Libertés' => '1',
                    'Commande' => '2',
                    'Livraison' => '3',
                    'Conditions Générales' => '4',
                    'Signaler' => '5',
                ],
            ])
            ->add('message', TextareaType::class, [
                'translation_domain' => 'form',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Contact::class,
        ]);
    }
}
