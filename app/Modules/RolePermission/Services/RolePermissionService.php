<?php

namespace App\Modules\RolePermission\Services;

use App\Modules\RolePermission\Repositories\RolePermissionRepository;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class RolePermissionService
{
    protected RolePermissionRepository $repository;

    /**
     * Create a new service instance.
     */
    public function __construct(RolePermissionRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Get data required for the settings index view.
     */
    public function getIndexData(?int $selectedRoleId): array
    {
        $roles = $this->repository->getAllRoles();

        // Get selected role or default to the first
        $selectedRole = $selectedRoleId ? $this->repository->findRole($selectedRoleId) : $this->repository->getFirstRole();

        // If no roles exist (unexpected), create Admin role
        if (!$selectedRole && $roles->isEmpty()) {
            $selectedRole = $this->repository->createRole('Admin');
            $roles = collect([$selectedRole]);
        }

        // Count total users with this role
        $totalUsers = $selectedRole ? $this->repository->countUsersWithRole($selectedRole->name) : 0;

        // Group permissions by prefix/module
        $permissions = $this->repository->getAllPermissions();
        $modules = [];

        foreach ($permissions as $permission) {
            if ($permission->name === 'api-access' || $permission->name === 'export-data') {
                continue;
            }

            if (preg_match('/^(.+)-(view|create|update|delete)$/', $permission->name, $matches)) {
                $module = $matches[1];
                $action = $matches[2];
                $modules[$module][$action] = $permission;
            }
        }

        return [
            'roles' => $roles,
            'selectedRole' => $selectedRole,
            'totalUsers' => $totalUsers,
            'modules' => $modules,
        ];
    }

    /**
     * Create a new role.
     */
    public function createRole(string $name): Role
    {
        return $this->repository->createRole(trim($name));
    }

    /**
     * Create a new permission group (view, create, update, delete).
     */
    public function createPermissionGroup(string $resourceName): array
    {
        $resource = strtolower(trim($resourceName));
        $resource = str_replace(' ', '-', $resource);

        $actions = ['view', 'create', 'update', 'delete'];
        $createdCount = 0;

        foreach ($actions as $action) {
            $permissionName = $resource . '-' . $action;
            if (!$this->repository->permissionExists($permissionName)) {
                $this->repository->createPermission($permissionName);
                $createdCount++;
            }
        }

        // Auto-assign new permissions to the Admin role
        $adminRole = $this->repository->findRoleByName('Admin');
        if ($adminRole) {
            $permissionsToAssign = array_map(fn($action) => $resource . '-' . $action, $actions);
            $adminRole->givePermissionTo($permissionsToAssign);
        }

        // Forget spatie permission cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        return [
            'resource' => $resource,
            'createdCount' => $createdCount
        ];
    }

    /**
     * Sync permissions to a role.
     */
    public function updateRolePermissions(int $roleId, array $permissions): Role
    {
        $role = $this->repository->findRole($roleId);
        if (!$role) {
            throw new \InvalidArgumentException("Role not found.");
        }

        $role->syncPermissions($permissions);

        // Forget cached permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        return $role;
    }

    /**
     * Delete a role.
     */
    public function deleteRole(Role $role): bool
    {
        if (strtolower($role->name) === 'admin') {
            throw new \Exception("The default Admin role cannot be deleted.");
        }

        $userCount = $this->repository->countUsersWithRole($role->name);
        if ($userCount > 0) {
            throw new \Exception("Cannot delete role '{$role->name}' because it is assigned to users.");
        }

        return $role->delete();
    }
}
