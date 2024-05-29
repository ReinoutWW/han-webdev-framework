<?php

namespace RWFramework\Framework\Authentication;

interface SessionAuthInterface {
    public function authenticate(string $username, string $password): bool;
    public function login(AuthUserInterface $user): void;
    public function logout(): void;
    public function user(): ?AuthUserInterface;
}