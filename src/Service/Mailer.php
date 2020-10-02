<?php

namespace App\Service;

use App\Entity\Command;
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
    public const EMAIL = 'noreply@ariifood.pf';
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
        $token = rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=');

        $user->setConfirmationEmail($token);
        $this->em->flush();

        $message = (new TemplatedEmail())
            ->from(new Address(self::EMAIL, 'Arii Food'))
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
            ->from(new Address(self::EMAIL, 'Arii Food'))
            ->to(new Address($command->getEmail(), $command->getName()))
            ->subject('REF #'.$command->getReference().' - Commande de plat')
            ->htmlTemplate('mailer/command.html.twig')
            ->context([
                'command' => $command,
                'cart2' => $this->cart->getFullCartByProvider(),
            ])
        ;

        $this->mailer->send($message);
    }

    public function validateCommand(Command $command, int $bool, User $user)
    {
        $message = (new TemplatedEmail())
            ->from(new Address(self::EMAIL, 'Arii Food'))
            ->to(new Address($command->getEmail(), $command->getName()))
            ->subject(0 == $bool ? 'Commande refusée' : (1 == $bool ? 'Commande validée' : $user->getProvider()->getName()))
            ->htmlTemplate('mailer/validate_command.html.twig')
            ->context([
                'command' => $command,
                'user' => $user,
                'bool' => $bool,
            ])
        ;

        $this->mailer->send($message);
    }
}
