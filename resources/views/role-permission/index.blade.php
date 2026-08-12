@extends('layouts.dashboard')

@section('title', 'Role Permissions')

@section('content')
<!-- Canvas -->
<main class="flex-1 p-margin-desktop">
    <!-- Page Content -->
    <div class="max-w-7xl mx-auto w-full">
        
        <!-- Page Header -->
        <div class="mb-xl flex flex-col md:flex-row md:items-end justify-between gap-md">
            <div>
                <h2 class="font-headline-lg-mobile md:font-headline-lg text-headline-lg-mobile md:text-headline-lg text-on-surface mb-xs">Role Permissions</h2>
                <p class="font-body-md text-body-md text-on-surface-variant">Manage access controls and capabilities for different user roles within the system.</p>
            </div>
            <div class="flex flex-wrap gap-sm">
                <button type="button" class="js-open-modal px-md py-sm rounded-lg border border-outline-variant bg-surface-container-lowest text-primary font-body-sm text-body-sm font-medium hover:bg-surface-container-low transition-colors shadow-sm flex items-center gap-xs cursor-pointer" data-target="create-role-modal">
                    <span class="material-symbols-outlined text-[18px]">add</span> Add Role
                </button>
                <button type="button" class="js-open-modal px-md py-sm rounded-lg border border-outline-variant bg-surface-container-lowest text-primary font-body-sm text-body-sm font-medium hover:bg-surface-container-low transition-colors shadow-sm flex items-center gap-xs cursor-pointer" data-target="create-permission-modal">
                    <span class="material-symbols-outlined text-[18px]">add</span> Add Permission
                </button>
                <a href="{{ route('role-permissions.index', ['role_id' => $selectedRole->id]) }}" class="px-md py-sm rounded-lg border border-outline-variant bg-surface-container-lowest text-on-surface-variant font-body-sm text-body-sm font-medium hover:bg-surface-container-low transition-colors shadow-sm flex items-center justify-center cursor-pointer">Cancel</a>
                <button type="submit" form="permissions-form" class="px-md py-sm rounded-lg bg-primary text-on-primary font-body-sm text-body-sm font-medium hover:bg-surface-tint transition-colors shadow-sm flex items-center justify-center cursor-pointer">Save Changes</button>
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
        @if ($errors->any())
            <div class="alert-notification mb-lg p-4 bg-rose-950/30 border border-rose-500/30 rounded-lg text-rose-400 font-body-sm text-body-sm flex flex-col gap-1">
                @foreach ($errors->all() as $error)
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-[20px]">error</span>
                        <span>{{ $error }}</span>
                    </div>
                @endforeach
            </div>
        @endif

        <!-- Role Selector -->
        <div class="bg-surface-container-lowest rounded-xl border border-outline-variant p-md md:p-lg mb-xl shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-lg">
            <div class="w-full md:max-w-2xl">
                <label class="block font-label-caps text-label-caps text-on-surface-variant mb-xs">Select Role to Edit</label>
                <select id="role-selector" name="role_id" class="w-full bg-surface border border-outline-variant rounded-lg py-sm px-md font-body-md text-body-md text-on-surface focus:outline-none focus:ring-0 cursor-pointer">
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}" @if($selectedRole->id === $role->id) selected @endif>{{ $role->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="flex items-center gap-md border-l border-outline-variant pl-lg w-full md:w-auto justify-between md:justify-start">
                
                @php
                    $canDelete = strtolower($selectedRole->name) !== 'admin' && $totalUsers === 0;
                @endphp

                <form action="{{ route('role-permissions.roles.destroy', $selectedRole->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete the role \'{{ $selectedRole->name }}\'?');" class="m-0 p-0">
                    @csrf
                    @method('DELETE')
                    
                    @if(!$canDelete)
                        <div class="relative group inline-block">
                            <button type="button" disabled class="px-md py-sm rounded-lg bg-outline-variant/30 text-on-surface-variant/40 font-body-sm text-body-sm font-medium cursor-not-allowed flex items-center gap-xs shadow-sm">
                                <span class="material-symbols-outlined text-[18px]">delete</span> Delete Role
                            </button>
                            <div class="absolute bottom-full right-0 mb-2 hidden group-hover:block bg-inverse-surface text-inverse-on-surface font-body-xs text-xs py-1.5 px-2.5 rounded shadow-lg whitespace-nowrap z-50">
                                @if(strtolower($selectedRole->name) === 'admin')
                                    Default Admin role cannot be deleted.
                                @else
                                    Cannot delete role assigned to active users.
                                @endif
                            </div>
                        </div>
                    @else
                        <button type="submit" class="px-md py-sm rounded-lg bg-error text-on-error font-body-sm text-body-sm font-medium hover:opacity-90 active:scale-[0.98] transition-all flex items-center gap-xs shadow-sm cursor-pointer">
                            <span class="material-symbols-outlined text-[18px]">delete</span> Delete Role
                        </button>
                    @endif
                </form>
            </div>
        </div>

        <form id="permissions-form" action="{{ route('role-permissions.update') }}" method="POST">
            @csrf
            <input type="hidden" name="role_id" value="{{ $selectedRole->id }}" />

            <!-- Permissions Matrix -->
            <div class="flex flex-col gap-lg mb-xl">
                @foreach($modules as $module => $actions)
                    @php
                        $moduleTitle = Str::title(str_replace('-', ' ', $module)) . ' Management';
                    @endphp
                    <div class="section-container bg-surface-container-lowest rounded-xl border border-outline-variant shadow-sm overflow-hidden">
                        <!-- Section Header -->
                        <div class="bg-surface-container-low px-lg py-md border-b border-outline-variant flex justify-between items-center">
                            <h3 class="font-title-md text-title-md text-on-surface flex items-center gap-sm">
                                <!-- <span class="material-symbols-outlined text-primary">widgets</span> -->
                                {{ $moduleTitle }}
                            </h3>
                            <div class="flex items-center gap-sm">
                                <span class="font-body-sm text-body-sm text-on-surface-variant">Select All in Section</span>
                                <div class="relative inline-block w-10 mr-2 align-middle select-none transition duration-200 ease-in">
                                    <input class="js-section-toggle absolute block w-5 h-5 rounded-full bg-white border-4 border-outline-variant checked:border-primary appearance-none cursor-pointer z-10 top-0 left-0 checked:translate-x-5 transition-all duration-300" id="toggle-{{ Str::slug($module) }}" type="checkbox"/>
                                    <label class="block overflow-hidden h-5 rounded-full bg-outline-variant peer-checked:bg-primary cursor-pointer transition-colors duration-300" for="toggle-{{ Str::slug($module) }}"></label>
                                </div>
                            </div>
                        </div>
                        <!-- Table -->
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-surface border-b border-outline-variant font-label-caps text-label-caps text-on-surface-variant">
                                        <th class="py-md px-lg w-1/2">Permission</th>
                                        <th class="py-md px-lg text-center w-32">View</th>
                                        <th class="py-md px-lg text-center w-32">Create</th>
                                        <th class="py-md px-lg text-center w-32">Edit</th>
                                        <th class="py-md px-lg text-center w-32">Delete</th>
                                    </tr>
                                </thead>
                                <tbody class="font-body-md text-body-md text-on-surface">
                                    <tr class="border-b border-outline-variant last:border-b-0 hover:bg-surface-bright transition-colors">
                                        <td class="py-md px-lg">
                                            <div class="font-medium text-on-surface">Manage {{ Str::title(str_replace('-', ' ', $module)) }}</div>
                                            <div class="font-body-sm text-body-sm text-on-surface-variant">Configure access controls and capabilities for the {{ str_replace('-', ' ', $module) }} resource.</div>
                                        </td>
                                        
                                        {{-- Actions: view, create, update (Edit), delete --}}
                                        @foreach(['view', 'create', 'update', 'delete'] as $action)
                                            <td class="py-md px-lg text-center">
                                                @if(isset($actions[$action]))
                                                    @php $perm = $actions[$action]; @endphp
                                                    <input 
                                                        type="checkbox" 
                                                        name="permissions[]" 
                                                        value="{{ $perm->name }}" 
                                                        class="custom-checkbox w-5 h-5 rounded border-outline-variant text-primary focus:ring-primary-fixed cursor-pointer transition-all" 
                                                        @if($selectedRole->hasPermissionTo($perm->name)) checked @endif
                                                    />
                                                @else
                                                    <span class="text-on-surface-variant opacity-50">-</span>
                                                @endif
                                            </td>
                                        @endforeach
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endforeach
            </div>
        </form>

        <!-- Create Role Modal -->
        <div id="create-role-modal" class="fixed inset-0 z-100 bg-black/60 backdrop-blur-xs flex items-center justify-center hidden opacity-0 transition-all duration-300 ease-out">
            <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-lg w-[450px] max-w-full mx-md shadow-2xl transform scale-95 transition-all duration-300 ease-out relative">
                <!-- Close Button -->
                <button type="button" class="js-close-modal absolute top-4 right-4 text-on-surface-variant hover:text-on-surface hover:bg-surface-container-low p-1 rounded-full transition-all cursor-pointer" data-target="create-role-modal">
                    <span class="material-symbols-outlined text-[20px]">close</span>
                </button>

                <div class="flex items-center gap-sm mb-md">
                    <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center text-primary">
                        <span class="material-symbols-outlined text-[24px]">shield_person</span>
                    </div>
                    <div>
                        <h3 class="font-title-md text-title-md text-on-surface">Create New Role</h3>
                        <p class="font-body-sm text-body-sm text-on-surface-variant">Define a new user access role</p>
                    </div>
                </div>
                
                <form action="{{ route('role-permissions.roles.store') }}" method="POST" class="flex flex-col gap-md">
                    @csrf
                    <div class="flex flex-col gap-sm">
                        <label class="font-label-sm text-label-sm text-on-surface" for="role-name-input">
                            Role Name
                        </label>
                        <input 
                            type="text"
                            id="role-name-input"
                            name="name"
                            placeholder="e.g. Supervisor" 
                            required 
                            class="w-full bg-surface border border-outline-variant rounded-lg py-2.5 px-3.5 font-body-sm text-body-sm text-on-surface placeholder:text-on-surface-variant focus:outline-none focus:ring-2 focus:ring-primary-fixed focus:border-primary transition-all"
                        />
                    </div>
                    <div class="flex justify-end gap-sm mt-lg">
                        <button type="button" class="js-close-modal px-md py-sm rounded-lg border border-outline-variant bg-surface text-on-surface hover:bg-surface-container-low transition-colors text-body-sm font-medium cursor-pointer" data-target="create-role-modal">Cancel</button>
                        <button type="submit" class="px-md py-2.5 rounded-lg bg-primary text-on-primary hover:bg-surface-tint transition-colors text-body-sm font-medium shadow-sm cursor-pointer flex items-center gap-1">
                            <span class="material-symbols-outlined text-[16px]">check</span> Create Role
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Create Permission Modal -->
        <div id="create-permission-modal" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-xs flex items-center justify-center hidden opacity-0 transition-all duration-300 ease-out">
            <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-lg w-[450px] max-w-full mx-md shadow-2xl transform scale-95 transition-all duration-300 ease-out relative">
                <!-- Close Button -->
                <button type="button" class="js-close-modal absolute top-4 right-4 text-on-surface-variant hover:text-on-surface hover:bg-surface-container-low p-1 rounded-full transition-all cursor-pointer" data-target="create-permission-modal">
                    <span class="material-symbols-outlined text-[20px]">close</span>
                </button>

                <div class="flex items-center gap-sm mb-md">
                    <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center text-primary">
                        <span class="material-symbols-outlined text-[24px]">vpn_key</span>
                    </div>
                    <div>
                        <h3 class="font-title-md text-title-md text-on-surface">Create Permission Group</h3>
                        <p class="font-body-sm text-body-sm text-on-surface-variant">Define a new system capability</p>
                    </div>
                </div>

                <p class="font-body-sm text-body-sm text-on-surface-variant mb-md leading-relaxed bg-surface p-sm rounded-lg border border-outline-variant">
                    <strong>Note:</strong> This will automatically generate all 4 standard CRUD permissions (view, create, update, delete) for the specified resource.
                </p>
                
                <form action="{{ route('role-permissions.permissions.store') }}" method="POST" class="flex flex-col gap-md">
                    @csrf
                    <div class="flex flex-col gap-sm">
                        <label class="font-label-sm text-label-sm text-on-surface" for="permission-name-input">
                            Resource Name
                        </label>
                        <input 
                            type="text"
                            id="permission-name-input"
                            name="resource_name"
                            placeholder="e.g. Order" 
                            required 
                            class="w-full bg-surface border border-outline-variant rounded-lg py-2.5 px-3.5 font-body-sm text-body-sm text-on-surface placeholder:text-on-surface-variant focus:outline-none focus:ring-2 focus:ring-primary-fixed focus:border-primary transition-all"
                        />
                    </div>
                    <div class="flex justify-end gap-sm mt-lg">
                        <button type="button" class="js-close-modal px-md py-sm rounded-lg border border-outline-variant bg-surface text-on-surface hover:bg-surface-container-low transition-colors text-body-sm font-medium cursor-pointer" data-target="create-permission-modal">Cancel</button>
                        <button type="submit" class="px-md py-2.5 rounded-lg bg-primary text-on-primary hover:bg-surface-tint transition-colors text-body-sm font-medium shadow-sm cursor-pointer flex items-center gap-1">
                            <span class="material-symbols-outlined text-[16px]">check</span> Create Group
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Role Selection Redirection
    const roleSelector = document.getElementById('role-selector');
    if (roleSelector) {
        roleSelector.addEventListener('change', function() {
            window.location.href = "{{ route('role-permissions.index') }}?role_id=" + this.value;
        });
    }

    // 2. Section "Select All" Toggle functionality
    document.querySelectorAll('.section-container').forEach(container => {
        const toggle = container.querySelector('.js-section-toggle');
        const checkboxes = container.querySelectorAll('input[type="checkbox"].custom-checkbox');
        if (!toggle || checkboxes.length === 0) return;
        
        // Update section toggle status based on checkboxes state
        const updateSectionToggle = () => {
            const checkedCount = Array.from(checkboxes).filter(cb => cb.checked).length;
            toggle.checked = checkedCount === checkboxes.length;
        };

        // When "Select All" is clicked
        toggle.addEventListener('change', function() {
            checkboxes.forEach(cb => {
                cb.checked = this.checked;
            });
        });

        // When any checkbox inside the section is clicked
        checkboxes.forEach(cb => {
            cb.addEventListener('change', updateSectionToggle);
        });

        // Run initial status sync
        updateSectionToggle();
    });

    // 3. Modals Opening / Closing with Transitions
    function openModal(id) {
        const modal = document.getElementById(id);
        if (modal) {
            modal.classList.remove('hidden');
            setTimeout(() => {
                modal.classList.remove('opacity-0');
                const box = modal.querySelector('.transform');
                if (box) {
                    box.classList.remove('scale-95');
                    box.classList.add('scale-100');
                }
            }, 10);
        }
    }

    function closeModal(id) {
        const modal = document.getElementById(id);
        if (modal) {
            modal.classList.add('opacity-0');
            const box = modal.querySelector('.transform');
            if (box) {
                box.classList.remove('scale-100');
                box.classList.add('scale-95');
            }
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300);
        }
    }

    document.querySelectorAll('.js-open-modal').forEach(btn => {
        btn.addEventListener('click', function() {
            const targetId = this.getAttribute('data-target');
            openModal(targetId);
        });
    });

    document.querySelectorAll('.js-close-modal').forEach(btn => {
        btn.addEventListener('click', function() {
            const targetId = this.getAttribute('data-target');
            closeModal(targetId);
        });
    });

    // Close modal on clicking outside the content area
    window.addEventListener('click', function(e) {
        document.querySelectorAll('#create-role-modal, #create-permission-modal').forEach(modal => {
            if (e.target === modal) {
                closeModal(modal.id);
            }
        });
    });

    // 4. Auto-remove alert notifications after 3 seconds
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
