<?php

namespace App\Controller;

use App\Entity\Lambda;
use App\Entity\User;
use App\Form\Type\LambdaType;
use App\Repository\LambdaRepository;
use App\Service\Mailer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/superuser", name="lambda_")
 */
class LambdaController extends AbstractController
{
    /**
     * @Route("/", name="index", methods={"GET"})
     */
    public function index(LambdaRepository $lambdaRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->render('lambda/index.html.twig', [
            'lambdas' => $lambdaRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="new", methods={"GET","POST"})
     */
    public function new(Request $request, Mailer $mailer, UserPasswordEncoderInterface $password): Response
    {
        $this->denyAccessUnlessGranted('IS_ANONYMOUS');

        $user = new User();
        $form = $this->createForm(LambdaType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            $lambda = new Lambda();
            $lambda->setName($user->getName());

            $roles = $user->getRoles();
            $roles[] = 'ROLE_LAMBDA';

            $user->setNtahiti('111111')
                ->setPassword($password->encodePassword($user, $user->getPassword()))
                ->setLambda($lambda)
                ->setRoles($roles)
                ->setValidate(true)
            ;

            $entityManager->persist($user);
            $entityManager->flush();

            $mailer->sendConfirmationNewUser($user);

            $this->addFlash('success', 'L\'utilisateur a bien été créé. Veuillez confirmer votre adresse mail pour bénéficier des avantages sur le site.');

            return $this->redirectToRoute('app_login');
        }

        return $this->render('lambda/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="show", methods={"GET"})
     */
    public function show(Lambda $lambda): Response
    {
        return $this->render('lambda/show.html.twig', [
            'lambda' => $lambda,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Lambda $lambda): Response
    {
        $form = $this->createForm(LambdaType::class, $lambda);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('lambda_index');
        }

        return $this->render('lambda/edit.html.twig', [
            'lambda' => $lambda,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="delete", methods={"DELETE"})
     */
    public function delete(Request $request, Lambda $lambda): Response
    {
        if ($this->isCsrfTokenValid('delete'.$lambda->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($lambda);
            $entityManager->flush();
        }

        return $this->redirectToRoute('lambda_index');
    }
}
