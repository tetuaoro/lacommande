<?php

namespace App\Controller;

use App\Entity\Delivery;
use App\Entity\Meal;
use App\Entity\Menu;
use App\Entity\Provider;
use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use App\Service\AjaxForm;
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
                    ->setCode(uniqid())
                    ->setClosetime(new \DateTime())
                    ->setOpentime(new \DateTime())
                    ->setUrl('https://www.google.com')
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

            $this->addFlash('success', 'L\'utilisateur a bien été créé. Veillez a confirmé votre adresse mail pour bénéficier des avantages sur ARII FOOD.');

            return $this->redirectToRoute('app_login');
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

    /**
     * @Route("/{token}/manage/{id}", name="manage", methods={"GET"}, requirements={"token": "[0-9]{14}"})
     */
    public function adminUser(AjaxForm $ajaxForm, User $user)
    {
        $this->denyAccessUnlessGranted('USER_MANAGE', $user);

        $menu = new Menu();
        $form_menu = $ajaxForm->create_menu($menu, $user->getProvider());
        $meal = new Meal();
        $form_meal = $ajaxForm->create_meal($meal);

        return $this->render('user/auth/manage.html.twig', [
            'user' => $user,
            'form_menu' => $form_menu->createView(),
            'form_meal' => $form_meal->createView(),
        ]);
    }

    /**
     * @Route("/manage/meal/{slug}-{id}", name="meal_show", methods={"GET"}, requirements={"slug": "[a-z0-9\-]*"})
     */
    public function adminUserMealShow(string $slug, Meal $meal)
    {
        $this->denyAccessUnlessGranted('USER_MANAGE', $this->getUser());

        if ($slug != $meal->getSlug()) {
            return $this->redirectToRoute('user_meal_show', ['id' => $meal->getId(), 'slug' => $meal->getSlug()]);
        }

        return $this->render('user/auth/show.html.twig', [
            'meal' => $meal,
        ]);
    }

    /**
     * @Route("/manage/meal/{slug}-{id}/edit", name="meal_u", methods={"GET"}, requirements={"slug": "[a-z0-9\-]*"})
     */
    public function adminUserMealEdit(string $slug, Meal $meal)
    {
        $this->denyAccessUnlessGranted('USER_MANAGE', $this->getUser());

        if ($slug != $meal->getSlug()) {
            return $this->redirectToRoute('user_meal_show', ['id' => $meal->getId(), 'slug' => $meal->getSlug()]);
        }

        return $this->render('user/auth/edit.html.twig', [
            'meal' => $meal,
        ]);
    }

    /**
     * @Route("/{token}/manage/{slug}-{id}/delete", name="meal_delete", methods={"DELETE"}, requirements={"token": "[0-9]{14}", "slug": "[a-z0-9\-]*"})
     */
    public function adminUserMealDelete(string $token, User $user)
    {
        $this->denyAccessUnlessGranted('USER_MANAGE', $user);

        return $this->render('user/auth/delete.html.twig', [
            'user' => $user,
        ]);
    }
}
