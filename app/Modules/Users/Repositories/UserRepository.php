<?php

namespace App\Modules\Users\Repositories;

use App\Modules\Users\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class UserRepository
{
    public function paginate(int $perPage = 10, ?string $search = null, ?string $status = 'all', ?string $sortBy = null, ?string $sortOrder = 'asc'): LengthAwarePaginator
    {
        if ($status === 'deleted') {
            $query = User::with('roles')->onlyTrashed();
        } elseif ($status === 'active') {
            $query = User::with('roles');
        } else {
            $query = User::with('roles')->withTrashed();
        }
        

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        // Apply sorting
        $allowedSortFields = ['name', 'email', 'updated_at'];
        $sortBy = in_array($sortBy, $allowedSortFields) ? $sortBy : 'name';
        $sortOrder = in_array(strtolower($sortOrder), ['asc', 'desc']) ? $sortOrder : 'asc';
        
        $query->orderBy($sortBy, $sortOrder);

        return $query->paginate($perPage);
    }

    /**
     * Find a user by ID.
     */
    public function find(int $id): ?User
    {
        return User::find($id);
    }

    /**
     * Create a new user.
     */
    public function create(array $data): User
    {
        return User::create($data);
    }

    /**
     * Update an existing user.
     */
    public function update(User $user, array $data): bool
    {
        return $user->update($data);
    }

    /**
     * Delete a user.
     */
    public function delete(User $user): ?bool
    {
        return $user->delete();
    }
}
