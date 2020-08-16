<?php

namespace App\Service;

use App\Entity\Command;
use App\Entity\Meal;
use App\Entity\Menu;
use App\Entity\Provider;
use App\Form\Type\CommandType;
use App\Form\Type\MealType;
use App\Form\Type\MenuType;
use App\Form\Type\ProviderType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
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
        return $this->form->createNamed('meal-create', MealType::class, $meal, [
            'method' => 'POST',
            'action' => $this->router->generate('meal_new'),
        ]);
    }

    public function edit_meal(Meal $meal)
    {
        return $this->form->createNamed('meal-edit-'.$meal->getId(), MealType::class, $meal, [
            'method' => 'POST',
            'action' => $this->router->generate('meal_edit', ['id' => $meal->getId(), 'slug' => $meal->getSlug()]),
        ]);
    }

    public function edit_provider(Provider $provider)
    {
        return $this->form->create(ProviderType::class, $provider, [
            'method' => 'POST',
            'action' => $this->router->generate('provider_edit', ['id' => $provider->getId()]),
        ]);
    }

    public function getErrorsFromForm(FormInterface $form)
    {
        $errors = [];
        foreach ($form->getErrors() as $error) {
            $errors[] = $error->getMessage();
        }
        foreach ($form->all() as $childForm) {
            if ($childForm instanceof FormInterface) {
                if ($childErrors = $this->getErrorsFromForm($childForm)) {
                    $errors[$childForm->getName()] = $childErrors;
                }
            }
        }

        return $errors;
    }
}
