<!-- Mobile overlay -->
<div 
    x-show="sidebarOpen" 
    x-transition.opacity
    @click="sidebarOpen = false"
    class="fixed inset-0 z-20 bg-black/50 lg:hidden"
    style="display: none;"
></div>

<!-- Sidebar -->
<aside 
    :class="{
        'translate-x-0': sidebarOpen,
        '-translate-x-full': !sidebarOpen,
        'w-64': !sidebarMini,
        'w-20': sidebarMini
    }"
    class="fixed inset-y-0 left-0 z-30 flex flex-col bg-gray-50 dark:bg-slate-900 border-r border-gray-200 dark:border-slate-800 transition-all duration-300 transform lg:static lg:translate-x-0"
>
    <!-- Logo/Brand -->
    <div class="h-16 flex items-center justify-center px-4 border-b border-gray-200 dark:border-slate-800 overflow-hidden">
        <span x-show="!sidebarMini" class="text-xl font-bold text-primary truncate transition-opacity duration-300">Task Manager</span>
        <span x-show="sidebarMini" style="display: none;" class="text-xl font-bold text-primary">TM</span>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 px-4 py-4 space-y-2 overflow-y-auto overflow-x-hidden">
        <!-- Dashboard Link -->
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('dashboard') ? 'text-primary bg-primary/10' : 'text-gray-600 dark:text-slate-400 hover:text-primary dark:hover:text-primary hover:bg-gray-100 dark:hover:bg-slate-800' }} transition-colors group" title="Dashboard">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
            </svg>
            <span x-show="!sidebarMini" class="transition-opacity duration-300">Dashboard</span>
        </a>

        <!-- Tugas Link -->
        <a href="{{ route('tugas') }}" class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('tugas') ? 'text-primary bg-primary/10' : 'text-gray-600 dark:text-slate-400 hover:text-primary dark:hover:text-primary hover:bg-gray-100 dark:hover:bg-slate-800' }} transition-colors group" title="Tugas">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
            </svg>
            <span x-show="!sidebarMini" class="transition-opacity duration-300">Tugas</span>
        </a>

        <!-- Activity History Link -->
        <a href="{{ route('activity.history') }}" class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('activity.history') ? 'text-primary bg-primary/10' : 'text-gray-600 dark:text-slate-400 hover:text-primary dark:hover:text-primary hover:bg-gray-100 dark:hover:bg-slate-800' }} transition-colors group" title="Activity History">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span x-show="!sidebarMini" class="transition-opacity duration-300">Activity History</span>
        </a>


    </nav>
    
    <!-- Footer Profil Preview -->
    <div class="p-4 border-t border-gray-200 dark:border-slate-800">
        @auth
        <div class="relative" x-data="{ dropdownOpen: false }">
            <!-- Clickable Profile -->
            <button 
                @click="dropdownOpen = !dropdownOpen" 
                @click.away="dropdownOpen = false" 
                class="w-full flex items-center justify-center lg:justify-start gap-3 p-2 rounded-lg bg-white dark:bg-slate-800 shadow-sm border border-gray-200 dark:border-slate-700 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors focus:outline-none"
            >
                @if(isset($userContext) && $userContext->avatar_url)
                    <img src="{{ asset($userContext->avatar_url) }}" alt="Profile" class="w-8 h-8 rounded-full flex-shrink-0 object-cover border border-gray-200 dark:border-slate-700">
                @else
                    <div class="w-8 h-8 flex-shrink-0 rounded-full bg-primary/20 flex items-center justify-center text-primary font-bold">
                        {{ strtoupper(substr(auth()->user()->full_name, 0, 1)) }}
                    </div>
                @endif
                <div x-show="!sidebarMini" class="flex-1 min-w-0 text-left transition-opacity duration-300">
                    <p class="text-sm font-medium text-gray-900 dark:text-slate-100 truncate">{{ isset($userContext) ? $userContext->full_name : auth()->user()->full_name }}</p>
                    <p class="text-xs text-gray-500 dark:text-slate-400 truncate">{{ isset($userContext) ? $userContext->email : auth()->user()->email }}</p>
                </div>
                <svg x-show="!sidebarMini" class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                </svg>
            </button>

            <!-- Logout Dropdown -->
            <div 
                x-show="dropdownOpen" 
                x-transition 
                style="display: none;" 
                class="absolute bottom-full left-0 mb-2 bg-white dark:bg-slate-800 rounded-lg shadow-lg border border-gray-100 dark:border-slate-700 overflow-hidden"
                :class="sidebarMini ? 'w-48 ml-4' : 'w-full'"
            >
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-left px-4 py-3 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-slate-700/50 flex items-center gap-2 transition-colors">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                        Logout
                    </button>
                </form>
            </div>
        </div>
        @endauth
    </div>
</aside>
