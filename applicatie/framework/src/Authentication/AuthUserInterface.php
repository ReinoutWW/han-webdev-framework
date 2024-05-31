<?php

namespace RWFramework\Framework\Authentication;

interface AuthUserInterface {
    public function getEmail(): string;
    public function getPassword(): string;
    public function getAuthId(): int|string; // Could be a UUID or an int for example
}