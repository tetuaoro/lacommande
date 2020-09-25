<?php

namespace App\Api;

use App\Entity\Command;
use App\Entity\Gallery;
use App\Entity\Meal;
use App\Entity\Menu;
use App\Repository\CommandRepository;
use App\Repository\MealRepository;
use App\Repository\MenuRepository;
use App\Service\AjaxService;
use App\Service\BitlyService;
use App\Service\Storage;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Route("/manage", name="manage_")
 */
class ManageApi extends AbstractController
{
    /**
     * @Route("/meals", name="meals", methods={"GET"})
     */
    public function meals(MealRepository $mealRepository, SerializerInterface $serializerInterface, NormalizerInterface $normalizerInterface, PaginatorInterface $paginator, Request $request)
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        $this->denyAccessUnlessGranted('USER_MANAGE', $user);

        $nb_items = 15;
        $meals = $paginator->paginate(
            $mealRepository->getMealByProvider($user->getProvider()),
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
            'items' => $meals->getTotalItemCount(),
            'page' => $meals->getCurrentPageNumber(),
            'data' => $normalizerInterface->normalize($meals, 'json', $defaultContext),
        ];

        return $this->json($data);
    }

    /**
     * @Route("/new-meal", name="meal_new", methods={"GET", "POST"})
     */
    public function mealNew(AjaxService $ajaxService, Request $request, Storage $storage, BitlyService $bitlyService)
    {
        $this->denyAccessUnlessGranted('ROLE_PROVIDER');

        $meal = new Meal();

        $form = $ajaxService->mealForm($meal);

        if ($request->isXmlHttpRequest()) {
            $form->handleRequest($request);

            /** @var \App\Entity\User $user */
            $user = $this->getUser();

            if ($form->isSubmitted() && $form->isValid()) {
                $entityManager = $this->getDoctrine()->getManager();
                $image = $form->get('image')->getData();
                $provider = $user->getProvider();
                $info = $storage->uploadMealImage($image, $provider, $meal);
                $meal->setImg($info['mediaLink'])
                    ->setProvider($provider)
                    ->setImgInfo($info)
                    ->setTotalcommand(0)
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
                    AbstractNormalizer::GROUPS => 'mealjs',
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
     * @Route("/edit-meal-{id}", name="meal_edit", methods={"GET", "POST"})
     */
    public function mealEdit(Meal $meal, AjaxService $ajaxService, Request $request, Storage $storage)
    {
        $this->denyAccessUnlessGranted('MEAL_EDIT', $meal);

        $form = $ajaxService->mealForm($meal);

        if ($request->isXmlHttpRequest()) {
            $form->handleRequest($request);

            /** @var \App\Entity\User $user */
            $user = $this->getUser();

            if ($form->isSubmitted() && $form->isValid()) {
                $entityManager = $this->getDoctrine()->getManager();
                $image = $form->get('image')->getData();
                if ($image) {
                    $info = $storage->uploadMealImage($image, $user->getProvider(), $meal);
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
                    AbstractNormalizer::GROUPS => 'mealjs',
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
     * @Route("/delete-meal-{id}", name="meal_delete", methods={"DELETE"})
     */
    public function mealDelete(Meal $meal, Request $request, Storage $storage)
    {
        $this->denyAccessUnlessGranted('MEAL_DELETE', $meal);

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
    public function menus(MenuRepository $menuRepository)
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        $this->denyAccessUnlessGranted('USER_MANAGE', $user);

        $defaultContext = [
            AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object, $format, $context) {
                return $object->getId();
            },
            AbstractNormalizer::GROUPS => 'menujs',
        ];

        return $this->json(
            $menuRepository->findMyMenu($user->getProvider()),
            JsonResponse::HTTP_OK,
            [],
            $defaultContext
        );
    }

    /**
     * @Route("/new-menu", name="menu_new", methods={"GET", "POST"})
     */
    public function menuNew(Request $request, AjaxService $ajaxService): Response
    {
        $this->denyAccessUnlessGranted('ROLE_PROVIDER');

        $menu = new Menu();

        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        $provider = $user->getProvider();
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
    public function menuEdit(Menu $menu, Request $request, AjaxService $ajaxService): Response
    {
        $this->denyAccessUnlessGranted('MENU_EDIT', $menu);

        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        $provider = $user->getProvider();
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

    /**
     * @Route("/commands", name="commands", methods={"POST"})
     */
    public function commands(CommandRepository $commandRepository, Request $request)
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        $this->denyAccessUnlessGranted('USER_MANAGE', $user);

        $form = $this->createForm(FormType::class, [], ['csrf_protection' => false])
            ->add('date')
            ->add('compare')
            ->add('limit')
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
     * @Route("/command-show-{id}", name="command_show", methods={"GET"})
     */
    public function commandShow(Command $command, CommandRepository $commandRepository)
    {
        $this->denyAccessUnlessGranted('COMMAND_VIEW', $command);

        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        return $this->render('command/show.html.twig', [
            'command' => $commandRepository->getFiltererByProvider($command->getId(), $user->getProvider()),
        ]);
    }
}
