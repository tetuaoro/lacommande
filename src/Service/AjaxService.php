<?php

namespace App\Service;

use App\Entity\Command;
use App\Entity\Meal;
use App\Entity\Menu;
use App\Entity\Provider;
use App\Entity\User;
use App\Form\Type\CartType;
use App\Form\Type\CommandType;
use App\Form\Type\MealType;
use App\Form\Type\MenuType;
use App\Form\Type\ProviderType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Routing\RouterInterface;

class AjaxService
{
    private $form;
    private $router;

    public function __construct(FormFactoryInterface $formFactoryInterface, RouterInterface $routerInterface)
    {
        $this->form = $formFactoryInterface;
        $this->router = $routerInterface;
    }

    public function command_meal(Command $command, ?User $user)
    {
        return $this->form->create(CommandType::class, $command, [
            'method' => 'POST',
            'action' => $this->router->generate('command_new'),
            'user' => $user,
        ]);
    }

    public function validateCommand(Command $command, Provider $provider)
    {
        return $this->form->create(FormType::class, $command, [
            'csrf_protection' => false,
            'attr' => [
                'id' => 'validateForm',
            ],
            'method' => 'POST',
            'action' => $this->router->generate('command_api_validate', ['id' => $command->getId()]),
        ])
            ->add('message', TextareaType::class, [
                'data' => $provider->getName().' : Nous validons votre commande ! Merci de nous faire confiance.',
            ])
        ;
    }

    public function cart_form(Meal $meal)
    {
        return $this->form->create(CartType::class, null, [
            'method' => 'POST',
            'action' => $this->router->generate('command_addToCart', ['id' => $meal->getId()]),
            'stock' => $meal->getStock(),
        ]);
    }

    public function test_create_menu(Menu $menu, Provider $provider)
    {
        return $this->form->create(MenuType::class, $menu, [
            'method' => 'POST',
            'action' => $this->router->generate('test_index'),
            'provider' => $provider,
        ]);
    }

    public function menuForm(Menu $menu, Provider $provider)
    {
        return $this->form->create(MenuType::class, $menu, [
            'method' => 'POST',
            'action' => ($menu && $menu->getId()) ? $this->router->generate('manage_menu_edit', ['id' => $menu->getId()]) : $this->router->generate('manage_menu_new'),
            'provider' => $provider,
        ]);
    }

    public function mealForm(Meal $meal)
    {
        return $this->form->create(MealType::class, $meal, [
            'method' => 'POST',
            'action' => ($meal && $meal->getId()) ? $this->router->generate('manage_meal_edit', ['id' => $meal->getId()]) : $this->router->generate('manage_meal_new'),
        ]);
    }

    public function edit_provider(Provider $provider)
    {
        return $this->form->create(ProviderType::class, $provider, [
            'method' => 'POST',
            'action' => $this->router->generate('provider_edit', ['id' => $provider->getId()]),
        ]);
    }
}
