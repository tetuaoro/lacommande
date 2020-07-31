<?php

namespace App\Controller;

use App\Entity\Command;
use App\Entity\Meal;
use App\Form\MealType;
use App\Repository\MealRepository;
use App\Service\AjaxForm;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
     * @Route("/n/new", name="new", methods={"GET","POST"})
     * @Route("/e/{slug}-{id}/edit", name="edit", methods={"GET","POST"}, requirements={"slug": "[a-z0-9\-]*"})
     */
    public function meal_cu(int $id = null, string $slug = null, MealRepository $mealRepo, Request $request): Response
    {
        $mod = '';
        $meal = $mealRepo->findOneBy(['id' => $id]);
        if (!$meal) {
            $meal = new Meal();
            $mod = 'create';
        } elseif ($slug != $meal->getSlug()) {
            return $this->redirectToRoute('meal_edit', ['id' => $meal->getId(), 'slug' => $meal->getSlug()]);
        }

        if ($meal->getId()) {
            $mod = 'edit';
        }

        $form = $this->createForm(MealType::class, $meal);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($meal);
            $entityManager->flush();

            return $this->redirectToRoute('meal_show', ['id' => $meal->getId(), 'slug' => $meal->getSlug()]);
        }

        return $this->render('meal/meal_cu.html.twig', [
            'page_name' => 'meal',
            'page_mod' => $mod,
            'meal' => $meal,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/s/{slug}-{id}", name="show", methods={"GET"}, requirements={"slug": "[a-z0-9\-]*"})
     */
    public function show(string $slug, Meal $meal, AjaxForm $ajaxForm): Response
    {
        if ($meal->getSlug() != $slug) {
            return $this->redirectToRoute('meal_show', ['id' => $meal->getId(), 'slug' => $meal->getSlug()]);
        }

        $command = new Command();
        $form = $ajaxForm->command_meal($command, $meal);

        return $this->render('meal/show.html.twig', [
            'meal' => $meal,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/editx", name="editc", methods={"GET","POST"})
     */
    public function edit(Request $request, Meal $meal): Response
    {
        $form = $this->createForm(Meal1Type::class, $meal);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('meal_index');
        }

        return $this->render('meal/edit.html.twig', [
            'meal' => $meal,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="delete", methods={"DELETE"})
     */
    public function delete(Request $request, Meal $meal): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if ($this->isCsrfTokenValid('delete'.$meal->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($meal);
            $entityManager->flush();
        }

        return $this->redirectToRoute('meal_index');
    }
}
