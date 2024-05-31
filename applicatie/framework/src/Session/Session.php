<?php

namespace RWFramework\Framework\Session;

class Session implements SessionInterface {
    private const FLASH_KEY = 'flash';
    public const NOTIFICATION_ERROR = 'error';
    public const NOTIFICATION_SUCCESS = 'success';
    public const NOTIFICATION_INFO = 'info';
    public const NOTIFICATION_WARNING = 'warning';
    public const AUTH_ID_KEY = 'auth_id';
    public const USER_KEY = 'user';

    public function start(): void
    {
        // Like explained in college, we need to start the session in the constructor
        // This way, we can track the session throughout the application
        if(session_status() === PHP_SESSION_NONE) {
            session_start();
        }
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

    public function hasNotification(): bool {
        return $this->hasFlash(self::NOTIFICATION_ERROR) || 
            $this->hasFlash(self::NOTIFICATION_SUCCESS) || 
            $this->hasFlash(self::NOTIFICATION_INFO) || 
            $this->hasFlash(self::NOTIFICATION_WARNING);
    }

    public function getNotificationTypes(): array {
        return [
            self::NOTIFICATION_ERROR,
            self::NOTIFICATION_SUCCESS,
            self::NOTIFICATION_INFO,
            self::NOTIFICATION_WARNING
        ];
    }

    public function isAuthenticated(): bool {
        return $this->has(Session::AUTH_ID_KEY);
    }

    public function getUser(): object {
        return $this->get(Session::USER_KEY);
    }
}