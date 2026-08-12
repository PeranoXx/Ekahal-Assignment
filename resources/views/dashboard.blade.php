@extends('layouts.dashboard')

@section('title', 'Dashboard')

@section('content')
<!-- Canvas -->
<main class="flex-1 overflow-y-auto  flex items-center justify-center">
    <div class="w-full max-w-7xl text-center p-xl bg-surface-container-lowest rounded-2xl border border-outline-variant shadow-sm flex flex-col items-center gap-md">
        <div class="w-16 h-16 rounded-2xl bg-primary/10 text-primary flex items-center justify-center mb-sm">
            <span class="material-symbols-outlined text-[36px]" style="font-variation-settings: 'FILL' 1;">space_dashboard</span>
        </div>
        <h2 class="font-headline-md text-headline-md text-on-surface tracking-tight">Welcome to InventoryOS</h2>
        <p class="font-body-md text-body-md text-on-surface-variant leading-relaxed">
            This is your workspace dashboard. Use the sidebar menu to navigate through system capabilities and manage permissions.
        </p>
    </div>
</main>
@endsection
