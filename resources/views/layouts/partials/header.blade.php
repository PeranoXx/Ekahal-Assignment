<!-- TopNavBar -->
<header
    class="docked full-width top-0 sticky z-40 bg-background dark:bg-background border-b border-outline-variant flex justify-between items-center w-full px-xl py-md">
    <!-- Breadcrumb / Section Title -->
    <div>
        <h2 class="font-label-md text-label-md text-on-surface-variant flex items-center gap-2">
            <span>Workspace</span>
            <span class="text-outline text-zinc-600">/</span>
            <span class="text-primary font-medium">User Management</span>
        </h2>
    </div>

    <!-- Actions -->
    <div class="flex items-center gap-md">
        <span class="font-label-md text-label-md text-on-surface-variant">
            {{ Auth::user()->name }}
        </span>

        <!-- Profile Dropdown Container -->
        <div class="relative">
            <div id="profile-menu-button"
                class="w-8 h-8 rounded-full overflow-hidden border border-outline-variant cursor-pointer hover:border-primary transition-colors">
                <img alt="User profile" class="w-full h-full object-cover"
                    src="https://lh3.googleusercontent.com/aida-public/AB6AXuAo6hAfXqtyQdeXlhdQR9y2SSSvwJbdHfn6Xyg4n1II1w_qUY4XAxkG9xyH5uJ2OXHgzxiAAY_pLJxKjUJhZLcE1ZvOjENHdO4wAMydRVbpwjH7utRcv6-YrvDA06geTCoFrJi3TDZMC1aP40EvYJfdc1QOBmoxT9GJVvMhLVwVcc6Sp38ifTlPScHqrG3k7KAkXmVZMItBH3vEnt6ovtC--V9TSdPPFMbz8BI2av6-MecX_xbSLKad" />
            </div>

            <!-- Dropdown Menu -->
            <div id="profile-dropdown"
                class="hidden absolute right-0 mt-2 w-48 bg-surface-container-lowest border border-outline-variant rounded-lg shadow-lg py-1 z-50">
                <form action="{{ route('logout') }}" method="POST" class="m-0 p-0">
                    @csrf
                    <button type="submit"
                        class="w-full flex items-center gap-2 px-4 py-2 text-body-sm font-label-md text-error hover:bg-rose-50 dark:hover:bg-rose-950/10 transition-colors text-left bg-transparent border-0 cursor-pointer">
                        <span class="material-symbols-outlined text-[18px]">logout</span>
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const button = document.getElementById('profile-menu-button');
        const dropdown = document.getElementById('profile-dropdown');

        if (button && dropdown) {
            // Toggle menu on profile click
            button.addEventListener('click', function (e) {
                e.stopPropagation();
                dropdown.classList.toggle('hidden');
            });

            // Close menu when clicking outside
            document.addEventListener('click', function (e) {
                if (!dropdown.contains(e.target) && !button.contains(e.target)) {
                    dropdown.classList.add('hidden');
                }
            });
        }
    });
</script>