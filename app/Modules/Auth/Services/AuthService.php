<?php

namespace App\Modules\Auth\Services;

use App\Modules\Users\Models\User;

use App\Modules\Users\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    /**
     * The user repository instance.
     */
    protected UserRepository $userRepository;

    /**
     * Create a new service instance.
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Register a new user and log them in.
     *
     * @param array $data
     * @return User
     */
    public function register(array $data): User
    {
        $data['password'] = Hash::make($data['password']);
        $user = $this->userRepository->create($data);

        $user->assignRole('User');

        Auth::login($user);

        return $user;
    }

    /**
     * Log in a user with the given credentials.
     *
     * @param array $credentials
     * @param bool $remember
     * @return bool
     */
    public function login(array $credentials, bool $remember = false): bool
    {
        return Auth::attempt($credentials, $remember);
    }

    /**
     * Log out the currently authenticated user.
     *
     * @return void
     */
    public function logout(): void
    {
        Auth::logout();
    }
}
