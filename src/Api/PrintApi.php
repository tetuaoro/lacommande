<?php

namespace App\Api;

use App\Entity\Provider;
use App\Repository\CommandRepository;
use App\Service\AjaxService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

/**
 * @Route("/print", name="print_")
 */
class PrintApi extends AbstractController
{
    /**
     * @Route("/", name="resolve", methods={"GET", "POST"})
     */
    public function resolve(AjaxService $ajaxService, Security $security, Request $request, CommandRepository $commandRepository)
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

        $form = $ajaxService->printSearch($provider);

        if ($request->isXmlHttpRequest()) {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $date = $form->get('date')->getData();

                return $this->render(
                    'print/index.html.twig',
                    [
                        'date' => $date,
                        'provider' => $provider,
                        'commands' => $commandRepository->getTotalOrderToPrint($provider, $date),
                        'form' => $form->createView(),
                    ],
                    new Response('success', Response::HTTP_ACCEPTED)
                );
            }

            return new Response('error', Response::HTTP_NOT_ACCEPTABLE);
        }

        return $this->render('print/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/print/results/{date}/{id}", name="result", methods={"GET"}, requirements={"date": "\d{2}\-\d{2}\-\d{4}"})
     */
    public function printable(Provider $provider, string $date, CommandRepository $commandRepository)
    {
        $this->denyAccessUnlessGranted('USER_MANAGE', $this->getUser());

        $tz = new \DateTimeZone('Pacific/Honolulu');
        $date = new \DateTime($date, $tz);

        $commands = $commandRepository->getOrderToPrint($provider, $date);

        return $this->render(
            'print/print.html.twig',
            [
                'date' => $date,
                'provider' => $provider,
                'commands' => $commands,
            ]
        );
    }
}
