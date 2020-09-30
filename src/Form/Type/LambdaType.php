<?php

namespace App\Form\Type;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;

class LambdaType extends AbstractType
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
            ])
            ->add('email', EmailType::class, [
            ])
            ->add('username', TextType::class, [
            ])
            ->add('phone', TelType::class, [
                'help' => '87XXXXXX ou 89XXXXXX',
                'attr' => [
                    'placeholder' => 'tel',
                    'pattern' => '^(?:\+689)?(87|89|92|40)(\d{6})$',
                ],
            ])
            ->add('password', RepeatedType::class, [
                'required' => true,
                'type' => PasswordType::class,
                'options' => ['attr' => ['class' => 'password-field']],
            ])
            ->add('checked', CheckboxType::class, [
                'mapped' => false,
                'required' => true,
                'label' => 'J\'accepte les conditions',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'translation_domain' => 'connect',
            'data_class' => User::class,
        ]);
    }
}
