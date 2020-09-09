<?php

namespace App\Controller;

use App\Entity\Menu;
use App\Entity\User;
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
        $menu = new Menu();
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        $form = $ajaxService->test_create_menu($menu, $user->getProvider());

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            dump('ok');
        }

        return $this->render('test/index.html.twig', [
            'controller_name' => 'TestController',
            'tz' => date_default_timezone_get(),
            'form' => $form->createView(),
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
}
