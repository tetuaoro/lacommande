<?php

namespace App\Controller;

use App\Entity\Newletter;
use App\Repository\GalleryRepository;
use App\Repository\MealRepository;
use App\Repository\ProviderRepository;
use App\Service\AjaxService;
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
        $lastmeals = $mealRepo->findLastMeal(6);
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
     * @Route("/newletter", name="newletter", methods={"POST", "GET"})
     */
    public function newletter(Request $request, AjaxService $ajaxService)
    {
        $newletter = new Newletter();
        $form = $ajaxService->newletter($newletter);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($newletter);
                $em->flush();
                $this->addFlash('success', 'Vous êtes abonné !');
            }

            return $this->redirectToRoute('app_home');
        }

        return $this->render('app/app/newletter_form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/public/legal/terms-of-use", name="credit")
     */
    public function credit()
    {
        return $this->render('app/credit/index.html.twig', [
            'controller_name' => 'app:credit',
        ]);
    }

    /**
     * @Route("/public/legal/privacy-policy", name="rgpd")
     */
    public function rgpd()
    {
        return $this->render('app/rgpd/index.html.twig', [
            'controller_name' => 'app:rgpd',
        ]);
    }

    /**
     * @Route("/public/legal/privacy-policy-document", name="rgpd_doc")
     */
    public function rgpd_doc()
    {
        return $this->render('app/rgpd/rgpd.html.twig', [
            'controller_name' => 'app:rgpd',
        ]);
    }

    /**
     * @Route("/gallery", name="gallery", methods={"GET"})
     */
    public function gallery(GalleryRepository $galleryRepo)
    {
        $lastgalls = $galleryRepo->findLastest();

        return $this->render('app/app/gal.html.twig', [
            'lastgalleries' => $lastgalls,
        ]);
    }

    /**
     * Change the locale for the current user.
     *
     * @param string $lang
     *
     * @return array
     *
     * @Route("/setlocale/{lang}", name="setlocale")
     */
    public function setLocaleAction(string $lang = null)
    {
        return $this->redirectToRoute('app_home', ['_locale' => $lang]);
    }
}
