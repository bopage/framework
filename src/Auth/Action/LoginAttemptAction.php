<?php

namespace App\Auth\Action;

use App\Auth\DatabaseAuth;
use App\Auth\Event\LoginEvent;
use Framework\Action\RouterAwareAction;
use Framework\Renderer\RendererInterface;
use Framework\Response\RedirectResponse;
use Framework\Router;
use Framework\Session\FlashService;
use Framework\Session\SessionInterface;
use Graphikart\EventManager;
use Psr\Http\Message\ServerRequestInterface;

class LoginAttemptAction
{
    private $renderer;
    private $router;
    private $session;
    private $auth;
    private $eventManager;

    use RouterAwareAction;

    public function __construct(
        RendererInterface $renderer,
        Router $router,
        DatabaseAuth $auth,
        SessionInterface $session,
        EventManager $eventManager
    ) {
        $this->renderer = $renderer;
        $this->router = $router;
        $this->auth = $auth;
        $this->session = $session;
        $this->eventManager = $eventManager;
    }

    public function __invoke(ServerRequestInterface $request)
    {
        $params = $request->getParsedBody();
        $user = $this->auth->login($params['username'], $params['password']);
        if ($user) {
            $this->eventManager->trigger(new LoginEvent($user));
            $path = $this->session->get('auth.redirect') ?: $this->router->generateUri('admin');
            $this->session->delete('auth.redirect');
            return new RedirectResponse($path);
        } else {
            (new FlashService($this->session))->error('Identifiant ou mot de passe incorrect');
            return $this->redirect('auth.login');
        }
    }
}
