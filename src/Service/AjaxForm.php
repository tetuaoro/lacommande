<?php

namespace App\Service;

use App\Entity\Command;
use App\Entity\Meal;
use App\Form\CommandType;
use App\Form\FileUploadType;
use App\Form\ReportForm\ReportSongType;
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
}
