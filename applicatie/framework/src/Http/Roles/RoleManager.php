<?php

namespace RWFramework\Framework\Http\Roles;

class RoleManager implements RoleManagerInterface {
    public function __construct(
        private array $roles = []
    ) {}

    public function getRequiredRoles(): array {
        return $this->roles;
    }

    public function addRequiredRoles(array $roles): void {
        foreach($roles as $role) {
            $this->addRequiredRole($role);
        }
    }

    public function addRequiredRole(Role $role): void {
        $this->roles[] = $role;
    }

    public function hasRequiredRoles(RoleUserInterface $user): bool {
        $userRoles = $user->getRoles();
        $requiredRoles = $this->getRequiredRoles();

        if(!$requiredRoles) {
            return true;
        }

        foreach($requiredRoles as $requiredRole) {
            if(!in_array($requiredRole->getRoleName(), $userRoles)) {
                return false;
            }
        }

        return true;
    }
}