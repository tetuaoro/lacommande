<?php

namespace App\MessageHandler;

use App\Message\SendEmailMessage;
use App\Repository\CommandRepository;
use App\Repository\UserRepository;
use App\Service\Mailer;
use Symfony\Bridge\Twig\Extension\RoutingExtension;
use Symfony\Bridge\Twig\Mime\BodyRenderer;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Mime\Crypto\SMimeSigner;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment;
use Twig\Extra\CssInliner\CssInlinerExtension;
use Twig\Extra\Inky\InkyExtension;
use Twig\Loader\FilesystemLoader;

final class SendEmailMessageHandler implements MessageHandlerInterface
{
    private $mailer;
    private $userRepo;
    private $commandRepo;
    private $mailerInterface;
    private $twigBodyRenderer;
    private $signer;

    // https://symfonycasts.com/screencast/mailer/route-context#why-is-the-link-broken
    // always 'localhost' with url()

    public function __construct(
        Mailer $mailer,
        UserRepository $userRepository,
        CommandRepository $commandRepository,
        MailerInterface $mailerInterface,
        UrlGeneratorInterface $urlGeneratorInterface,
        $templateFolder,
        $crt,
        $key,
        $passphrase
    ) {
        $this->mailer = $mailer;
        $this->userRepo = $userRepository;
        $this->commandRepo = $commandRepository;
        $this->mailerInterface = $mailerInterface;

        // =====

        $twigEnv = (new Environment(new FilesystemLoader($templateFolder)));
        $twigEnv->addExtension(new InkyExtension());
        $twigEnv->addExtension(new CssInlinerExtension());
        $twigEnv->addExtension(new RoutingExtension($urlGeneratorInterface));

        $this->signer = new SMimeSigner($crt, $key, $passphrase);
        $this->twigBodyRenderer = new BodyRenderer($twigEnv);
    }

    public function __invoke(SendEmailMessage $message)
    {
        $mode = $message->getMode();
        $mail = '';

        if (1 == $mode) {
            $mail = $this->mailer->sendConfirmationNewUser($this->userRepo->find($message->getUser()));
        } elseif (2 == $mode) {
            $mail = $this->mailer->sendCommand($this->commandRepo->find($message->getCommand()));
        } elseif (3 == $mode) {
            $mail = $this->mailer->validateCommand($this->commandRepo->find($message->getCommand()), $message->getBool(), $this->userRepo->find($message->getUser()));
        }

        $this->twigBodyRenderer->render($mail);
        $this->mailerInterface->send($this->signer->sign($mail));
    }
}
