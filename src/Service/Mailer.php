<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

class Mailer
{
    private $mailer;
    private $em;
    private $request;
    private $user;

    public function __construct(
        MailerInterface $mailerInterface,
        EntityManagerInterface $entityManagerInterface,
        RequestStack $requestStack,
        UserRepository $userRepository
    ) {
        $this->mailer = $mailerInterface;
        $this->em = $entityManagerInterface;
        $this->request = $requestStack;
        $this->user = $userRepository;
    }

    public function sendConfirmationNewUser(User $user)
    {
        $email = $user->getEmail();

        $token = rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=');

        $user->setConfirmationEmail($token);
        $this->em->flush();

        $message = (new TemplatedEmail())
            ->from('lacommandeariifood@gmail.com')
            ->to(new Address($email))
            ->subject('Confirmation email Arii Food')
            ->htmlTemplate('mailer/signup.html.twig')
            ->context([
                'user' => $user,
            ])
        ;

        $this->mailer->send($message);
    }

    public function confirmNewUser(User $user, $token)
    {
        if ($token == $user->getConfirmationEmail()) {
            $user->setConfirmationEmail(null);
            $this->em->flush();

            return true;
        }

        return false;
    }

    public function userNotifyMessage(User $user, $message, $subject)
    {
        $email = $user->getEmail();

        $message_ = (new TemplatedEmail())
            ->from('chansondufenua@gmail.com')
            ->to(new Address($email))
            ->subject($subject)
            ->htmlTemplate('emails/userNotify.html.twig')
            ->context([
                'user' => $user,
                'message' => $message,
                'subject' => $subject,
            ])
                ;

        $this->mailer->send($message_);
    }

    public function userNotifyNotification($form)
    {
        $email = $form->getData()['email'];
        $message = $form->getData()['message'];
        $subject = $form->getData()['subject'];

        $message_ = (new TemplatedEmail())
            ->from('chansondufenua@gmail.com')
            ->to(new Address($email))
            ->subject($subject)
            ->htmlTemplate('emails/userNotify.html.twig')
            ->context([
                'message' => $message,
                'subject' => $subject,
            ])
                ;

        $this->mailer->send($message_);
    }
}
