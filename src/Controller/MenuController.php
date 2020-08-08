<?php

namespace App\Controller;

use App\Entity\Menu;
use App\Entity\Provider;
use App\Form\MenuType;
use App\Repository\MenuRepository;
use App\Service\AjaxForm;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/menu", name="menu_")
 * @IsGranted("IS_AUTHENTICATED_FULLY")
 */
class MenuController extends AbstractController
{
    /**
     * @Route("/", name="index", methods={"GET"})
     */
    public function index(MenuRepository $menuRepository): Response
    {
        return $this->render('menu/index.html.twig', [
            'menus' => $menuRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new/menu/{id}", name="new", methods={"POST"})
     */
    public function new(Provider $provider, Request $request, AjaxForm $ajaxForm): Response
    {
        $menu = new Menu();
        $form = $ajaxForm->create_menu($menu, $provider);

        if ($request->isXmlHttpRequest()) {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $entityManager = $this->getDoctrine()->getManager();
                $menu->setProvider($provider);
                $entityManager->persist($menu);
                $entityManager->flush();

                return $this->render(
                    'ajax/menu/new.html.twig',
                    [
                        'menu' => $menu,
                    ],
                    new Response('success', Response::HTTP_CREATED)
                );
            }
            if ($form->isSubmitted() && !$form->isValid()) {
                return $this->render(
                    'ajax/menu/_form.html.twig',
                    [
                        'form_menu' => $form->createView(),
                    ],
                    new Response('error', Response::HTTP_BAD_REQUEST)
                );
            }
        } else {
            $this->addFlash('error', 'Impossible de créer un menu de cette façon !');

            return new Response('error', Response::HTTP_METHOD_NOT_ALLOWED);
        }
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
    public function edit(Request $request, Menu $menu): Response
    {
        $form = $this->createForm(MenuType::class, $menu);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('menu_index');
        }

        return $this->render('menu/edit.html.twig', [
            'menu' => $menu,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="delete", methods={"DELETE"})
     */
    public function delete(Request $request, Menu $menu): Response
    {
        if ($this->isCsrfTokenValid('delete'.$menu->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($menu);
            $entityManager->flush();
        }

        return $this->redirectToRoute('menu_index');
    }
}
