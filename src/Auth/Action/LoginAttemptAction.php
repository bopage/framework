<?php

namespace App\Auth\Action;

use App\Auth\DatabaseAuth;
use Framework\Action\RouterAwareAction;
use Framework\Renderer\RendererInterface;
use Framework\Response\RedirectResponse;
use Framework\Router;
use Framework\Session\FlashService;
use Framework\Session\SessionInterface;
use Psr\Http\Message\ServerRequestInterface;

class LoginAttemptAction
{
    private $renderer;
    private $router;
    private $session;
    private $auth;

    use RouterAwareAction;

    public function __construct(
        RendererInterface $renderer,
        Router $router,
        DatabaseAuth $auth,
        SessionInterface $session
    ) {
        $this->renderer = $renderer;
        $this->router = $router;
        $this->auth = $auth;
        $this->session = $session;
    }

    public function __invoke(ServerRequestInterface $request)
    {
        $params = $request->getParsedBody();
        $user = $this->auth->login($params['username'], $params['password']);
        if ($user) {
            $path = $this->session->get('auth.redirect') ?: $this->router->generateUri('admin');
            $this->session->delete('auth.redirect');
            return new RedirectResponse($path);
        } else {
            (new FlashService($this->session))->error('Identifiant ou mot de passe incorrect');
            return $this->redirect('auth.login');
        }
    }
}
