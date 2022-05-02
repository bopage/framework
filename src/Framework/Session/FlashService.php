<?php

namespace Framework\Session;

class FlashService
{
    private $session;

    private $sessionKey = 'flash';

    private $messages;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }
    
    /**
     * Sauvegarde les valeurs avec la clé success
     *
     * @param  string $message
     * @return void
     */
    public function success(string $message)
    {
        $flash = $this->session->get($this->sessionKey, []);
        $flash['success'] = $message;
        $this->session->set($this->sessionKey, $flash);
    }

     /**
     * Sauvegarde les valeurs avec la clé error
     *
     * @param  string $message
     * @return void
     */
    public function error(string $message)
    {
        $flash = $this->session->get($this->sessionKey, []);
        $flash['error'] = $message;
        $this->session->set($this->sessionKey, $flash);
    }
    
    /**
     * Récupère les valeurs
     *
     * @param  string $type
     * @return string|null
     */
    public function get(string $type): ?string
    {
        if (is_null($this->messages)) {
            /** @var array  */
            $this->messages = $this->session->get($this->sessionKey, []);
            $this->session->delete('flash');
        }
        if (array_key_exists($type, $this->messages)) {
            return $this->messages[$type];
        }

        return null;
    }
}
