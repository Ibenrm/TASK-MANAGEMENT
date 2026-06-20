<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Task Manager')</title>
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%232563EB'%3E%3Cpath d='M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4'/%3E%3C/svg%3E">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 text-gray-900 antialiased font-sans" x-data="{ sidebarOpen: false, sidebarMini: false }">

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

</body>
</html>
