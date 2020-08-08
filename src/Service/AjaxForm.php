<?php

namespace App\Service;

use App\Entity\Command;
use App\Entity\Meal;
use App\Entity\Menu;
use App\Entity\Provider;
use App\Form\CommandType;
use App\Form\MealType;
use App\Form\MenuType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Routing\RouterInterface;

class AjaxForm
{
    private $form;
    private $router;

    public function __construct(FormFactoryInterface $formFactoryInterface, RouterInterface $routerInterface)
    {
        $this->form = $formFactoryInterface;
        $this->router = $routerInterface;
    }

    public function command_meal(Command $command, Meal $meal)
    {
        return $this->form->create(CommandType::class, $command, [
            'method' => 'POST',
            'action' => $this->router->generate('command_new', ['id' => $meal->getId()]),
        ]);
    }

    public function create_menu(Menu $menu, Provider $provider)
    {
        return $this->form->create(MenuType::class, $menu, [
            'method' => 'POST',
            'action' => $this->router->generate('menu_new', ['id' => $provider->getId()]),
            'provider' => $provider,
        ]);
    }

    public function create_meal(Meal $meal)
    {
        return $this->form->create(MealType::class, $meal, [
            'method' => 'POST',
            'action' => $this->router->generate('meal_new')
        ]);
    }
}
