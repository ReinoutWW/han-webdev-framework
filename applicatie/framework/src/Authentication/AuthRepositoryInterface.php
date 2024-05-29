<?php

namespace RWFramework\Framework\Authentication;

interface AuthRepositoryInterface {
    public function findByUsername(string $username): ?AuthUserInterface;
}