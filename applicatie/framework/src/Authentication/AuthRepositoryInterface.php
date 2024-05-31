<?php

namespace RWFramework\Framework\Authentication;

interface AuthRepositoryInterface {
    public function findByEmail(string $username): ?AuthUserInterface;
}