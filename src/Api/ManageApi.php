<?php

namespace App\Api;

use App\Entity\Meal;
use App\Repository\MealRepository;
use App\Service\AjaxService;
use App\Service\Storage;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * @Route("/manage", name="manage_")
 */
class ManageApi extends AbstractController
{
    /**
     * @Route("/meals", name="meals", methods={"GET"})
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
    public function newform(AjaxService $ajaxService)
    {
        $this->denyAccessUnlessGranted('ROLE_PROVIDER');

        $meal = new Meal();

        $form = $ajaxService->create_meal($meal);

        return $this->render('meal/_form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/edit-meal-{id}", name="edit_meal", methods={"GET"})
     */
    public function editform(Meal $meal, AjaxService $ajaxService)
    {
        $this->denyAccessUnlessGranted('ROLE_PROVIDER');

        $form = $ajaxService->edit_meal($meal);

        return $this->render('meal/_form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/delete-meal-{id}", name="delete_meal", methods={"DELETE"})
     */
    public function delete(Meal $meal, Request $request, Storage $storage)
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
}
