<?php

namespace App\Controller;

use App\Entity\Command;
use App\Entity\Gallery;
use App\Entity\Meal;
use App\Repository\MealRepository;
use App\Service\AjaxService;
use App\Service\BitlyService;
use App\Service\Recaptcha;
use App\Service\Storage;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGenerator;

/**
 * @Route("/m/i", name="meal_")
 */
class MealController extends AbstractController
{
    /**
     * @Route("/", name="index", methods={"GET"})
     */
    public function index(MealRepository $mealRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $meals = $paginator->paginate(
            $mealRepository->paginator(),
            $request->query->get('page', 1),
            14,
            [
                $paginator::DEFAULT_SORT_FIELD_NAME => 'm.createdAt',
                $paginator::DEFAULT_SORT_DIRECTION => 'desc',
            ]
        );

        return $this->render('meal/index.html.twig', [
            'meals' => $meals,
        ]);
    }

    /**
     * @Route("/n/new", name="new", methods={"POST", "GET"})
     */
    public function new(AjaxService $ajaxService, Recaptcha $recaptcha, BitlyService $bitlyService, Storage $storage, Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_PROVIDER');

        $meal = new Meal();
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        $form = $ajaxService->create_meal($meal);

        if ($request->isXmlHttpRequest()) {
            $form->handleRequest($request);

            $f = false;
            if ($g = $form->get('recaptcha')->getData()) {
                $f = $recaptcha->captchaverify($g)->success;
            }

            if ($form->isSubmitted() && !$f) {
                $form->get('recaptcha')->addError(new FormError('Recaptcha : êtes-vous un robot ?'));
            }

            if ($form->isSubmitted() && $form->isValid() && $f) {
                $entityManager = $this->getDoctrine()->getManager();
                $image = $form->get('image')->getData();
                $provider = $user->getProvider();
                $info = $storage->uploadMealImage($image, $provider, $meal);
                $meal->setImg($info['mediaLink'])
                    ->setProvider($provider)
                    ->setImgInfo($info)
                ;
                $gallery = new Gallery();
                $gallery->setUrl($meal->getImg())
                ;
                $meal->setGallery($gallery);
                $entityManager->persist($meal);
                $entityManager->flush();

                $bitlink = $bitlyService->create_url($this->generateUrl('meal_show', ['id' => $meal->getId(), 'slug' => $meal->getSlug()], UrlGenerator::ABSOLUTE_URL));

                $meal->setBitly($bitlink);
                $entityManager->flush();

                $this->addFlash('success', 'Assiette créée avec succès.');

                return new Response($this->generateUrl('user_manage', ['id' => $user->getId(), 'view' => 'v-pills-meal']), Response::HTTP_CREATED);
            }
            if ($form->isSubmitted() && !$form->isValid()) {
                return $this->render(
                    'meal/_form.html.twig',
                    [
                        'form' => $form->createView(),
                    ],
                    new Response('error', Response::HTTP_BAD_REQUEST)
                );
            }
        }

        return $this->render('meal/_form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/s/{slug}-{id}", name="show", methods={"GET"}, schemes={"https"}, requirements={"slug": "[a-z0-9\-]*"})
     */
    public function show(string $slug, Meal $meal, AjaxService $ajaxService): Response
    {
        if ($meal->getSlug() != $slug) {
            return $this->redirectToRoute('meal_show', ['id' => $meal->getId(), 'slug' => $meal->getSlug()]);
        }

        $command = new Command();
        $form = $ajaxService->command_meal($command, $meal);

        return $this->render('meal/show.html.twig', [
            'meal' => $meal,
            'form' => $form->createView(),
            'lacommandPrice' => 113,
        ]);
    }

    /**
     * @Route("/e/{id}/edit", name="edit", methods={"POST", "GET"})
     */
    public function edit(Request $request, Meal $meal, BitlyService $bitlyService, Storage $storage, AjaxService $ajaxService, Recaptcha $recaptcha): Response
    {
        $this->denyAccessUnlessGranted('MEAL_EDIT', $meal);

        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        $form = $ajaxService->edit_meal($meal);
        if ($request->isXmlHttpRequest()) {
            $form->handleRequest($request);

            $f = false;
            if ($g = $form->get('recaptcha')->getData()) {
                $f = $recaptcha->captchaverify($g)->success;
            }

            if ($form->isSubmitted() && !$f) {
                $form->get('recaptcha')->addError(new FormError('Recaptcha : êtes-vous un robot ?'));
            }

            if ($form->isSubmitted() && $form->isValid() && $f) {
                $entityManager = $this->getDoctrine()->getManager();
                $image = $form->get('image')->getData();
                if ($image) {
                    $provider = $user->getProvider();
                    $info = $storage->uploadMealImage($image, $provider, $meal);
                    $meal->setImg($info['mediaLink'])
                        ->setImgInfo($info)
                    ;
                    $meal->getGallery()->setUrl($meal->getImg())
                    ;
                }
                $entityManager->flush();

                $bitlink = $bitlyService->update_url($meal->getBitly()['id'], $this->generateUrl('meal_show', ['id' => $meal->getId(), 'slug' => $meal->getSlug()], UrlGenerator::ABSOLUTE_URL));

                $meal->setBitly($bitlink);
                $entityManager->flush();

                $this->addFlash('success', 'Assiette modifiée avec succès.');

                return new Response($this->generateUrl('user_manage', ['id' => $user->getId(), 'view' => 'v-pills-meal']), Response::HTTP_CREATED);
            }
            if ($form->isSubmitted() && !$form->isValid()) {
                return $this->render(
                    'meal/_form.html.twig',
                    [
                        'form' => $form->createView(),
                    ],
                    new Response('error', Response::HTTP_BAD_REQUEST)
                );
            }
        }

        return $this->render('meal/_form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="delete", methods={"DELETE"})
     */
    public function delete(Request $request, Meal $meal, Storage $storage): Response
    {
        $this->denyAccessUnlessGranted('MEAL_DELETE', $meal);

        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        if ($this->isCsrfTokenValid('delete'.$meal->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $storage->removeMealImage($meal);
            $entityManager->remove($meal);
            $entityManager->flush();

            $this->addFlash('success', 'L\'assiete a été supprimée.');
        }

        return $this->redirectToRoute('user_manage', ['id' => $user->getId(), 'view' => 'v-pills-meal']);
    }
}
