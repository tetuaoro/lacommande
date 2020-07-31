<?php

namespace App\Controller;

use App\Repository\GalleryRepository;
use App\Repository\MealRepository;
use App\Repository\ProviderRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/", name="app_")
 */
class AppController extends AbstractController
{
    /**
     * @Route("/", name="index")
     * @Route("/accueil", name="home")
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
     * @Route("/contact", name="contact")
     */
    public function contact(Request $request)
    {
        $form = $this->createFormBuilder()
            ->add('nom', TextType::class, [
                'translation_domain' => 'form',
            ])
            ->add('prenom', TextType::class, [
                'translation_domain' => 'form',
            ])
            ->add('email', EmailType::class, [
                'translation_domain' => 'form',
            ])
            ->add('sujet', TextType::class, [
                'translation_domain' => 'form',
            ])
            ->add('message', TextareaType::class, [
                'translation_domain' => 'form',
            ])
            ->getForm()
         ;
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            dump($form->getData());
        }

        return $this->render('app/app/contact.html.twig', [
            'controller_name' => 'contact',
            'form' => $form->createView(),
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
}
