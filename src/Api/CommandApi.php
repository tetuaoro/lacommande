<?php

namespace App\Api;

use App\Repository\CommandRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

/**
 * @Route("/command", name="command_")
 */
class CommandApi extends AbstractController
{
    /**
     * @Route("/all.json", name="index", methods={"GET"})
     */
    public function index(CommandRepository $commandRepository)
    {
        $this->denyAccessUnlessGranted('ROLE_PROVIDER');

        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        $defaultContext = [
            AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object, $format, $context) {
                return $object->getId();
            },
            AbstractNormalizer::GROUPS => 'commandjs',
        ];

        $commands = $commandRepository->findByProviderOrderByDate($user->getProvider()->getId());
        dump($commands);

        return $this->json(
            $commands,
            JsonResponse::HTTP_OK,
            [],
            $defaultContext
        );
    }
}
