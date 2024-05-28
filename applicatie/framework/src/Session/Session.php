<?php

namespace RWFramework\Framework\Session;

class Session implements SessionInterface {
    private const FLASH_KEY = 'flash';

    public function start(): void
    {
        // Like explained in college, we need to start the session in the constructor
        // This way, we can track the session throughout the application
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
        // Get the flash messages from the session
        $flash = $this->get(self::FLASH_KEY, []);
        
        // If the type exists in the flash messages
        if (isset($flash[$type])) {
            // Get the messages from the requested type
            $messages = $flash[$type];

            // Unset because flash messages are one-time only.
            unset($flash[$type]);

            // Set the flash messages back in the session
            $this->set(self::FLASH_KEY, $flash);

            // Return the messages
            return $messages;
        }

        return [];
    }

    public function setFlash(string $type, string $message): void
    {
        $flash = $this->get(self::FLASH_KEY, []);
        $flash[$type][] = $message;
        $this->set(self::FLASH_KEY, $flash);
    }

    public function hasFlash(string $type): bool
    {
        $flash = $this->get(self::FLASH_KEY, []);
        return isset($flash[$type]);
    }

    public function clearFlash(): void
    {
        unset($_SESSION[self::FLASH_KEY]);
    }
}