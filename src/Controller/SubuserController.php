<?php

namespace App\Controller;

use App\Entity\Subuser;
use App\Form\SubuserType;
use App\Repository\SubuserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/subuser")
 */
class SubuserController extends AbstractController
{
    /**
     * @Route("/", name="subuser_index", methods={"GET"})
     */
    public function index(SubuserRepository $subuserRepository): Response
    {
        return $this->render('subuser/index.html.twig', [
            'subusers' => $subuserRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="subuser_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $subuser = new Subuser();
        $form = $this->createForm(SubuserType::class, $subuser);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($subuser);
            $entityManager->flush();

            return $this->redirectToRoute('subuser_index');
        }

        return $this->render('subuser/new.html.twig', [
            'subuser' => $subuser,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="subuser_show", methods={"GET"})
     */
    public function show(Subuser $subuser): Response
    {
        return $this->render('subuser/show.html.twig', [
            'subuser' => $subuser,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="subuser_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Subuser $subuser): Response
    {
        $form = $this->createForm(SubuserType::class, $subuser);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('subuser_index');
        }

        return $this->render('subuser/edit.html.twig', [
            'subuser' => $subuser,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="subuser_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Subuser $subuser): Response
    {
        if ($this->isCsrfTokenValid('delete'.$subuser->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($subuser);
            $entityManager->flush();
        }

        return $this->redirectToRoute('subuser_index');
    }
}
