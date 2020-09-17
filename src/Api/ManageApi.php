<?php

namespace App\Api;

use App\Entity\Meal;
use App\Repository\MealRepository;
use App\Service\AjaxService;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * @Route("/manage", name="manage_")
 */
class ManageApi extends AbstractController
{
    /**
     * @Route("/meals/all.json", name="meals", methods={"GET"})
     */
    public function meals(MealRepository $mealRepository, NormalizerInterface $normalizerInterface, PaginatorInterface $paginator, Request $request)
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
            AbstractNormalizer::GROUPS => 'commandjs',
        ];

        $data = [
            'totalPage' => round($meals->getTotalItemCount() / $nb_items),
            'items' => $meals->getTotalItemCount(),
            'page' => $meals->getCurrentPageNumber(),
            'data' => $normalizerInterface->normalize($meals, 'json', $defaultContext),
        ];

        return $this->json($data);
    }

    /**
     * @Route("/new-meal", name="new_meal", methods={"GET"})
     */
    public function form(AjaxService $ajaxService)
    {
        $this->denyAccessUnlessGranted('ROLE_PROVIDER');

        $meal = new Meal();

        $form = $ajaxService->create_meal($meal);

        return $this->render('meal/_form.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
