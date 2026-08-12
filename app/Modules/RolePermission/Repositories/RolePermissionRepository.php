<?php

namespace App\Modules\RolePermission\Repositories;

use App\Modules\Users\Models\User;
use Illuminate\Support\Collection;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionRepository
{
    /**
     * Get all roles.
     */
    public function getAllRoles(): Collection
    {
        return Role::all();
    }

    /**
     * Find a role by ID.
     */
    public function findRole(int $id): ?Role
    {
        return Role::find($id);
    }

    /**
     * Get the first role in the database.
     */
    public function getFirstRole(): ?Role
    {
        return Role::first();
    }

    /**
     * Create a new role.
     */
    public function createRole(string $name): Role
    {
        return Role::create([
            'name' => $name,
            'guard_name' => 'web'
        ]);
    }

    /**
     * Count total users with a specific role name.
     */
    public function countUsersWithRole(string $roleName): int
    {
        return User::role($roleName)->count();
    }

    /**
     * Get all permissions.
     */
    public function getAllPermissions(): Collection
    {
        return Permission::all();
    }

    /**
     * Find or create a permission.
     */
    public function firstOrCreatePermission(string $name): Permission
    {
        return Permission::firstOrCreate([
            'name' => $name,
            'guard_name' => 'web'
        ]);
    }

    /**
     * Check if a permission exists by name.
     */
    public function permissionExists(string $name): bool
    {
        return Permission::where('name', $name)->exists();
    }

    /**
     * Create a permission.
     */
    public function createPermission(string $name): Permission
    {
        return Permission::create([
            'name' => $name,
            'guard_name' => 'web'
        ]);
    }

    /**
     * Find a role by name.
     */
    public function findRoleByName(string $name): ?Role
    {
        return Role::where('name', $name)->first();
    }
}
