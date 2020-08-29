<?php

namespace App\Controller;

use App\Entity\Command;
use App\Entity\Meal;
use App\Form\Type\CommandType;
use App\Repository\CommandRepository;
use App\Service\AjaxService;
use App\Service\Mailer;
use App\Service\Recaptcha;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/cmd", name="command_")
 */
class CommandController extends AbstractController
{
    /**
     * @Route("/", name="index", methods={"GET"})
     */
    public function index(CommandRepository $commandRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->render('command/index.html.twig', [
            'commands' => $commandRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new/{id}", name="new", methods={"GET", "POST"})
     */
    public function new(Meal $meal, Request $request, Recaptcha $recaptcha, Mailer $mailer, AjaxService $ajaxService): Response
    {
        $command = new Command();
        $form = $ajaxService->command_meal($command, $meal);

        if ($request->isXmlHttpRequest()) {
            $form->handleRequest($request);

            $f = false;
            if ($g = $form->get('recaptcha')->getData()) {
                $f = $recaptcha->captchaverify($g)->success;
            }

            if ($form->isSubmitted() && !$f) {
                $form->get('recaptcha')->addError(new FormError('Recaptcha : êtes-vous un robot ?'));
            }

            if ($form->isSubmitted() && $form->isValid()) {
                $entityManager = $this->getDoctrine()->getManager();

                $command->setName($meal->getProvider()->getId().'-'.$meal->getId())
                    ->addMeals($meal)
                    ->setProvider($meal->getProvider())
                    ->setPrice($meal->getPrice() * $command->getItems() + 113)
                ;
                $meal->commandPlus();

                $entityManager->persist($command);
                $entityManager->persist($meal);
                $entityManager->flush();

                $command->setName($command->getName().'-'.$command->getId());
                $entityManager->flush();

                $data = [
                    'id' => $command->getName(),
                    'name' => $meal->getName(),
                    'email' => $form->get('email')->getData(),
                    'provider' => $meal->getProvider(),
                    'items' => $command->getItems(),
                    'meal_price' => $meal->getPrice(),
                    'price' => $command->getPrice(),
                    'date' => $command->getCreatedAt(),
                ];

                $mailer->sendCommand($data);

                return new Response('success', Response::HTTP_CREATED);
            }
            if ($form->isSubmitted() && !$form->isValid()) {
                return $this->render('command/_form.html.twig', [
                    'form' => $form->createView(),
                ], new Response('error', Response::HTTP_BAD_REQUEST));
            }
        }

        return $this->render('command/_form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="show", methods={"GET"})
     */
    public function show(Command $command): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->render('command/show.html.twig', [
            'command' => $command,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Command $command): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $form = $this->createForm(CommandType::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('command_index');
        }

        return $this->render('command/edit.html.twig', [
            'command' => $command,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="delete", methods={"DELETE"})
     */
    public function delete(Request $request, Command $command): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if ($this->isCsrfTokenValid('delete'.$command->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($command);
            $entityManager->flush();
        }

        return $this->redirectToRoute('command_index');
    }
}
