<?php

namespace RWFramework\Framework\Http\Roles;

/**
 * Will be filled on a request. This will be used to check if the user has the required roles
 * Note: Only specific roles are required for a specific route
 */
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