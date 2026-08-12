@extends('layouts.dashboard')

@section('title', 'Add User')

@section('content')
<!-- Canvas -->
<main class="flex-1 p-margin-desktop max-w-[640px] mx-auto w-full">
    <!-- Page Header -->
    <div class="mb-xl">
        <h2 class="font-headline-lg text-headline-lg text-primary tracking-tight mb-xs">Add New User</h2>
        <p class="font-body-md text-body-md text-on-surface-variant">Create a new system user and configure their roles and access settings.</p>
    </div>

    <!-- Form Card -->
    <div class="bg-surface-container-lowest border border-outline-variant rounded-lg p-lg shadow-sm">
        <form action="{{ route('users.store') }}" method="POST" class="flex flex-col gap-lg">
            @csrf

            <!-- Name Input -->
            <x-input 
                label="Full Name" 
                name="name" 
                type="text" 
                placeholder="Enter user's name" 
                value="{{ old('name') }}" 
                autofocus
            />

            <!-- Email Input -->
            <x-input 
                label="Email Address" 
                name="email" 
                type="email" 
                placeholder="Enter email address" 
                value="{{ old('email') }}" 
            />

            <!-- Password Input -->
            <x-input 
                label="Password" 
                name="password" 
                type="password" 
                placeholder="Set a password (min. 8 characters)" 
            />

            <!-- Role Input -->
            <div class="flex flex-col gap-2">
                <label for="role" class="font-label-sm text-label-sm text-on-surface-variant">System Role</label>
                <select id="role" name="role" class="w-full bg-surface border border-outline-variant rounded py-2 px-3 font-body-sm text-body-sm text-on-surface focus:outline-none focus:border-primary transition-colors focus:ring-0">
                    @foreach($roles as $role)
                        <option value="{{ $role->name }}" {{ old('role', 'User') === $role->name ? 'selected' : '' }}>
                            {{ $role->name }}
                        </option>
                    @endforeach
                </select>
                @error('role')
                    <span class="text-xs text-error mt-1">{{ $message }}</span>
                @enderror
            </div>

            <!-- Actions -->
            <div class="flex items-center gap-md mt-sm">
                <x-button variant="primary">
                    Create User
                </x-button>
                <a href="{{ route('users.index') }}" class="font-label-md text-label-md text-on-surface-variant hover:text-primary transition-colors">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</main>
@endsection
