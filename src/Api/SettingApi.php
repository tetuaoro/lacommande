<?php

namespace App\Api;

use App\Entity\Provider;
use App\Service\AjaxService;
use App\Service\Storage;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

/**
 * @Route("/setting", name="setting_")
 */
class SettingApi extends AbstractController
{
    /**
     * @Route("/", name="index", methods={"GET"})
     */
    public function index(Security $security)
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        $this->denyAccessUnlessGranted('USER_MANAGE', $user);

        $provider = '';
        if ($security->isGranted('ROLE_SUBUSER')) {
            $provider = $user->getSubuser()->getProvider();
        } elseif ($security->isGranted('ROLE_PROVIDER')) {
            $provider = $user->getProvider();
        }

        $defaultContext = [
            AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object, $format, $context) {
                return $object->getId();
            },
            AbstractNormalizer::GROUPS => 'settingjs',
        ];

        return $this->json($provider, JsonResponse::HTTP_OK, [], $defaultContext);
    }

    /**
     * @Route("/edit-set-{id}", name="edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Storage $storage, Provider $provider, AjaxService $ajaxService): Response
    {
        $this->denyAccessUnlessGranted('PROVIDER_EDIT', $provider);

        $form = $ajaxService->edit_provider($provider);

        if ($request->isXmlHttpRequest()) {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $entityManager = $this->getDoctrine()->getManager();
                $image = $form->get('image')->getData();
                if ($image) {
                    $info = $storage->uploadProviderImage($image, $provider);
                    $provider->setBgImg($info['mediaLink'])
                        ->setImgInfo($info)
                    ;
                }
                $entityManager->flush();

                return new Response('sucess', Response::HTTP_ACCEPTED);
            }

            if ($form->isSubmitted() && !$form->isValid()) {
                return $this->render(
                    'provider/_form.html.twig',
                    [
                        'form' => $form->createView(),
                    ],
                    new Response('error', Response::HTTP_BAD_REQUEST)
                );
            }
        }

        return $this->render('provider/_form.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
