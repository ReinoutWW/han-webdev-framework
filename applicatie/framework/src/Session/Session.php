<?php

namespace RWFramework\Framework\Session;

class Session implements SessionInterface {
    private const FLASH_KEY = 'flash';

    public function __construct()
    {
        session_start();
    }

    public function set(string $key, $value): void
    {
        $_SESSION[$key] = $value;
    }

    public function get(string $key, $default = null)
    {
        return $_SESSION[$key] ?? $default;
    }

    public function remove(string $key): void
    {
        unset($_SESSION[$key]);
    }

    public function has(string $key): bool
    {
        return isset($_SESSION[$key]);
    }

    public function getFlash(string $type): array
    {
   
    }

    public function setFlash(string $type, string $message): void
    {
        
    }

    public function hasFlash(string $type): bool
    {

    }

    public function clearFlash(): void
    {
        unset($_SESSION[self::FLASH_KEY]);
    }
}