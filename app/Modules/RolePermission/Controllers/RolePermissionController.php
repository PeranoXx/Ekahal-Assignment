<?php

namespace App\Modules\RolePermission\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\RolePermission\Services\RolePermissionService;
use App\Modules\RolePermission\Requests\CreateRoleRequest;
use App\Modules\RolePermission\Requests\CreatePermissionGroupRequest;
use App\Modules\RolePermission\Requests\UpdateRolePermissionsRequest;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class RolePermissionController extends Controller
{
    protected RolePermissionService $service;

    /**
     * Create a new controller instance.
     */
    public function __construct(RolePermissionService $service)
    {
        $this->service = $service;
    }

    /**
     * Display the settings and role permissions matrix.
     */
    public function index(Request $request): View
    {
        if (!auth()->user()->hasRole('Admin')) {
            abort(403, 'Unauthorized action.');
        }

        $selectedRoleId = $request->input('role_id') ? (int) $request->input('role_id') : null;
        $data = $this->service->getIndexData($selectedRoleId);

        return view('role-permission.index', $data);
    }

    /**
     * Store a new role.
     */
    public function storeRole(CreateRoleRequest $request): RedirectResponse
    {
        if (!auth()->user()->hasRole('Admin')) {
            abort(403, 'Unauthorized action.');
        }

        $role = $this->service->createRole($request->input('name'));

        return redirect()->route('role-permissions.index', ['role_id' => $role->id])
            ->with('success', "Role '{$role->name}' created successfully.");
    }

    /**
     * Store a new resource permission group (view, create, update, delete).
     */
    public function storePermission(CreatePermissionGroupRequest $request): RedirectResponse
    {
        if (!auth()->user()->hasRole('Admin')) {
            abort(403, 'Unauthorized action.');
        }

        $result = $this->service->createPermissionGroup($request->input('resource_name'));

        return redirect()->route('role-permissions.index')
            ->with('success', "Permission group '{$result['resource']}' created successfully ({$result['createdCount']} permissions added).");
    }

    /**
     * Update permissions for a specific role.
     */
    public function updatePermissions(UpdateRolePermissionsRequest $request): RedirectResponse
    {
        if (!auth()->user()->hasRole('Admin')) {
            abort(403, 'Unauthorized action.');
        }

        $role = $this->service->updateRolePermissions(
            (int) $request->input('role_id'),
            $request->input('permissions', [])
        );

        return redirect()->route('role-permissions.index', ['role_id' => $role->id])
            ->with('success', "Permissions for role '{$role->name}' saved successfully.");
    }

    /**
     * Delete a role.
     */
    public function destroyRole(Role $role): RedirectResponse
    {
        if (!auth()->user()->hasRole('Admin')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $this->service->deleteRole($role);
            return redirect()->route('role-permissions.index')
                ->with('success', "Role '{$role->name}' deleted successfully.");
        } catch (\Exception $e) {
            return redirect()->route('role-permissions.index', ['role_id' => $role->id])
                ->with('error', $e->getMessage());
        }
    }
}