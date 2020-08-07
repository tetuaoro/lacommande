<?php

namespace App\Controller;

use App\Repository\GalleryRepository;
use App\Repository\MealRepository;
use App\Repository\ProviderRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/", name="app_")
 */
class AppController extends AbstractController
{
    /**
     * @Route("/", name="index")
     * @Route({"fr": "/accueil", "en": "/welcome"}, name="home")
     */
    public function index(MealRepository $mealRepo, ProviderRepository $providerRepo)
    {
        $lastmeals = $mealRepo->findLastMeal();
        $meal_recap = $mealRepo->findLastMeal(8);
        $meal_popular = $mealRepo->findPopular(4);
        $prov_recap = $providerRepo->findLastProvider(8);

        return $this->render('app/index.html.twig', [
            'controller_name' => 'app',
            'lastmeals' => $lastmeals,
            'meal_recap' => $meal_recap,
            'prov_recap' => $prov_recap,
            'meal_popular' => $meal_popular,
        ]);
    }

    /**
     * @Route("/public/credit/cgu-cgv-ml", name="credit")
     */
    public function credit()
    {
        return $this->render('app/credit/cg.html.twig', [
            'controller_name' => 'app:credit',
        ]);
    }

    public function gallery_html(string $who, GalleryRepository $galleryRepo)
    {
        $lastgalls = $galleryRepo->findLastest();

        return $this->render('base/gallery/gal.html.twig', [
            'lastgalleries' => $lastgalls,
            'who' => $who,
        ]);
    }

    /**
     * Change the locale for the current user.
     *
     * @param string $lang
     *
     * @return array
     *
     * @Route("/setlocale/{lang}-{route}", name="setlocale")
     */
    public function setLocaleAction(string $route, string $lang = null, Request $request)
    {
        return $this->redirectToRoute($route, ['_locale' => $lang]);
    }

    /**
     * @Route("/test/{label}", name="test", methods={"GET"}, requirements={"slug": "success|danger"})
     */
    public function test(string $label)
    {
        $this->denyAccessUnlessGranted('MODE_DEV');

        if ('success' == $label) {
            $this->addFlash('success', 'mon message test.');
        }
        if ('danger' == $label) {
            $this->addFlash('danger', 'mon message test.');
        }

        return $this->redirectToRoute('contact_new');
    }
}
