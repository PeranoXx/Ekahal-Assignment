@extends('layouts.auth')

@section('title', 'Signup')

@section('content')
<!-- Auth Container -->
<main class="w-full max-w-[440px] bg-surface border border-outline-variant rounded-xl p-lg md:p-xl shadow-2xl shadow-black/50 relative overflow-hidden">
    <!-- Subtle decorative glow -->
    <div class="absolute -top-32 -left-32 w-64 h-64 bg-surface-container-highest rounded-full blur-[100px] opacity-50 pointer-events-none"></div>
    
    <!-- Brand Header -->
    <div class="flex flex-col items-center mb-xl relative z-10">
        <div class="w-12 h-12 bg-primary text-on-primary rounded-lg flex items-center justify-center mb-md">
            <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">tv_signin</span>
        </div>
        <h1 class="font-headline-lg-mobile md:font-headline-lg text-headline-lg-mobile md:text-headline-lg text-primary text-center">
           Create a new workspace
        </h1>
        <p class="font-body-sm text-body-sm text-on-surface-variant mt-sm text-center">
           Enter your details below to create your account
        </p>
    </div>

    <!-- Signup Form -->
    <form action="{{ url('/signup') }}" method="POST" class="flex flex-col gap-lg relative z-10">
        @csrf

        <!-- Full Name Field -->
        <x-input 
            label="Full Name" 
            name="name" 
            placeholder="Enter your name" 
            value="{{ old('name') }}" 
        />

        <!-- Email Field -->
        <x-input 
            label="Work Email" 
            name="email" 
            type="email" 
            placeholder="Enter your email" 
            value="{{ old('email') }}" 
        />

        <!-- Password Field -->
        <x-input 
            label="Password" 
            name="password" 
            type="password" 
            placeholder="Enter your password" 
            help="Must be at least 8 characters long." 
        />

        <!-- Confirm Password Field -->
        <x-input 
            label="Confirm Password" 
            name="password_confirmation" 
            type="password" 
            placeholder="Repeat your password" 
        />

        <!-- Submit Button -->
        <x-button variant="primary">
            Create Account
            <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
        </x-button>
    </form>

    <!-- Footer Link -->
    <div class="mt-lg text-center relative z-10">
        <p class="font-body-sm text-body-sm text-on-surface-variant">
            Already have an account? 
            <a class="text-primary hover:underline font-medium transition-colors" href="{{ url('/signin') }}">Sign In</a>
        </p>
    </div>
</main>

@endsection
