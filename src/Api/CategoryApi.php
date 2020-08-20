<?php

namespace App\Api;

use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/category", name="tags_")
 */
class CategoryApi extends AbstractController
{
    /**
     * @Route("/all.json", name="index", methods={"GET"})
     */
    public function index(CategoryRepository $categoryRepository)
    {
        return $this->json(
            $categoryRepository->findAll(),
            JsonResponse::HTTP_OK,
            [],
            ['groups' => ['category_api']]
        );
    }
}
