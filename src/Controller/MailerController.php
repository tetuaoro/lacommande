<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\Mailer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
        $confirm = $mailer->confirmNewUser($user, $token);

        if ($confirm) {
            $this->addFlash('success', 'Email confirmé avec success !');
        } else {
            $this->addFlash('danger', 'Ce lien n\'est plus valide ! Veuillez réessayer.');
        }

        // @var \App\Entity\User $user
        if ($user_ = $this->getUser()) {
            return $this->redirectToRoute('user_show', ['id' => $user_->getId(), 'slug' => $user->getSlug()]);
        }

        return $this->redirectToRoute('app_index');
    }
}
