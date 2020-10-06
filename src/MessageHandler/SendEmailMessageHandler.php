<?php

namespace App\MessageHandler;

use App\Message\SendEmailMessage;
use App\Repository\CommandRepository;
use App\Repository\UserRepository;
use App\Service\Mailer;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class SendEmailMessageHandler implements MessageHandlerInterface
{
    private $mailer;
    private $userRepo;
    private $commandRepo;
    private $mailerInterface;

    // https://symfonycasts.com/screencast/mailer/route-context#why-is-the-link-broken
    // always 'localhost' with url()

    public function __construct(
        Mailer $mailer,
        UserRepository $userRepository,
        CommandRepository $commandRepository,
        MailerInterface $mailerInterface
    ) {
        $this->mailer = $mailer;
        $this->userRepo = $userRepository;
        $this->commandRepo = $commandRepository;
        $this->mailerInterface = $mailerInterface;
    }

    public function __invoke(SendEmailMessage $message)
    {
        $mode = $message->getMode();
        
        if (1 == $mode) {
            $this->mailerInterface->send($this->mailer->sendConfirmationNewUser($this->userRepo->find($message->getUser())));
        } elseif (2 == $mode) {
            $this->mailerInterface->send($this->mailer->sendCommand($this->commandRepo->find($message->getCommand())));
        } elseif (3 == $mode) {
            $this->mailerInterface->send($this->mailer->validateCommand($this->commandRepo->find($message->getCommand()), $message->getBool(), $this->userRepo->find($message->getUser())));
        }
    }
}
