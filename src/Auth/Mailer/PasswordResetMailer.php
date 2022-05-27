<?php

namespace App\Auth\Mailer;

use Framework\Renderer\RendererInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class PasswordResetMailer
{
    private $mailer;
    private $renderer;
    private $from;

    public function __construct(MailerInterface $mailer, RendererInterface $renderer, string $from)
    {
        $this->mailer = $mailer;
        $this->renderer = $renderer;
        $this->from = $from;
    }

    public function send(string $to, array $params)
    {
        $message = (new Email())
                ->from($this->from)
                ->to($to)
                ->html($this->renderer->render('@auth/email/password.html', $params))
                ->text($this->renderer->render('@auth/email/password.text', $params));
        $this->mailer->send($message);
    }
}
