<?php

namespace App\Contact;

use Framework\Renderer\RendererInterface;
use Framework\Response\RedirectResponse;
use Framework\Session\FlashService;
use Framework\Validator;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class ContactAction
{
    private $renderer;

    private $flashService;

    private $mailer;

    private $to;

    public function __construct(
        string $to,
        RendererInterface $renderer,
        FlashService $flashService,
        MailerInterface $mailer
    ) {
        $this->to = $to;
        $this->renderer = $renderer;
        $this->flashService = $flashService;
        $this->mailer = $mailer;
    }

    public function __invoke(ServerRequestInterface $request)
    {
        if ($request->getMethod() === 'GET') {
            return $this->renderer->render('@contact/contact');
        }
        $params = $request->getParsedbody();
        $validator = (new Validator($params))
                ->required('name', 'email', 'content')
                ->length('name', 5)
                ->email('email')
                ->length('content', 15);
        if ($validator->isValid()) {
            $this->flashService->success('Merci pour votre message');
            $message = (new Email())
                    ->to($this->to)
                    ->from($params['email'])
                    ->html($this->renderer->render('@contact/email/contact.html', $params))
                    ->text($this->renderer->render('@contact/email/contact.text', $params));
            $this->mailer->send($message);
            return new RedirectResponse((string)$request->getUri());
        } else {
            $this->flashService->error('Merci de corriger vos erreurs');
            $errors = $validator->getErrors();
            return $this->renderer->render('@contact/contact', compact('errors'));
        }
    }
}
