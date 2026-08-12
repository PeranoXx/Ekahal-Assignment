@extends('layouts.dashboard')

@section('title', 'Users Management')

@section('content')
<!-- Canvas -->
<main class="flex-1 p-margin-desktop">
    <!-- Page Header & Actions -->
    <div class="flex justify-between items-end mb-xl">
        <div>
            <h2 class="font-headline-lg text-headline-lg text-primary tracking-tight mb-xs">Users Management</h2>
            <p class="font-body-md text-body-md text-on-surface-variant">View, edit, and manage system administrators and general users.</p>
        </div>
        <div class="flex gap-sm">
            @can('user-create')
            <a href="{{ route('users.create') }}" class="flex items-center gap-2 px-4 py-2 bg-primary text-on-primary rounded font-label-md text-label-md hover:bg-surface-tint transition-all">
                <span class="material-symbols-outlined text-[18px]">add</span>
                Add User
            </a>
            @endcan
        </div>
    </div>

    <!-- Alert Notifications -->
    @if (session('success'))
        <div class="alert-notification mb-lg p-4 bg-emerald-950 border border-emerald-500/30 rounded-lg text-emerald-400 font-body-sm text-body-sm flex items-center gap-2">
            <span class="material-symbols-outlined text-[20px]">check_circle</span>
            <span>{{ session('success') }}</span>
        </div>
    @endif
    @if (session('error'))
        <div class="alert-notification mb-lg p-4 bg-rose-950 border border-rose-500/30 rounded-lg text-rose-400 font-body-sm text-body-sm flex items-center gap-2">
            <span class="material-symbols-outlined text-[20px]">error</span>
            <span>{{ session('error') }}</span>
        </div>
    @endif
    
    <!-- Data Table Card -->
    <div class="border rounded-lg overflow-hidden border-outline-variant bg-surface-container-lowest">
        <!-- Table Controls / Filter Row -->
        <div class="p-4 border-b border-outline-variant flex justify-between items-center bg-surface">
            <!-- Left side: Filters (all, active, deleted) -->
            <div class="flex gap-2">
                <button type="button" class="js-status-filter px-3 py-1.5 rounded text-label-sm font-label-md border transition-colors bg-primary text-on-primary border-primary cursor-pointer" data-status="all">All</button>
                <button type="button" class="js-status-filter px-3 py-1.5 rounded text-label-sm font-label-md border transition-colors bg-surface border-outline-variant text-on-surface-variant hover:text-on-surface hover:border-outline cursor-pointer" data-status="active">Active</button>
                <button type="button" class="js-status-filter px-3 py-1.5 rounded text-label-sm font-label-md border transition-colors bg-surface border-outline-variant text-on-surface-variant hover:text-on-surface hover:border-outline cursor-pointer" data-status="deleted">Deleted</button>
            </div>
            
            <!-- Right side: Search bar -->
            <div class="relative w-72">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant text-[18px]">search</span>
                <input id="user-search" class="w-full bg-surface-container-lowest border border-outline-variant rounded py-1.5 pl-10 pr-4 font-body-sm text-body-sm text-on-surface placeholder-on-surface-variant focus:outline-none focus:border-primary transition-colors focus:ring-0" placeholder="Search users by name or email..." type="text"/>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-surface font-label-caps text-label-caps text-on-surface-variant border-b border-outline-variant">
                    <tr>
                        <!-- <th class="py-md px-lg font-medium w-10">
                            <input class="w-4 h-4 rounded border-outline-variant text-primary focus:ring-primary/20" type="checkbox"/>
                        </th> -->
                        <th class="py-md px-lg font-medium">
                            <button type="button" class="js-sort flex items-center gap-xs font-semibold text-on-surface hover:text-primary transition-colors cursor-pointer" data-sort="name">
                                Name
                                <span class="material-symbols-outlined text-[16px] js-sort-icon" data-field="name">arrow_upward</span>
                            </button>
                        </th>
                        <th class="py-md px-lg font-medium">
                            <button type="button" class="js-sort flex items-center gap-xs font-semibold text-on-surface hover:text-primary transition-colors cursor-pointer" data-sort="email">
                                Email
                                <span class="material-symbols-outlined text-[16px] js-sort-icon" data-field="email">swap_vert</span>
                            </button>
                        </th>
                        <th class="py-md px-lg font-medium text-on-surface-variant">Role</th>
                        <th class="py-md px-lg font-medium text-on-surface-variant">Status</th>
                        <th class="py-md px-lg font-medium">
                            <button type="button" class="js-sort flex items-center gap-xs font-semibold text-on-surface hover:text-primary transition-colors cursor-pointer" data-sort="updated_at">
                                Last Login
                                <span class="material-symbols-outlined text-[16px] js-sort-icon" data-field="updated_at">swap_vert</span>
                            </button>
                        </th>
                        <th class="py-md px-lg font-medium text-right w-28 text-on-surface-variant">Actions</th>
                    </tr>
                </thead>
                <tbody id="users-table-body" class="font-body-sm text-body-sm text-on-surface divide-y divide-outline-variant transition-opacity duration-150">
                    @include('users.partials.table_rows')
                </tbody>
            </table>
        </div>
        
        <!-- Pagination Container -->
        <div id="users-pagination-container" class="p-4 border-t border-outline-variant flex justify-between items-center bg-surface">
            @include('users.partials.pagination')
        </div>
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('user-search');
    const tableBody = document.getElementById('users-table-body');
    const paginationContainer = document.getElementById('users-pagination-container');
    const statusButtons = document.querySelectorAll('.js-status-filter');

    let currentStatus = 'all';
    let currentSortBy = 'name';
    let currentSortOrder = 'asc';
    let debounceTimer;

    // Perform the AJAX fetch
    function fetchUsers(url, searchQuery = '', statusFilter = 'all', sortBy = 'name', sortOrder = 'asc') {
        const fetchUrl = new URL(url);
        if (searchQuery) {
            fetchUrl.searchParams.set('search', searchQuery);
        } else {
            fetchUrl.searchParams.delete('search');
        }
        fetchUrl.searchParams.set('status', statusFilter);
        fetchUrl.searchParams.set('sort_by', sortBy);
        fetchUrl.searchParams.set('sort_order', sortOrder);

        // Add opacity to show loading state
        tableBody.style.opacity = '0.4';

        fetch(fetchUrl, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            tableBody.innerHTML = data.html;
            paginationContainer.innerHTML = data.pagination;
            tableBody.style.opacity = '1';
        })
        .catch(error => {
            console.error('Error fetching users:', error);
            tableBody.style.opacity = '1';
        });
    }

    // Bind click events on sort headers
    document.querySelectorAll('.js-sort').forEach(button => {
        button.addEventListener('click', function () {
            const sortBy = this.getAttribute('data-sort');
            if (currentSortBy === sortBy) {
                // Toggle order
                currentSortOrder = currentSortOrder === 'asc' ? 'desc' : 'asc';
            } else {
                currentSortBy = sortBy;
                currentSortOrder = 'asc';
            }
            
            // Update sort icon UI
            updateSortIcons();

            // Fetch users with new sorting
            fetchUsers('{{ route('users.index') }}', searchInput.value.trim(), currentStatus, currentSortBy, currentSortOrder);
        });
    });

    function updateSortIcons() {
        document.querySelectorAll('.js-sort-icon').forEach(icon => {
            const field = icon.getAttribute('data-field');
            if (field === currentSortBy) {
                icon.textContent = currentSortOrder === 'asc' ? 'arrow_drop_up' : 'arrow_drop_down';
                icon.classList.remove('text-on-surface-variant');
                icon.classList.add('text-primary');
            } else {
                icon.textContent = '';
                icon.classList.remove('text-primary');
                icon.classList.add('text-on-surface-variant');
            }
        });
    }

    // Initialize sort icons
    updateSortIcons();

    // Bind click events on status filter buttons
    statusButtons.forEach(button => {
        button.addEventListener('click', function () {
            // Update active states
            statusButtons.forEach(btn => {
                btn.classList.remove('bg-primary', 'text-on-primary', 'border-primary');
                btn.classList.add('bg-surface', 'border-outline-variant', 'text-on-surface-variant');
            });
            this.classList.remove('bg-surface', 'border-outline-variant', 'text-on-surface-variant');
            this.classList.add('bg-primary', 'text-on-primary', 'border-primary');

            currentStatus = this.getAttribute('data-status');
            fetchUsers('{{ route('users.index') }}', searchInput.value.trim(), currentStatus, currentSortBy, currentSortOrder);
        });
    });

    // Debounce the input search
    searchInput.addEventListener('input', function () {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            fetchUsers('{{ route('users.index') }}', searchInput.value.trim(), currentStatus, currentSortBy, currentSortOrder);
        }, 300); // 300ms debounce
    });

    // Intercept clicks on paginated page links and load via AJAX
    paginationContainer.addEventListener('click', function (e) {
        const link = e.target.closest('.js-page-link');
        if (link) {
            e.preventDefault();
            const url = link.getAttribute('href');
            fetchUsers(url, searchInput.value.trim(), currentStatus, currentSortBy, currentSortOrder);
        }
    });

    // Auto-remove alert notifications after 3 seconds
    const alerts = document.querySelectorAll('.alert-notification');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.transition = 'opacity 0.5s ease';
            alert.style.opacity = '0';
            setTimeout(() => {
                alert.remove();
            }, 500);
        }, 3000);
    });
});
</script>
@endsection
