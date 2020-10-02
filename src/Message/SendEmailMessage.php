<?php

namespace App\Message;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;

final class SendEmailMessage
{
    private $enveloppe;

    public function __construct(TemplatedEmail $templatedEmail)
    {
        $this->enveloppe = $templatedEmail;
    }

    public function getEnveloppe(): TemplatedEmail
    {
        return $this->enveloppe;
    }
}
