<?php

namespace App\Form\Type;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;

class UserType extends AbstractType
{
    protected $router;

    public function __construct(RouterInterface $routerInterface)
    {
        $this->router = $routerInterface;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'translation_domain' => 'connect',
            ])
            ->add('email', EmailType::class, [
                'translation_domain' => 'connect',
            ])
            ->add('username', TextType::class, [
                'translation_domain' => 'connect',
            ])
            ->add('ntahiti', TextType::class, [
                'translation_domain' => 'connect',
            ])
            ->add('entity', ChoiceType::class, [
                'translation_domain' => 'connect',
                'mapped' => false,
                'required' => true,
                'choices' => [
                    'provider' => 'provider',
                    'delivery' => 'delivery',
                ],
            ])
            ->add('password', RepeatedType::class, [
                'translation_domain' => 'connect',
                'required' => true,
                'type' => PasswordType::class,
                'options' => ['attr' => ['class' => 'password-field']],
            ])
            ->add('checked', CheckboxType::class, [
                'translation_domain' => 'connect',
                'mapped' => false,
                'required' => true,
                'label' => 'J\'accepte les conditions',
            ])
            ->add('envoyer', SubmitType::class, [
                'attr' => [
                    'class' => 'btn',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
