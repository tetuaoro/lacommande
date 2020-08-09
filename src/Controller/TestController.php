<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\Mailer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/test", name="test_")
 */
class TestController extends AbstractController
{
    /**
     * @Route("/", name="index", methods={"GET"})
     */
    public function index()
    {
        $this->denyAccessUnlessGranted('MODE_DEV');

        return $this->render('test/index.html.twig', [
            'controller_name' => 'TestController',
        ]);
    }

    /**
     * @Route("/flash/{label}", name="flash", methods={"GET"}, requirements={"slug": "success|danger"})
     */
    public function flash(string $label)
    {
        $this->denyAccessUnlessGranted('MODE_DEV');

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
        $this->denyAccessUnlessGranted('MODE_DEV');

        if ($user) {
            $mailer->sendConfirmationNewUser($user);
            $this->addFlash('success', 'mail envoyé');
        }

        return $this->redirectToRoute('test_index');
    }
}
