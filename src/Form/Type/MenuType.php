<?php

namespace App\Form\Type;

use App\Entity\Category;
use App\Entity\Meal;
use App\Entity\Menu;
use App\Entity\Provider;
use App\Repository\MealRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MenuType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var \App\Entity\Provider */
        $provider = $options['provider'];

        $builder
            ->add('name', TextType::class, [
                'translation_domain' => 'form',
            ])
            ->add('price', NumberType::class, [
                'translation_domain' => 'form',
            ])
            ->add('meal', EntityType::class, [
                'translation_domain' => 'form',
                'class' => Meal::class,
                'choice_label' => 'name',
                'query_builder' => function (MealRepository $r) use ($provider) {
                    return $r->findMealWithOutMenu($provider->getId());
                },
            ])
            ->add('category', EntityType::class, [
                'translation_domain' => 'form',
                'class' => Category::class,
                'choice_label' => 'name',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Menu::class,
            'provider' => false,
        ]);

        $resolver->setAllowedTypes('provider', Provider::class);
    }
}
