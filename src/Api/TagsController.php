<?php

namespace App\Api;

use App\Repository\TagsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/", name="tags_")
 */
class TagsController extends AbstractController
{
    /**
     * @Route("/all.json", name="index", methods={"GET"})
     */
    public function index(TagsRepository $tagsRepository)
    {
        return $this->json(
            $tagsRepository->findAll(),
            JsonResponse::HTTP_OK,
            [],
            ['groups' => ['tags']]
        );
    }
}
