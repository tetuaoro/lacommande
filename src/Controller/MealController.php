<?php

namespace App\Controller;

use App\Entity\Command;
use App\Entity\Gallery;
use App\Entity\Meal;
use App\Form\MealType;
use App\Repository\MealRepository;
use App\Repository\UserRepository;
use App\Service\AjaxForm;
use App\Service\Recaptcha;
use App\Service\Storage;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
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
     * @Route("/n/new", name="new", methods={"POST"})
     */
    public function new(AjaxForm $ajaxForm, UserRepository $userRepo, Recaptcha $recaptcha, Storage $storage, Request $request): Response
    {
        $meal = new Meal();

        $this->denyAccessUnlessGranted('MEAL_CREATE', $meal);

        $form = $ajaxForm->create_meal($meal);

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
                $image = $form->get('image')->getData();
                $entityManager = $this->getDoctrine()->getManager();
                $provider = $userRepo->findOneBy(['username' => $this->getUser()->getUsername()])->getProvider();
                $info = $storage->uploadMealImage($image, $provider, $meal);
                $meal->setImg($info['mediaLink'])
                    ->setProvider($provider)
                    ->setImgInfo($info)
                ;
                $gallery = new Gallery();
                $gallery->setUrl($meal->getImg())
                    ->setName($meal->getName())
                    ->setType('img')
                    ;
                $meal->setGallery($gallery);
                $entityManager->persist($meal);
                $entityManager->flush();

                return $this->render(
                    'ajax/meal/new.html.twig',
                    [
                        'meal' => $meal,
                    ],
                    new Response('success', Response::HTTP_CREATED)
                );
            }
            if ($form->isSubmitted() && !$form->isValid()) {
                return $this->render(
                    'ajax/meal/_form.html.twig',
                    [
                        'form_meal' => $form->createView(),
                    ],
                    new Response('error', Response::HTTP_BAD_REQUEST)
                );
            }
        } else {
            $this->addFlash('error', 'Impossible de créer une assiette de cette façon !');

            return new Response('error', Response::HTTP_METHOD_NOT_ALLOWED);
        }
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
     * @Route("/e/{slug}-{id}/edit", name="edit", methods={"POST"}, requirements={"slug": "[a-z0-9\-]*"})
     */
    public function edit(string $slug, Request $request, Meal $meal): Response
    {
        $this->denyAccessUnlessGranted('MEAL_EDIT');

        if ($slug != $meal->getSlug()) {
            return $this->redirectToRoute('meal_edit', ['id' => $meal->getId(), 'slug' => $meal->getSlug()]);
        }

        $form = $this->createForm(MealType::class, $meal);
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
    public function delete(Request $request, Meal $meal, Storage $storage): Response
    {
        if ($request->isXmlHttpRequest()) {
            $this->denyAccessUnlessGranted('MEAL_DELETE', $meal);

            if ($this->isCsrfTokenValid('delete'.$meal->getId(), $request->request->get('_token'))) {
                $entityManager = $this->getDoctrine()->getManager();
                $storage->removeMealImage($meal);
                $entityManager->remove($meal);
                $entityManager->flush();

                return new Response('success', Response::HTTP_CREATED);
            }

            return new Response('error', Response::HTTP_BAD_REQUEST);
        }

        return new Response('error', Response::HTTP_METHOD_NOT_ALLOWED);
    }
}
