<?php

namespace RWFramework\Framework\Http\Roles;

class Role {
    public function __construct(
        private string $roleName
    ) {}

    public function getRoleName(): string {
        return $this->roleName;
    }
}