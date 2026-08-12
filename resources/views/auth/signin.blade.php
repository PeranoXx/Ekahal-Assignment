@extends('layouts.auth')

@section('title', 'Sign In')

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
            Sign in to your workspace
        </h1>
        <p class="font-body-sm text-body-sm text-on-surface-variant mt-sm text-center">
            Enter your email and password below to log in.
        </p>
    </div>

    <!-- Signin Form -->
    <form action="{{ url('/signin') }}" method="POST" class="flex flex-col gap-lg relative z-10">
        @csrf

        <!-- Email Field -->
        <x-input 
            label="Work Email" 
            name="email" 
            type="email" 
            placeholder="Enter your email" 
            value="{{ old('email') }}" 
            autofocus 
        />

        <!-- Password Field -->
        <x-input 
            label="Password" 
            name="password" 
            type="password" 
            placeholder="Enter your password" 
        />

        <!-- Remember Me -->
        <div class="flex items-center gap-sm">
            <input type="checkbox" name="remember" id="remember"
                class="h-4 w-4 rounded border-outline-variant bg-surface-container-lowest text-primary focus:ring-primary focus:ring-offset-background transition-colors" />
            <label for="remember" class="font-label-sm text-label-sm text-on-surface-variant select-none">
                Remember me
            </label>
        </div>

        <!-- Submit Button -->
        <x-button variant="primary">
            Sign In
            <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
        </x-button>
    </form>

    <!-- Footer Link -->
    <div class="mt-lg text-center relative z-10">
        <p class="font-body-sm text-body-sm text-on-surface-variant">
            Don't have an account? 
            <a class="text-primary hover:underline font-medium transition-colors" href="{{ url('/signup') }}">Sign Up</a>
        </p>
    </div>
</main>

@endsection
