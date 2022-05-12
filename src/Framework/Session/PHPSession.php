<?php

namespace Framework\Session;

use ArrayAccess;

class PHPSession implements SessionInterface, ArrayAccess
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

    public function offsetExists(mixed $offset): bool
    {
        $this->enableSession();
        return array_key_exists($offset, $_SESSION);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->get($offset);
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->set($offset, $value);
    }

    public function offsetUnset(mixed $offset): void
    {
        $this->delete($offset);
    }
}
