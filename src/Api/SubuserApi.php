<?php

namespace App\Api;

use App\Entity\Subuser;
use App\Entity\User;
use App\Repository\SubuserRepository;
use App\Service\AjaxService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/sub", name="sub_")
 */
class SubuserApi extends AbstractController
{
    /**
     * @Route("/", name="index", methods={"GET"})
     */
    public function index()
    {
        return $this->json([]);
    }

    /**
     * @Route("/new", name="new", methods={"GET", "POST"})
     */
    public function new(Request $request, UserPasswordEncoderInterface $password, SubuserRepository $subuserRepository, AjaxService $ajaxService)
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        $this->denyAccessUnlessGranted('USER_MANAGE', $user);

        if ($subuserRepository->getCountSubusers($user->getProvider()) >= 5) {
            return new Response('Le quota de 5 suppléants a été atteint ! Vous ne pouvez plus en ajouter.', Response::HTTP_CONFLICT);
        }

        $s_user = new User();
        $form = $ajaxService->subForm($s_user);

        if ($request->isXmlHttpRequest()) {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $entityManager = $this->getDoctrine()->getManager();

                $subuser = new Subuser();
                $subuser->setProvider($user->getProvider())
                    ->setName($s_user->getName())
                ;

                $roles = $s_user->getRoles();
                $roles[] = 'ROLE_SUBUSER';

                $s_user
                    ->setPassword(
                        $password->encodePassword($s_user, $s_user->getPassword())
                    )
                    ->setNtahiti($user->getNtahiti())
                    ->setSubuser($subuser)
                    ->setRoles($roles)
                ;

                $entityManager->persist($s_user);

                $entityManager->flush();

                return new Response('success', Response::HTTP_CREATED);
            }

            if ($form->isSubmitted() && !$form->isValid()) {
                return $this->render('subuser/_form.html.twig', [
                    'form' => $form->createView(),
                ], new Response('error', Response::HTTP_BAD_REQUEST));
            }
        }

        return $this->render('subuser/_form.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
