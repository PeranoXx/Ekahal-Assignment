<!-- SideNavBar -->
<aside class="fixed h-full w-72 left-0 top-0 border-r border-outline-variant bg-surface dark:bg-surface flex flex-col p-md gap-sm">
    <!-- Brand / Header -->
    <div class="flex items-center gap-sm mb-lg px-sm">
        <div class="w-8 h-8 rounded-DEFAULT bg-primary flex items-center justify-center text-on-primary">
            <span class="material-symbols-outlined font-[FILL] text-[20px]" style="font-variation-settings: 'FILL' 1;">dashboard</span>
        </div>
        <div>
            <h1 class="font-headline-md text-headline-md text-primary dark:text-primary font-bold tracking-tight">Admin</h1>
            <p class="font-label-sm text-label-sm text-on-surface-variant">Platform</p>
        </div>
    </div>
    
    <!-- Navigation Tabs -->
    <nav class="flex-1 flex flex-col gap-xs">
        <!-- Dashboard -->
        <a class="flex items-center gap-sm px-sm py-2 rounded-lg {{ request()->routeIs('dashboard') ? 'bg-secondary-container text-on-secondary-container font-medium scale-[0.98] transition-transform' : 'text-on-surface-variant hover:text-on-surface hover:bg-surface-container-highest transition-all duration-200' }}" href="{{ route('dashboard') }}">
            <span class="material-symbols-outlined text-[20px]" style="{{ request()->routeIs('dashboard') ? "font-variation-settings: 'FILL' 1;" : '' }}">space_dashboard</span>
            <span class="font-label-md text-label-md">Dashboard</span>
        </a>
        @can('user-view')
        <!-- Users -->
        <a class="flex items-center gap-sm px-sm py-2 rounded-lg {{ request()->routeIs('users.*') ? 'bg-secondary-container text-on-secondary-container font-medium scale-[0.98] transition-transform' : 'text-on-surface-variant hover:text-on-surface hover:bg-surface-container-highest transition-all duration-200' }}" href="{{ route('users.index') }}">
            <span class="material-symbols-outlined text-[20px]" style="{{ request()->routeIs('users.*') ? "font-variation-settings: 'FILL' 1;" : '' }}">group</span>
            <span class="font-label-md text-label-md">Users</span>
        </a>
        @endcan
        @can('product-view')
        <!-- Products -->
        <a class="flex items-center gap-sm px-sm py-2 rounded-lg {{ request()->routeIs('products.*') ? 'bg-secondary-container text-on-secondary-container font-medium scale-[0.98] transition-transform' : 'text-on-surface-variant hover:text-on-surface hover:bg-surface-container-highest transition-all duration-200' }}" href="{{ route('products.index') }}">
            <span class="material-symbols-outlined text-[20px]" style="{{ request()->routeIs('products.*') ? "font-variation-settings: 'FILL' 1;" : '' }}">inventory_2</span>
            <span class="font-label-md text-label-md">Products</span>
        </a>
        @endcan
    </nav>
    
    <!-- CTA & Footer Tabs -->
    <div class="mt-auto flex flex-col gap-sm">
        @role('Admin')
        <!-- Role Permissions -->
        <a class="flex items-center gap-sm px-sm py-2 rounded-lg {{ request()->routeIs('role-permissions.*') ? 'bg-secondary-container text-on-secondary-container font-medium scale-[0.98] transition-transform' : 'text-on-surface-variant hover:text-on-surface hover:bg-surface-container-highest transition-all duration-200' }}" href="{{ route('role-permissions.index') }}">
            <span class="material-symbols-outlined text-[20px]" style="{{ request()->routeIs('role-permissions.*') ? "font-variation-settings: 'FILL' 1;" : '' }}">settings</span>
            <span class="font-label-md text-label-md">Role Permissions</span>
        </a>
        @endrole
        <div class="h-px w-full bg-outline-variant my-xs"></div>
        
        <a class="flex items-center gap-sm px-sm py-2 rounded-lg text-on-surface-variant hover:text-on-surface hover:bg-surface-container-highest transition-all duration-200" href="https://github.com/PeranoXx/Ekahal-Assignment" target="_blank">
            <span class="material-symbols-outlined text-[20px]">terminal</span>
            <span class="font-label-md text-label-md">Repository</span>
        </a>
        <a class="flex items-center gap-sm px-sm py-2 rounded-lg text-on-surface-variant hover:text-on-surface hover:bg-surface-container-highest transition-all duration-200" href="https://github.com/PeranoXx/Ekahal-Assignment#ekahal-assignment" target="_blank">
            <span class="material-symbols-outlined text-[20px]">menu_book</span>
            <span class="font-label-md text-label-md">Documentation</span>
        </a>
    </div>
</aside>
