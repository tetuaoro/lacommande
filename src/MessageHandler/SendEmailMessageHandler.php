<?php

namespace App\MessageHandler;

use App\Message\SendEmailMessage;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class SendEmailMessageHandler implements MessageHandlerInterface
{
    private $mailer;

    public function __construct(
        MailerInterface $mailerInterface
    ) {
        $this->mailer = $mailerInterface;
    }

    public function __invoke(SendEmailMessage $message)
    {
        $this->mailer->send($message->getEnveloppe());
    }
}
