<?php

namespace App\Api;

use App\Entity\Command;
use App\Message\SendEmailMessage;
use App\Repository\CommandRepository;
use App\Service\AjaxService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

/**
 * @Route("/command", name="command_api_")
 */
class CommandApi extends AbstractController
{
    /**
     * @Route("/", name="index", methods={"POST"})
     */
    public function index(CommandRepository $commandRepository, Security $security, Request $request)
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        $this->denyAccessUnlessGranted('USER_MANAGE', $user);

        $provider = '';
        if ($security->isGranted('ROLE_SUBUSER')) {
            $provider = $user->getSubuser()->getProvider();
        } elseif ($security->isGranted('ROLE_PROVIDER')) {
            $provider = $user->getProvider();
        }

        $form = $this->createForm(FormType::class, [], ['csrf_protection' => false])
            ->add('date')
            ->add('compare')
            ->add('order')
        ;

        if ($request->isXmlHttpRequest()) {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $defaultContext = [
                    AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object, $format, $context) {
                        return $object->getId();
                    },
                    AbstractNormalizer::GROUPS => 'commandjs',
                ];

                return $this->json(
                    $commandRepository->findByProviderOrderByCommandDate($provider, $form),
                    JsonResponse::HTTP_OK,
                    [],
                    $defaultContext
                );
            }
        }

        return new Response('error', Response::HTTP_NOT_ACCEPTABLE);
    }

    /**
     * @Route("/show-{id}", name="show", methods={"GET"})
     */
    public function show(Command $command, Security $security, CommandRepository $commandRepository)
    {
        $this->denyAccessUnlessGranted('COMMAND_VIEW', $command);

        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        $provider = '';
        if ($security->isGranted('ROLE_SUBUSER')) {
            $provider = $user->getSubuser()->getProvider();
        } elseif ($security->isGranted('ROLE_PROVIDER')) {
            $provider = $user->getProvider();
        }

        /** @var \App\Entity\Command $command */
        $command = $commandRepository->getFiltererByProvider($command->getId(), $provider);
        $details = $command->getDetails();

        $price = 0;
        /** @var \App\Entity\Meal $meal */
        foreach ($command->getMeals() as $meal) {
            $price += $meal->getPrice() * $details[$meal->getId()];
        }

        return $this->render('command/show.html.twig', [
            'command' => $command,
            'details' => $details,
            'price' => $price,
        ]);
    }

    /**
     * https://stackoverflow.com/questions/30538431/symfony-2-when-and-why-does-a-route-parameter-get-automatically-converted.
     *
     * @Route("/validate-{id}-{bool}", name="validate", methods={"POST", "GET"}, requirements={"bool": "0|1"})
     */
    public function validate(Command $command, Security $security, bool $bool, Request $request, AjaxService $ajaxService)
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        $this->denyAccessUnlessGranted('COMMAND_VALIDATE', $command);

        if ($security->isGranted('ROLE_SUBUSER')) {
            $user = $user->getSubuser()->getProvider()->getUser();
        }

        $form = $ajaxService->validateCommand($command, $bool, $user);

        if ($request->isXmlHttpRequest()) {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $command->setValidation($user->getProvider());
                $this->getDoctrine()->getManager()->flush();

                $this->dispatchMessage(new SendEmailMessage(3, $user->getId(), $command->getId(), $bool));

                return new Response('success', Response::HTTP_CREATED);
            }

            if ($form->isSubmitted() && !$form->isValid()) {
                return $this->render('command/validate.html.twig', [
                    'form' => $form->createView(),
                ], new Response('error', Response::HTTP_BAD_REQUEST));
            }
        }

        return $this->render('command/validate.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * https://stackoverflow.com/questions/30538431/symfony-2-when-and-why-does-a-route-parameter-get-automatically-converted.
     *
     * @Route("/message-{id}", name="message", methods={"POST", "GET"})
     */
    public function custom_message(Command $command, Security $security, Request $request, AjaxService $ajaxService)
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        $this->denyAccessUnlessGranted('COMMAND_VALIDATE', $command);

        if ($security->isGranted('ROLE_SUBUSER')) {
            $user = $user->getSubuser()->getProvider()->getUser();
        }

        $form = $ajaxService->customMessageCommand($command, $user);

        if ($request->isXmlHttpRequest()) {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $this->getDoctrine()->getManager()->flush();
                $this->dispatchMessage(new SendEmailMessage(3, $user->getId(), $command->getId(), 2));

                return new Response('success', Response::HTTP_CREATED);
            }

            if ($form->isSubmitted() && !$form->isValid()) {
                return $this->render('command/validate.html.twig', [
                    'form' => $form->createView(),
                ], new Response('error', Response::HTTP_BAD_REQUEST));
            }
        }

        return $this->render('command/validate.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
