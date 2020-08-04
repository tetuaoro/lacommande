<?php

namespace App\Controller;

use App\Entity\Delivery;
use App\Entity\Provider;
use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/user", name="user_")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/all/", name="index", methods={"GET"})
     */
    public function index(UserRepository $userRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="new", methods={"GET","POST"})
     */
    public function new(Request $request, UserPasswordEncoderInterface $password): Response
    {
        $this->denyAccessUnlessGranted('IS_ANONYMOUS');

        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            $choice = $form->get('entity')->getData();

            if ('provider' == $choice) {
                $provider = new Provider();
                $provider->setName($user->getName())
                    ->setCode(uniqid())
                    ->setClosetime(new \DateTime())
                    ->setOpentime(new \DateTime())
                    ->setUrl('https://www.google.com')
                ;
                $entityManager->persist($provider);

                $roles = $user->getRoles();
                $roles[] = 'ROLE_PROVIDER';
                $user->setProvider($provider)
                    ->setRoles($roles)
                ;
            } else {
                $delivery = new Delivery();
                $delivery->setName($user->getName())
                ;
                $entityManager->persist($delivery);

                $roles = $user->getRoles();
                $roles[] = 'ROLE_DELIVERY';
                $user->setDelivery($delivery)
                    ->setRoles($roles)
                ;
            }

            // password
            $user->setPassword($password->encodePassword($user, $user->getPassword()));

            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_home');
        }

        return $this->render('user/new.html.twig', [
            'controller_name' => 'user:new',
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="show", methods={"GET"})
     */
    public function show(User $user): Response
    {
        $this->denyAccessUnlessGranted('USER_VIEW', $user);

        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="edit", methods={"GET","POST"})
     */
    public function edit(Request $request, User $user): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('index');
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="delete", methods={"DELETE"})
     */
    public function delete(Request $request, User $user): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('index');
    }
}
