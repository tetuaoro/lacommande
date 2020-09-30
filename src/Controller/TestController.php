<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\Type\RegisterType;
use App\Service\AjaxService;
use App\Service\Mailer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/test", name="test_")
 * @IsGranted("MODE_DEV")
 */
class TestController extends AbstractController
{
    /**
     * @Route("/", name="index", methods={"GET", "POST"})
     */
    public function index(AjaxService $ajaxService, Request $request)
    {
        return $this->render('test/index.html.twig', [
            'controller_name' => 'TestController',
            'tz' => date_default_timezone_get(),
        ]);
    }

    /**
     * @Route("/flash/{label}", name="flash", methods={"GET"}, requirements={"slug": "success|danger"})
     */
    public function flash(string $label)
    {
        if ('success' == $label) {
            $this->addFlash('success', 'mon message test.');
        } elseif ('danger' == $label) {
            $this->addFlash('danger', 'mon message test.');
        }

        return $this->redirectToRoute('test_index');
    }

    /**
     * @Route("/mailer/{id}", name="mailer", methods={"GET"})
     */
    public function mailer(User $user, Mailer $mailer)
    {
        if ($user) {
            $mailer->sendConfirmationNewUser($user);
            $this->addFlash('success', 'mail envoyé');
        }

        return $this->redirectToRoute('test_index');
    }

    /**
     * @Route("/form/dynamic", name="formulaire", methods={"GET", "POST"})
     */
    public function formulaire(Request $request)
    {
        $form = $this->createForm(RegisterType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // $choice = $form->get('choice')->getData();

            if ('lambda' == $form->get('choice')->getData()) {
                return $this->redirectToRoute('lambda_new');
            }

            return $this->redirectToRoute('user_new');
        }

        return $this->render('test/formulaire.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
