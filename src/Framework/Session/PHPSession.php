<?php

namespace Framework\Session;

class PHPSession implements SessionInterface
{
    private function enableSession()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function get(string $key, $default = null)
    {
        $this->enableSession();
        if (array_key_exists($key, $_SESSION)) {
            return $_SESSION[$key];
        }

        return $default;
    }

    public function set(string $key, $value): void
    {
        $this->enableSession();
        $_SESSION[$key] = $value;
    }

    public function delete(string $key): void
    {
        $this->enableSession();
        unset($_SESSION[$key]);
    }
}
