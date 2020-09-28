<?php

namespace App\Api;

use App\Entity\Command;
use App\Repository\CommandRepository;
use App\Service\AjaxService;
use App\Service\Mailer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

/**
 * @Route("/command", name="command_api_")
 */
class CommandApi extends AbstractController
{
    /**
     * @Route("/", name="index", methods={"POST"})
     */
    public function index(CommandRepository $commandRepository, Request $request)
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        $this->denyAccessUnlessGranted('USER_MANAGE', $user);

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
                    $commandRepository->findByProviderOrderByCommandDate($user->getProvider(), $form),
                    JsonResponse::HTTP_OK,
                    [],
                    $defaultContext
                );
            }
        }

        return new Response('success', Response::HTTP_NOT_ACCEPTABLE);
    }

    /**
     * @Route("/show-{id}", name="show", methods={"GET"})
     */
    public function show(Command $command, CommandRepository $commandRepository)
    {
        $this->denyAccessUnlessGranted('COMMAND_VIEW', $command);

        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        /** @var \App\Entity\Command $command */
        $command = $commandRepository->getFiltererByProvider($command->getId(), $user->getProvider());
        $details = array_replace(...$command->getDetails());

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
    public function validate(Command $command, bool $bool, Mailer $mailer, Request $request, AjaxService $ajaxService)
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        $this->denyAccessUnlessGranted('USER_MANAGE', $user);

        $form = $ajaxService->validateCommand($command, $bool, $user);

        if ($request->isXmlHttpRequest()) {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $em = $this->getDoctrine()->getManager();

                $mailer->validateCommand($command, $bool, $user);
                $command->setValidate($bool);

                $em->flush();

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
    public function custom_message(Command $command, Mailer $mailer, Request $request, AjaxService $ajaxService)
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        $this->denyAccessUnlessGranted('USER_MANAGE', $user);

        $form = $ajaxService->customMessageCommand($command, $user);

        if ($request->isXmlHttpRequest()) {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $mailer->validateCommand($command, 2, $user);

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
