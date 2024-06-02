<?php 

namespace RWFramework\Framework\Http\Roles;

class RequiredRoles {
    public function __construct(
        private array $roles = []
    ) {}

    public function getRequiredRoles(): array {
        return $this->roles;
    }
}