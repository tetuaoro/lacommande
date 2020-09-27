<?php

namespace App\Service;

use App\Entity\Command;
use App\Entity\Provider;
use App\Entity\User;
use App\Repository\CommandRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

class Mailer
{
    protected $mailer;
    protected $em;
    protected $request;
    protected $user;
    protected $cart;
    protected $commandRepo;

    public function __construct(
        MailerInterface $mailerInterface,
        EntityManagerInterface $entityManagerInterface,
        RequestStack $requestStack,
        UserRepository $userRepository,
        CartService $cartService,
        CommandRepository $commandRepository
    ) {
        $this->mailer = $mailerInterface;
        $this->em = $entityManagerInterface;
        $this->request = $requestStack;
        $this->user = $userRepository;
        $this->cart = $cartService;
        $this->commandRepo = $commandRepository;
    }

    public function sendConfirmationNewUser(User $user)
    {
        $email = $user->getEmail();

        $token = rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=');

        $user->setConfirmationEmail($token);
        $this->em->flush();

        $message = (new TemplatedEmail())
            ->from(new Address('lacommandeariifood@gmail.com', 'Arii Food'))
            ->to(new Address($user->getEmail(), $user->getName()))
            ->subject('Confirmation e-mail Arii Food')
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

    public function sendCommand(Command $command)
    {
        $message = (new TemplatedEmail())
            ->from(new Address('lacommandeariifood@gmail.com', 'Arii Food'))
            ->to(new Address($command->getEmail()))
            ->subject('REF #'.$command->getReference().' - Commande de plat')
            ->htmlTemplate('mailer/command.html.twig')
            ->context([
                'command' => $command,
                'cart2' => $this->cart->getFullCartByProvider(),
            ])
        ;

        $this->mailer->send($message);
    }

    public function validateCommand(Command $command, Provider $provider)
    {
        $message = (new TemplatedEmail())
            ->from(new Address('lacommandeariifood@gmail.com', 'Arii Food'))
            ->to(new Address($command->getEmail()))
            ->subject('Commande validée')
            ->htmlTemplate('mailer/validate_command.html.twig')
            ->context([
                'command' => $command,
                'provider' => $provider,
            ])
        ;

        $this->mailer->send($message);
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
