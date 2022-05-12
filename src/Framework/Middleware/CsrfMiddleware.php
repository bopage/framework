<?php

namespace Framework\Middleware;

use ArrayAccess;
use Framework\Exceptions\CsrfInvalidException;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TypeError;

class CsrfMiddleware implements MiddlewareInterface
{
    private $formKey;

    private $sessionKey;

    private $limit;

    private $session;

    public function __construct(
        &$session,
        int $limit = 50,
        string $formKey = '_csrf',
        string $sessionKey = 'csrf'
    ) {
        $this->validSession($session);
        $this->session = &$session;
        $this->limit = $limit;
        $this->formKey = $formKey;
        $this->sessionKey = $sessionKey;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (in_array($request->getMethod(), ['POST', 'PUT', 'DELETE'])) {
            $params = $request->getParsedBody();
            if (array_key_exists($this->formKey, $params)) {
                $csrfList = $this->session[$this->sessionKey] ?? [];
                if (in_array($params[$this->formKey], $csrfList)) {
                    $this->userToken($params[$this->formKey]);
                    return  $handler->handle($request);
                } else {
                    $this->rejet();
                }
            } else {
                $this->rejet();
            }
        } else {
            return $handler->handle($request);
        }
    }

    /**
     * Permet de générer un token
     *
     * @return string
     */
    public function generateToken(): string
    {
        $token = bin2hex(random_bytes(16));
        $csrfList = $this->session[$this->sessionKey] ?? [];
        $csrfList[] = $token;
        $this->session[$this->sessionKey] = $csrfList;
        $this->limitTokens();
        return $token;
    }

    public function rejet()
    {
        throw new CsrfInvalidException();
    }

    /**
     * Permet d'enlever dans la liste de token le token existant
     *
     * @param  string $token
     * @return void
     */
    private function userToken(string $token): void
    {
        $tokens = array_filter($this->session[$this->sessionKey], function ($t) use ($token) {
            return $token !== $t;
        });
        $this->session[$this->sessionKey] = $tokens;
    }

    /**
     * Permet de limitet les tokens
     *
     * @return void
     */
    private function limitTokens(): void
    {
        $tokens = $this->session[$this->sessionKey] ?? [];
        if (count($tokens) > $this->limit) {
            array_shift($tokens);
        }
        $this->session[$this->sessionKey] = $tokens;
    }

    private function validSession($session)
    {
        if (!is_array($session) && !$session instanceof ArrayAccess) {
            throw new TypeError('La session passée au middleware n\'est pas traitable comme un tableau');
        }
    }

    /**
     * Get the value of formKey
     */
    public function getFormKey()
    {
        return $this->formKey;
    }
}
