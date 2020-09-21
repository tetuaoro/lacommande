<?php

namespace App\Form\Type;

use App\Entity\Meal;
use App\Entity\Menu;
use App\Entity\Provider;
use App\Repository\MealRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MenuType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var \App\Entity\Provider */
        $provider = $options['provider'];

        $builder
            ->add('meals', EntityType::class, [
                'translation_domain' => 'form',
                'class' => Meal::class,
                'required' => false,
                'multiple' => true,
                'by_reference' => false,
                'query_builder' => function (MealRepository $r) use ($provider) {
                    return $r->findMealWithOutMenu($provider);
                },
                // @var \App\Entity\Meal $meal
                'group_by' => function ($meal, $key, $value) {
                    if ($meal && $meal->getMenu()) {
                        return '2) Déjà Associée';
                    }

                    return '1) Pas encore associée';
                },
            ])
            ->add('category', CategoryType::class, [
                'translation_domain' => 'form',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Menu::class,
            'provider' => false,
            'attr' => [
                'id' => 'menuForm',
            ],
        ]);

        $resolver->setAllowedTypes('provider', Provider::class);
    }
}
