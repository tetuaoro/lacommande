<?php

namespace App\Api;

use App\Entity\Notification;
use App\Repository\NotificationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

/**
 * @Route("/notification", name="notification_api_")
 */
class NotificationApi extends AbstractController
{
    /**
     * @Route("/", name="index", methods={"GET"})
     */
    public function index(NotificationRepository $notificationRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_PROVIDER');

        $defaultContext = [
            AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object, $format, $context) {
                return $object->getId();
            },
            AbstractNormalizer::GROUPS => 'notifjs',
        ];

        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        return $this->json(
            $notificationRepository->findNotificationByProvider($user->getProvider()),
            200,
            [],
            $defaultContext
        );
    }

    /**
     * @Route("/count", name="count", methods={"GET"})
     */
    public function count(NotificationRepository $notificationRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_PROVIDER');

        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        return $this->json($notificationRepository->getNotifCount($user->getProvider()));
    }

    /**
     * @Route("/delete-notif-{id}", name="delete", methods={"DELETE"})
     */
    public function delete(Request $request, Notification $notification): Response
    {
        $this->denyAccessUnlessGranted('ROLE_PROVIDER');

        if ($request->isXmlHttpRequest()) {
            /** @var \App\Entity\User $user */
            $user = $this->getUser();

            $notification->removeProvider($user->getProvider());

            $this->getDoctrine()->getManager()->flush();

            return new Response('sucess', Response::HTTP_ACCEPTED);
        }

        return new Response('error', Response::HTTP_BAD_REQUEST);
    }
}
