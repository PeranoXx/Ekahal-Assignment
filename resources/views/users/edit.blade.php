@extends('layouts.dashboard')

@section('title', 'Edit User')

@section('content')
<!-- Canvas -->
<main class="flex-1 p-margin-desktop max-w-[640px] mx-auto w-full">
    <!-- Page Header -->
    <div class="mb-xl">
        <h2 class="font-headline-lg text-headline-lg text-primary tracking-tight mb-xs">Edit User Account</h2>
        <p class="font-body-md text-body-md text-on-surface-variant">Update the user details, reset password, or change system role permissions.</p>
    </div>

    <!-- Form Card -->
    <div class="bg-surface-container-lowest border border-outline-variant rounded-lg p-lg shadow-sm">
        <form action="{{ route('users.update', $user) }}" method="POST" class="flex flex-col gap-lg">
            @csrf
            @method('PUT')

            <!-- Name Input -->
            <x-input 
                label="Full Name" 
                name="name" 
                type="text" 
                placeholder="Enter user's name" 
                value="{{ old('name', $user->name) }}" 
                autofocus
            />

            <!-- Email Input -->
            <x-input 
                label="Email Address" 
                name="email" 
                type="email" 
                placeholder="Enter email address" 
                value="{{ old('email', $user->email) }}" 
            />

            <!-- Password Input -->
            <x-input 
                label="Password" 
                name="password" 
                type="password" 
                placeholder="Leave blank to keep current password" 
            />

            <!-- Role Input -->
            <div class="flex flex-col gap-2">
                <label for="role" class="font-label-sm text-label-sm text-on-surface-variant">System Role</label>
                <select id="role" name="role" class="w-full bg-surface border border-outline-variant rounded py-2 px-3 font-body-sm text-body-sm text-on-surface focus:outline-none focus:border-primary transition-colors focus:ring-0">
                    <option value="User" {{ old('role', $user->hasRole('Admin') ? 'Admin' : 'User') === 'User' ? 'selected' : '' }}>User (Standard permissions)</option>
                    <option value="Admin" {{ old('role', $user->hasRole('Admin') ? 'Admin' : 'User') === 'Admin' ? 'selected' : '' }}>Admin (Full permission settings)</option>
                </select>
                @error('role')
                    <span class="text-xs text-error mt-1">{{ $message }}</span>
                @enderror
            </div>

            <!-- Actions -->
            <div class="flex items-center gap-md mt-sm">
                <x-button variant="primary">
                    Update User
                </x-button>
                <a href="{{ route('users.index') }}" class="font-label-md text-label-md text-on-surface-variant hover:text-primary transition-colors">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</main>
@endsection
