<?php

namespace App\Controller;

use App\Entity\Provider;
use App\Repository\MealRepository;
use App\Repository\ProviderRepository;
use App\Service\AjaxService;
use App\Service\Storage;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/public/provider", name="provider_")
 */
class ProviderController extends AbstractController
{
    /**
     * @Route("/", name="index", methods={"GET"})
     */
    public function index(ProviderRepository $providerRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->render('provider/index.html.twig', [
            'providers' => $providerRepository->findAll(),
        ]);
    }

    /**
     * @Route("/{slug}-{id}", name="show", methods={"GET"}, requirements={"slug": "[a-z0-9\-]*"})
     */
    public function show(string $slug, Provider $provider, PaginatorInterface $paginator, MealRepository $mealRepository, Request $request): Response
    {
        if ($slug != $provider->getSlug()) {
            return $this->redirectToRoute('provider_show', [
                'id' => $provider->getId(),
                'slug' => $provider->getSlug(),
            ]);
        }

        $meals = $paginator->paginate(
            $mealRepository->getMealByProvider($provider),
            $request->query->get('page', 1),
            14,
            [
                $paginator::DEFAULT_SORT_FIELD_NAME => 'm.name',
                $paginator::DEFAULT_SORT_DIRECTION => 'asc',
            ]
        );

        return $this->render('provider/show.html.twig', [
            'controller_name' => 'provider:show',
            'provider' => $provider,
            'meals' => $meals,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Storage $storage, Provider $provider, AjaxService $ajaxService): Response
    {
        $this->denyAccessUnlessGranted('PROVIDER_EDIT', $provider);

        $form = $ajaxService->edit_provider($provider);

        if ($request->isXmlHttpRequest()) {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $entityManager = $this->getDoctrine()->getManager();
                $image = $form->get('image')->getData();
                if ($image) {
                    $info = $storage->uploadProviderImage($image, $provider);
                    $provider->setBgImg($info['mediaLink'])
                        ->setImgInfo($info)
                    ;
                }
                $entityManager->flush();

                $this->addFlash('success', 'Profile modifié avec succès.');

                return new Response($this->generateUrl('provider_show', ['id' => $provider->getId(), 'slug' => $provider->getSlug()]), Response::HTTP_CREATED);
            }

            if ($form->isSubmitted() && !$form->isValid()) {
                return $this->render(
                    'provider/_form.html.twig',
                    [
                        'form' => $form->createView(),
                    ],
                    new Response('error', Response::HTTP_BAD_REQUEST)
                );
            }
        }

        return $this->render('provider/_form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="delete", methods={"DELETE"})
     */
    public function delete(Request $request, Provider $provider): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if ($this->isCsrfTokenValid('delete'.$provider->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($provider);
            $entityManager->flush();
        }

        return $this->redirectToRoute('provider_index');
    }
}
