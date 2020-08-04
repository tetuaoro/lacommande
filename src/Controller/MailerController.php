<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\Mailer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/mailer", name="mailer_")
 */
class MailerController extends AbstractController
{
    /**
     * @Route("/{id}-{token}", name="confirmation", methods={"GET"}, requirements={"token": "[a-zA-Z0-9\-\_]*"})
     */
    public function confirmation(User $user, string $token, Mailer $mailer)
    {
        $confirm = $mailer->confirmeNewUser($user, $token);

        if ($confirm) {
            $this->addFlash('flash_success', 'Email confirmé avec success !');
        } else {
            $this->addFlash('flash_error', 'Une erreur est survenue lors de la confimation ! Veuillez réessayer.');
        }

        return $this->redirectToRoute('user_show', ['id' => $user->getId()]);
    }

    /**
     * @Route("/", name="index")
     */
    public function index(Request $request, Mailer $mailer)
    {
        $form = $this->createFormBuilder()
            ->add('email', EmailType::class)
            ->add('send', SubmitType::class)
            ->getForm()
        ;
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $mailer->sendConfirmationNewUser($this->getUser());
        }

        return $this->render('mailer/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
