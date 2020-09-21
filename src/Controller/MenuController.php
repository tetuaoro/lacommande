<?php

namespace App\Controller;

use App\Entity\Menu;
use App\Repository\MenuRepository;
use App\Service\AjaxService;
use App\Service\Recaptcha;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/menu", name="menu_")
 */
class MenuController extends AbstractController
{
    /**
     * @Route("/", name="index", methods={"GET"})
     */
    public function index(MenuRepository $menuRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->render('menu/index.html.twig', [
            'menus' => $menuRepository->findAll(),
        ]);
    }

    /**
     * @Route("/{id}", name="show", methods={"GET"})
     */
    public function show(Menu $menu): Response
    {
        return $this->render('menu/show.html.twig', [
            'menu' => $menu,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Menu $menu, AjaxService $ajaxService, Recaptcha $recaptcha): Response
    {
        $this->denyAccessUnlessGranted('MENU_EDIT', $menu);

        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        $provider = $user->getProvider();
        $form = $ajaxService->menuForm($menu, $provider);

        if ($request->isXmlHttpRequest()) {
            $form->handleRequest($request);

            $f = false;
            if ($g = $form->get('recaptcha')->getData()) {
                $f = $recaptcha->captchaverify($g)->success;
            }
            if ($form->isSubmitted() && !$f) {
                $form->get('recaptcha')->addError(new FormError('Recaptcha : êtes-vous un robot ?'));
            }
            if ($form->isSubmitted() && !$form->isValid()) {
                return $this->render(
                    'menu/_form.html.twig',
                    [
                        'form' => $form->createView(),
                    ],
                    new Response('error', Response::HTTP_BAD_REQUEST)
                );
            }

            if ($form->isSubmitted() && $form->isValid() && $f) {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->flush();

                $this->addFlash('success', 'Menu modifié avec success.');

                return new Response($this->generateUrl('user_manage', ['id' => $user->getId(), 'view' => 'v-pills-menu']), Response::HTTP_CREATED);
            }
        }

        return $this->render('menu/_form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="delete", methods={"DELETE"})
     */
    public function delete(Request $request, Menu $menu): Response
    {
        $this->denyAccessUnlessGranted('MENU_DELETE', $menu);

        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        if ($this->isCsrfTokenValid('delete'.$menu->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $menu->resetMeals();
            $entityManager->remove($menu);
            $entityManager->flush();
        }

        return $this->redirectToRoute('user_manage', ['id' => $user->getId(), 'view' => 'v-pills-menu']);
    }
}
