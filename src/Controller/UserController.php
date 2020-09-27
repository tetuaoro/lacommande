<?php

namespace App\Controller;

use App\Entity\Delivery;
use App\Entity\Provider;
use App\Entity\User;
use App\Form\Type\RegisterType;
use App\Form\Type\UserType;
use App\Repository\UserRepository;
use App\Service\Mailer;
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
    public function new(Request $request, Mailer $mailer, UserPasswordEncoderInterface $password): Response
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
                    ->setBitly(['link' => 'https://bit.ly/2ZDJHyz'])
                    ->setOpenHours([
                        'monday' => ['09:00-12:00', '13:00-18:00'],
                        'tuesday' => ['09:00-12:00', '13:00-18:00'],
                        'wednesday' => ['09:00-12:00'],
                        'thursday' => ['09:00-12:00', '13:00-18:00'],
                        'friday' => ['09:00-12:00', '13:00-20:00'],
                        'saturday' => ['09:00-12:00', '13:00-16:00'],
                        'sunday' => [],
                    ])
                    ->setMinPriceDelivery(2500)
                    ->setLinkfb('https://www.facebook.com')
                    ->setLinktwitter('https://www.twitter.com')
                    ->setLinkinsta('https://www.instagram.com')
                ;

                $roles = $user->getRoles();
                $roles[] = 'ROLE_PROVIDER';
                $user->setProvider($provider)
                    ->setRoles($roles)
                ;
            } elseif ('delivery' == $choice) {
                $delivery = new Delivery();
                $delivery->setName($user->getName())
                ;

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

            $mailer->sendConfirmationNewUser($user);

            $this->addFlash('success', 'L\'utilisateur a bien été créé. Veuillez confirmer votre adresse mail pour bénéficier des avantages sur ARII FOOD.');

            return $this->redirectToRoute('app_login');
        }

        return $this->render('user/new.html.twig', [
            'controller_name' => 'user:new',
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Ne pas supprimer.
     *
     * @Route("/create-new-super", name="register")
     */
    public function new_user(Request $request)
    {
        $form = $this->createForm(RegisterType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ('lambda' == $form->get('choice')->getData()) {
                return $this->redirectToRoute('lambda_new');
            }

            return $this->redirectToRoute('user_new');
        }

        return $this->render('user/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}-{slug}", name="show", methods={"GET"}, requirements={"slug": "[a-z0-9\-]*"})
     */
    public function show(User $user, string $slug): Response
    {
        $this->denyAccessUnlessGranted('USER_VIEW', $user);

        if ($user->getSlug() != $slug) {
            return $this->redirectToRoute('meal_show', ['id' => $user->getId(), 'slug' => $user->getSlug()]);
        }

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

    /**
     * @Route("/manage/{id}", name="manage", methods={"GET"})
     *
     * @param null|mixed $form
     * @param null|mixed $data
     */
    public function manageUser(User $user)
    {
        $this->denyAccessUnlessGranted('USER_MANAGE', $user);

        return $this->render('user/auth/manage.html.twig', [
            'user' => $user,
        ]);
    }
}
