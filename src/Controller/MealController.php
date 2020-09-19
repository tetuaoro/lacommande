<?php

namespace App\Controller;

use App\Entity\Gallery;
use App\Entity\Meal;
use App\Repository\MealRepository;
use App\Service\AjaxService;
use App\Service\BitlyService;
use App\Service\Storage;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

/**
 * @Route("/product", name="meal_")
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

        // dd($mealRepository->getMealByProvider($this->getUser()->getProvider())->getResult());

        return $this->render('meal/index.html.twig', [
            'meals' => $meals,
        ]);
    }

    /**
     * @Route("/new-meal", name="new", methods={"POST", "GET"})
     */
    public function new(AjaxService $ajaxService, BitlyService $bitlyService, Storage $storage, Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_PROVIDER');

        $meal = new Meal();
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        $form = $ajaxService->create_meal($meal);

        if ($request->isXmlHttpRequest()) {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
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
                $meal->setGallery($gallery)
                    ->setIsDelete(false)
                ;
                $entityManager->persist($meal);
                $entityManager->flush();

                $meal->setBitly(
                    $bitlyService->bitThis($this->generateUrl('meal_show', ['id' => $meal->getId(), 'slug' => $meal->getSlug()], UrlGenerator::ABSOLUTE_URL), $meal->getName())
                );
                $entityManager->flush();

                $defaultContext = [
                    AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object, $format, $context) {
                        return $object->getId();
                    },
                    AbstractNormalizer::GROUPS => 'commandjs',
                ];

                return $this->json($meal, Response::HTTP_CREATED, [], $defaultContext);
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
     * @Route("/details/{slug}-{id}", name="show", methods={"GET"}, requirements={"slug": "[a-z0-9\-]*"})
     */
    public function show(string $slug, Meal $meal, AjaxService $ajaxService): Response
    {
        if ($meal->getIsDelete()) {
            throw $this->createNotFoundException('Cette assiette n\'existe plus !');
        }

        if ($meal->getSlug() != $slug) {
            return $this->redirectToRoute('meal_show', ['id' => $meal->getId(), 'slug' => $meal->getSlug()]);
        }

        $form = $ajaxService->cart_form($meal);

        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        $check = false;
        if ($user && $user->getLambda()) {
            $check = $user->getLambda()->checkFavorites($meal);
        }

        return $this->render('meal/show.html.twig', [
            'meal' => $meal,
            'form' => $form->createView(),
            'fav' => $check,
            'lacommandPrice' => 113,
        ]);
    }

    /**
     * @Route("/favorite/{id}", name="favorite", methods={"GET"})
     */
    public function checkFovorites(Meal $meal): Response
    {
        $this->denyAccessUnlessGranted('ROLE_LAMBDA');

        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        if ($lambda = $user->getLambda()) {
            $lambda->favorite($meal);
            $this->getDoctrine()->getManager()->flush();
        }

        return $this->redirectToRoute('meal_show', [
            'id' => $meal->getId(),
            'slug' => $meal->getSlug(),
        ]);
    }

    /**
     * @Route("/edit-meal-{id}", name="edit", methods={"POST", "GET"})
     */
    public function edit(Request $request, Meal $meal, Storage $storage, AjaxService $ajaxService): Response
    {
        $this->denyAccessUnlessGranted('MEAL_EDIT', $meal);

        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        $form = $ajaxService->edit_meal($meal);

        if ($request->isXmlHttpRequest()) {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
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

                $defaultContext = [
                    AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object, $format, $context) {
                        return $object->getId();
                    },
                    AbstractNormalizer::GROUPS => 'commandjs',
                ];

                $entityManager->flush();

                return $this->json($meal, Response::HTTP_ACCEPTED, [], $defaultContext);
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
