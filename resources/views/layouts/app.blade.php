<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Task Manager')</title>
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%232563EB'%3E%3Cpath d='M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4'/%3E%3C/svg%3E">
    
    <!-- Google Fonts: Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
    <script>
        // Prevent FOUC for Dark Mode
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
    <style>
        /* ── GLOBAL FONT: POPPINS ── */
        body { font-family: 'Poppins', ui-sans-serif, system-ui, sans-serif !important; }
        
        /* ── RAW CSS DARK MODE (Since Tailwind isn't compiling locally) ── */
        html.dark body { background-color: #0f172a !important; color: #f8fafc !important; }
        
        html.dark header, 
        html.dark aside, 
        html.dark .bg-white { background-color: #1e293b !important; }
        
        html.dark .bg-gray-50, 
        html.dark .bg-gray-100 { background-color: #0f172a !important; }
        
        html.dark .border-gray-100, 
        html.dark .border-gray-200 { border-color: #334155 !important; }
        html.dark .border-b, html.dark .border-r, html.dark .border-t { border-color: #334155 !important; }
        
        html.dark .text-gray-900, html.dark .text-gray-800, html.dark .text-slate-800, html.dark .text-slate-900 { color: #f8fafc !important; }
        html.dark .text-gray-700, html.dark .text-slate-700 { color: #e2e8f0 !important; }
        html.dark .text-gray-600, html.dark .text-slate-600 { color: #cbd5e1 !important; }
        html.dark .text-gray-500, html.dark .text-gray-400, html.dark .text-slate-500, html.dark .text-slate-400 { color: #94a3b8 !important; }
        
        html.dark .hover\:bg-gray-50:hover, 
        html.dark .hover\:bg-gray-100:hover { background-color: #334155 !important; }
        
        html.dark .kanban-card { border: 1px solid #334155 !important; background-color: #1e293b !important; }
        html.dark .kanban-card:hover { border-color: #475569 !important; }
        html.dark .kanban-col { background-color: #0f172a !important; border-color: #334155 !important; }
        html.dark .kanban-col-header { background-color: #1e293b !important; border-bottom-color: #334155 !important; }
        
        html.dark .empty-card { background-color: transparent !important; border-color: #475569 !important; color: #f8fafc !important; }
        
        /* Navbar Dark Mode Icons */
        html.dark .dark-icon-sun { display: block !important; }
        html:not(.dark) .dark-icon-sun { display: none !important; }
        html.dark .dark-icon-moon { display: none !important; }
        html:not(.dark) .dark-icon-moon { display: block !important; }
    </style>
</head>
<body class="bg-gray-100 text-gray-900 dark:bg-slate-900 dark:text-slate-200 antialiased font-sans transition-colors duration-200" x-data="{ sidebarOpen: false, sidebarMini: false }">

    <div class="flex h-screen overflow-hidden relative">
        
        <!-- Sidebar (Desktop & Mobile) -->
        @include('partials.sidebar')

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
            
            <!-- Navbar -->
            @include('partials.navbar')

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto p-4 md:p-6">
                @yield('content')
            </main>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
