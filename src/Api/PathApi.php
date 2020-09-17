<?php

namespace App\Api;

use App\Entity\Meal;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/path", name="path_")
 */
class PathApi extends AbstractController
{
    /**
     * @Route("/meal/{id}", name="meal", methods={"GET"})
     */
    public function meal(Meal $meal)
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        $this->denyAccessUnlessGranted('USER_MANAGE', $user);

        return $this->json(
            $this->generateUrl('meal_show', ['id' => $meal->getId(), 'slug' => $meal->getSlug()]),
            JsonResponse::HTTP_OK,
        );
    }
}
