<?php

namespace RWFramework\Framework\Http\Roles;

interface RoleManagerInterface {
    public function getRequiredRoles(): array;

    public function addRequiredRoles(array $roles): void;
    public function hasRequiredRoles(RoleUserInterface $user): bool;
}