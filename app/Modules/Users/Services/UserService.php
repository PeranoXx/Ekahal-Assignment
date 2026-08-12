<?php

namespace App\Modules\Users\Services;

use App\Modules\Users\Models\User;
use App\Modules\Users\Repositories\UserRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;

class UserService
{
    protected UserRepository $userRepository;

    /**
     * Create a new service instance.
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function getPaginatedUsers(int $perPage = 10, ?string $search = null, ?string $status = 'all', ?string $sortBy = null, ?string $sortOrder = 'asc'): LengthAwarePaginator
    {
        return $this->userRepository->paginate($perPage, $search, $status, $sortBy, $sortOrder);
    }

    /**
     * Create a new user and assign a role.
     */
    public function createUser(array $data): User
    {
        $data['password'] = Hash::make($data['password']);
        
        $user = $this->userRepository->create($data);

        if (!empty($data['role'])) {
            $user->syncRoles($data['role']);
        }

        return $user;
    }

    /**
     * Update a user's details and sync their role.
     */
    public function updateUser(User $user, array $data): User
    {
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $this->userRepository->update($user, $data);

        if (isset($data['role'])) {
            $user->syncRoles($data['role']);
        }

        return $user;
    }

    /**
     * Delete a user.
     */
    public function deleteUser(User $user): bool
    {
        return $this->userRepository->delete($user);
    }
}
