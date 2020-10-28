<?php

namespace App\Api;

use App\Entity\Gallery;
use App\Entity\Meal;
use App\Entity\Menu;
use App\Repository\MealRepository;
use App\Repository\MenuRepository;
use App\Service\AjaxService;
use App\Service\BitlyService;
use App\Service\Storage;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * @Route("/mm", name="manage_")
 */
class MealMenuApi extends AbstractController
{
    protected const QUOTAMEAL = 20;

    /**
     * @Route("/meals", name="meals", methods={"GET"})
     */
    public function meals(MealRepository $mealRepository, Security $security, NormalizerInterface $normalizerInterface, PaginatorInterface $paginator, Request $request)
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

        $nb_items = 15;
        $meals = $paginator->paginate(
            $mealRepository->getMealByProvider($provider),
            $request->query->get('page', 1),
            $nb_items,
            [
                $paginator::DEFAULT_SORT_FIELD_NAME => 'm.name',
                $paginator::DEFAULT_SORT_DIRECTION => 'asc',
            ]
        );

        $defaultContext = [
            AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object, $format, $context) {
                return $object->getId();
            },
            AbstractNormalizer::GROUPS => 'mealjs',
        ];

        $totalPage = $meals->getTotalItemCount() / $nb_items;

        $data = [
            'totalPage' => floor($totalPage) == $totalPage ? $totalPage : floor($totalPage) + 1,
            'quota' => self::QUOTAMEAL,
            'items' => $meals->getTotalItemCount(),
            'page' => $meals->getCurrentPageNumber(),
            'data' => $normalizerInterface->normalize($meals, 'json', $defaultContext),
        ];

        return $this->json($data);
    }

    /**
     * @Route("/new-meal", name="meal_new", methods={"GET", "POST"})
     */
    public function mealNew(AjaxService $ajaxService, Security $security, MealRepository $mealRepository, Request $request, Storage $storage, BitlyService $bitlyService)
    {
        $meal = new Meal();

        $this->denyAccessUnlessGranted('MEAL_CREATE', $meal);

        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        $provider = '';
        if ($security->isGranted('ROLE_SUBUSER')) {
            $provider = $user->getSubuser()->getProvider();
        } elseif ($security->isGranted('ROLE_PROVIDER')) {
            $provider = $user->getProvider();
        }

        if ($mealRepository->getCountMeals($provider) >= self::QUOTAMEAL) {
            return new Response('Le quota de '.self::QUOTAMEAL.' assiettes a été atteint ! Vous ne pouvez plus en ajouter.', Response::HTTP_CONFLICT);
        }

        $form = $ajaxService->mealForm($meal);

        if ($request->isXmlHttpRequest()) {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $entityManager = $this->getDoctrine()->getManager();
                $image = $form->get('image')->getData();
                $info = $storage->uploadMealImage($image, $provider, $meal);
                $meal->setImg($info['mediaLink'])
                    ->setProvider($provider)
                    ->setImgInfo($info)
                    ->setTotalcommand(0)
                    ->setViewer(0)
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

                return new Response('sucess', Response::HTTP_CREATED);
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
     * @Route("/edit-meal-{id}", name="meal_edit", methods={"GET", "POST"})
     */
    public function mealEdit(Meal $meal, Security $security, AjaxService $ajaxService, Request $request, Storage $storage)
    {
        $this->denyAccessUnlessGranted('MEAL_VALIDATE', $meal);

        $form = $ajaxService->mealForm($meal);

        if ($request->isXmlHttpRequest()) {
            $form->handleRequest($request);

            /** @var \App\Entity\User $user */
            $user = $this->getUser();

            $provider = '';
            if ($security->isGranted('ROLE_SUBUSER')) {
                $provider = $user->getSubuser()->getProvider();
            } elseif ($security->isGranted('ROLE_PROVIDER')) {
                $provider = $user->getProvider();
            }

            if ($form->isSubmitted() && $form->isValid()) {
                $entityManager = $this->getDoctrine()->getManager();
                $image = $form->get('image')->getData();
                if ($image) {
                    $info = $storage->uploadMealImage($image, $provider, $meal);
                    $meal->setImg($info['mediaLink'])
                        ->setImgInfo($info)
                    ;
                    $meal->getGallery()->setUrl($meal->getImg())
                    ;
                }

                $entityManager->flush();

                return new Response('sucess', Response::HTTP_ACCEPTED);
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
     * @Route("/delete-meal-{id}", name="meal_delete", methods={"DELETE"})
     */
    public function mealDelete(Meal $meal, Request $request, Storage $storage)
    {
        $this->denyAccessUnlessGranted('MEAL_VALIDATE', $meal);

        if ($request->isXmlHttpRequest()) {
            $entityManager = $this->getDoctrine()->getManager();
            $storage->removeMealImage($meal);
            if ($meal->getTotalcommand()) {
                $meal->setIsDelete(true);
            } else {
                $entityManager->remove($meal);
            }
            $entityManager->flush();

            return new Response('success', Response::HTTP_ACCEPTED);
        }

        return new Response('error', Response::HTTP_BAD_REQUEST);
    }

    /**
     * @Route("/menus", name="menus", methods={"GET"})
     */
    public function menus(MenuRepository $menuRepository, Security $security)
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

        $defaultContext = [
            AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object, $format, $context) {
                return $object->getId();
            },
            AbstractNormalizer::GROUPS => 'menujs',
        ];

        return $this->json(
            $menuRepository->findMyMenu($provider),
            JsonResponse::HTTP_OK,
            [],
            $defaultContext
        );
    }

    /**
     * @Route("/new-menu", name="menu_new", methods={"GET", "POST"})
     */
    public function menuNew(Request $request, Security $security, AjaxService $ajaxService): Response
    {
        $menu = new Menu();
        $this->denyAccessUnlessGranted('MENU_CREATE', $menu);

        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        $provider = '';
        if ($security->isGranted('ROLE_SUBUSER')) {
            $provider = $user->getSubuser()->getProvider();
        } elseif ($security->isGranted('ROLE_PROVIDER')) {
            $provider = $user->getProvider();
        }

        $form = $ajaxService->menuForm($menu, $provider);

        if ($request->isXmlHttpRequest()) {
            $form->handleRequest($request);

            if ($form->isSubmitted() && !$form->isValid()) {
                return $this->render(
                    'menu/_form.html.twig',
                    [
                        'form' => $form->createView(),
                    ],
                    new Response('error', Response::HTTP_BAD_REQUEST)
                );
            }

            if ($form->isSubmitted() && $form->isValid()) {
                $entityManager = $this->getDoctrine()->getManager();
                $menu->setProvider($provider);
                $entityManager->persist($menu);
                $entityManager->flush();

                $defaultContext = [
                    AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object, $format, $context) {
                        return $object->getId();
                    },
                    AbstractNormalizer::GROUPS => 'menujs',
                ];

                return $this->json(
                    $menu,
                    JsonResponse::HTTP_CREATED,
                    [],
                    $defaultContext
                );
            }
        }

        return $this->render('menu/_form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/edit-menu-{id}", name="menu_edit", methods={"GET", "POST"})
     */
    public function menuEdit(Menu $menu, Security $security, Request $request, AjaxService $ajaxService): Response
    {
        $this->denyAccessUnlessGranted('MENU_EDIT', $menu);

        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        $provider = '';
        if ($security->isGranted('ROLE_SUBUSER')) {
            $provider = $user->getSubuser()->getProvider();
        } elseif ($security->isGranted('ROLE_PROVIDER')) {
            $provider = $user->getProvider();
        }

        $form = $ajaxService->menuForm($menu, $provider);

        if ($request->isXmlHttpRequest()) {
            $form->handleRequest($request);

            if ($form->isSubmitted() && !$form->isValid()) {
                return $this->render(
                    'menu/_form.html.twig',
                    [
                        'form' => $form->createView(),
                    ],
                    new Response('error', Response::HTTP_BAD_REQUEST)
                );
            }

            if ($form->isSubmitted() && $form->isValid()) {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->flush();

                $defaultContext = [
                    AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object, $format, $context) {
                        return $object->getId();
                    },
                    AbstractNormalizer::GROUPS => 'menujs',
                ];

                return $this->json(
                    $menu,
                    JsonResponse::HTTP_ACCEPTED,
                    [],
                    $defaultContext
                );
            }
        }

        return $this->render('menu/_form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/delete-menu-{id}", name="menu_delete", methods={"DELETE"})
     */
    public function menuDelete(Menu $menu, Request $request)
    {
        $this->denyAccessUnlessGranted('MENU_DELETE', $menu);

        if ($request->isXmlHttpRequest()) {
            $entityManager = $this->getDoctrine()->getManager();
            $menu->resetMeals();
            $entityManager->remove($menu);
            $entityManager->flush();

            return new Response('success', Response::HTTP_ACCEPTED);
        }

        return new Response('error', Response::HTTP_BAD_REQUEST);
    }
}
