<?php

namespace App\Form\Type;

use App\Entity\Meal;
use App\Entity\Menu;
use App\Entity\Provider;
use App\Repository\MealRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
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
                'multiple' => true,
                'by_reference' => false,
                'query_builder' => function (MealRepository $r) use ($provider) {
                    return $r->findMealWithOutMenu($provider->getId());
                },
                // @var \App\Entity\Meal $meal
                'group_by' => function ($meal, $key, $value) {
                    if ($meal && $meal->getMenu()) {
                        return 'Déjà Associé';
                    }
                },
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
        ]);

        $resolver->setAllowedTypes('provider', Provider::class);
    }
}
