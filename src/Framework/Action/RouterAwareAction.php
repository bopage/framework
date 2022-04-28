<?php

namespace Framework\Action;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;

/**
 * Rajoute des methodes liées à l'utilisation du router
 */
trait RouterAwareAction
{

    
    /**
     * Renvoie une réponse de redirection
     *
     * @param  string $path
     * @param  array $params
     * @return ResponseInterface
     */
    public function redirect(string $path, array $params = []): ResponseInterface
    {
        $redirect = $this->router->generateUri($path, $params);
        return (new Response())
                ->withStatus(301)
                ->withHeader('Location', $redirect);
    }
}
