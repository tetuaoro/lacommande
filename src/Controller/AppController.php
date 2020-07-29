<?php

namespace App\Controller;

use App\Repository\GalleryRepository;
use App\Repository\MealRepository;
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
    public function index(MealRepository $mealRepo, GalleryRepository $galleryRepo)
    {
        $lastmeals = $mealRepo->findLastMeal();
        $lastgalls = $galleryRepo->findLastest();

        return $this->render('app/index.html.twig', [
            'controller_name' => 'app',
            'lastmeals' => $lastmeals,
            'lastgalleries' => $lastgalls,
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
