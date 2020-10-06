<?php

namespace App\Api;

use App\Entity\Subuser;
use App\Entity\User;
use App\Repository\SubuserRepository;
use App\Repository\UserRepository;
use App\Service\AjaxService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * @Route("/sub", name="sub_")
 */
class SubuserApi extends AbstractController
{
    protected const QUOTASUB = 5;

    /**
     * @Route("/", name="index", methods={"GET"})
     */
    public function index(SubuserRepository $subuserRepository, NormalizerInterface $normalizerInterface)
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        $this->denyAccessUnlessGranted('USER_MANAGE', $user);

        $defaultContext = [
            AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object, $format, $context) {
                return $object->getId();
            },
            AbstractNormalizer::GROUPS => 'subjs',
        ];

        $data = [
            'quota' => self::QUOTASUB,
            'data' => $normalizerInterface->normalize($subuserRepository->getSubUsers($user->getProvider()), 'json', $defaultContext),
        ];

        return $this->json($data);
    }

    /**
     * @Route("/new", name="new", methods={"GET", "POST"})
     */
    public function new(Request $request, UserPasswordEncoderInterface $password, SubuserRepository $subuserRepository, AjaxService $ajaxService)
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        $this->denyAccessUnlessGranted('USER_MANAGE', $user);

        if ($subuserRepository->getCountSubusers($user->getProvider()) >= self::QUOTASUB) {
            return new Response('Le quota de '.self::QUOTASUB.' suppléants a été atteint ! Vous ne pouvez plus en ajouter.', Response::HTTP_CONFLICT);
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

                $s_user->setPassword(
                    $password->encodePassword($s_user, $s_user->getPassword())
                )
                    ->setNtahiti($user->getNtahiti().'#')
                    ->setSubuser($subuser)
                    ->setRoles($roles)
                    ->setPhone($user->getPhone())
                    ->setValidate(true)
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

    /**
     * @Route("/edit-sub-{id}", name="edit", methods={"GET", "POST"})
     */
    public function edit(Subuser $subuser, UserRepository $userRepository, Request $request, UserPasswordEncoderInterface $password, AjaxService $ajaxService)
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        $this->denyAccessUnlessGranted('USER_MANAGE', $user);

        $s_user = $userRepository->getSubBySub($subuser);
        $form = $ajaxService->subForm($s_user);

        if ($request->isXmlHttpRequest()) {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $entityManager = $this->getDoctrine()->getManager();
                $s_user->setPassword(
                    $password->encodePassword($s_user, $s_user->getPassword())
                );
                $entityManager->flush();

                return new Response('success', Response::HTTP_ACCEPTED);
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

    /**
     * @Route("/edit-auth-sub-{id}", name="edit_auth", methods={"POST"})
     */
    public function editAuth(Subuser $subuser, Request $request, AjaxService $ajaxService)
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        $this->denyAccessUnlessGranted('USER_MANAGE', $user);

        $form = $ajaxService->editSubAuth();

        if ($request->isXmlHttpRequest()) {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $entityManager = $this->getDoctrine()->getManager();

                $roles = $subuser->getRoles();
                $ccrud = 'command-crud';
                $mcrud = 'meal-crud';

                if ($form->get('command')->getData()) {
                    if (!array_key_exists($ccrud, $roles)) {
                        $roles[$ccrud] = true;
                    }
                }
                if (!$form->get('command')->getData()) {
                    if (array_key_exists($ccrud, $roles)) {
                        unset($roles[$ccrud]);
                    }
                }

                if ($form->get('meal')->getData()) {
                    if (!array_key_exists($mcrud, $roles)) {
                        $roles[$mcrud] = true;
                    }
                }
                if (!$form->get('meal')->getData()) {
                    if (array_key_exists($mcrud, $roles)) {
                        unset($roles[$mcrud]);
                    }
                }

                $subuser->setRoles($roles);

                $entityManager->flush();

                return new Response('success', Response::HTTP_ACCEPTED);
            }
        }

        return new Response('error', Response::HTTP_BAD_REQUEST);
    }

    /**
     * @Route("/delete-sub-{id}", name="delete", methods={"DELETE"})
     */
    public function delete(Subuser $subuser, UserRepository $userRepository, Request $request)
    {
        $this->denyAccessUnlessGranted('SUBUSER_DELETE', $subuser);

        if ($request->isXmlHttpRequest()) {
            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->remove($userRepository->getSubBySub($subuser));

            $entityManager->flush();

            return new Response('success', Response::HTTP_ACCEPTED);
        }

        return new Response('error', Response::HTTP_BAD_REQUEST);
    }
}
