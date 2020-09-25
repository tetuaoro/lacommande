<?php

namespace App\Controller;

use App\Entity\Meal;
use App\Repository\MealRepository;
use App\Service\AjaxService;
use Knp\Component\Pager\PaginatorInterface;
use Spatie\OpeningHours\OpeningHours;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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

        $openingHours = OpeningHours::create($meal->getProvider()->getOpenHours());

        dump($meal->getImgInfo());

        return $this->render('meal/show.html.twig', [
            'meal' => $meal,
            'form' => $form->createView(),
            'fav' => $check,
            'openTime' => $openingHours->nextOpen(new \DateTime('now', new \DateTimeZone('Pacific/Honolulu'))),
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
}
