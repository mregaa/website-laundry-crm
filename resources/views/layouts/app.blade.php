<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Laundry CRM') - LaundryPro</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Reset all default margins and paddings */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        html, body {
            height: 100%;
            overflow: hidden;
        }
        
        /* Custom scrollbar for sidebar */
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 3px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
        
        /* Smooth transitions */
        * {
            -webkit-tap-highlight-color: transparent;
        }
    </style>
    @stack('styles')
</head>
<body class="h-full bg-gray-50 font-sans antialiased overflow-hidden">
    <div class="h-full flex overflow-hidden">
        <!-- Desktop Sidebar -->
        <aside id="sidebar" class="hidden md:flex md:flex-col w-64 bg-white border-r border-gray-200 transition-all duration-300">
            <!-- Logo -->
            <div class="flex items-center justify-between h-16 px-6 border-b border-gray-200">
                <a href="{{ route('dashboard') }}" class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-blue-400 to-blue-600 rounded-lg flex items-center justify-center">
                        <i class="fas fa-tshirt text-white text-lg"></i>
                    </div>
                    <span class="text-xl font-bold text-gray-800" id="logoText">LaundryPro</span>
                </a>
                <button id="toggleSidebar" class="text-gray-500 hover:text-gray-700 transition">
                    <i class="fas fa-bars"></i>
                </button>
            </div>

            <!-- Navigation Menu -->
            <nav class="flex-1 overflow-y-auto custom-scrollbar py-4 px-3">
                <div class="space-y-1">
                    {{-- Dashboard --}}
                    <a href="{{ route('dashboard') }}" 
                       class="flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('dashboard') ? 'bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-50' }}">
                        <i class="fas fa-home w-5 text-center"></i>
                        <span class="sidebar-text font-medium">Dashboard</span>
                    </a>

                    {{-- Orders --}}
                    <a href="{{ route('orders.index') }}" 
                       class="flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('orders.*') ? 'bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-50' }}">
                        <i class="fas fa-box w-5 text-center"></i>
                        <span class="sidebar-text font-medium">Order</span>
                    </a>

                    {{-- Customers --}}
                    <a href="{{ route('customers.index') }}" 
                       class="flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('customers.*') ? 'bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-50' }}">
                        <i class="fas fa-users w-5 text-center"></i>
                        <span class="sidebar-text font-medium">Pelanggan</span>
                    </a>

                    {{-- Services --}}
                    <a href="{{ route('services.index') }}" 
                       class="flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('services.*') ? 'bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-50' }}">
                        <i class="fas fa-cogs w-5 text-center"></i>
                        <span class="sidebar-text font-medium">Layanan</span>
                    </a>

                    {{-- Financial --}}
                    <a href="{{ route('financial.index') }}" 
                       class="flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('financial.*') ? 'bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-50' }}">
                        <i class="fas fa-wallet w-5 text-center"></i>
                        <span class="sidebar-text font-medium">Keuangan</span>
                    </a>

                    {{-- Inventory --}}
                    {{-- <a href="{{ route('inventory.index') }}" 
                       class="flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('inventory.*') ? 'bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-50' }}">
                        <i class="fas fa-boxes w-5 text-center"></i>
                        <span class="sidebar-text font-medium">Inventaris</span>
                    </a> --}}

                    {{-- Rewards --}}
                    <a href="{{ route('rewards.index') }}" 
                       class="flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('rewards.*') ? 'bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-50' }}">
                        <i class="fas fa-gift w-5 text-center"></i>
                        <span class="sidebar-text font-medium">Reward</span>
                    </a>
                </div>
            </nav>

            <!-- User Info -->
            <div class="border-t border-gray-200 p-4">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center">
                        <i class="fas fa-user text-gray-600"></i>
                    </div>
                    <div class="flex-1 sidebar-text">
                        <p class="text-sm font-medium text-gray-800">Admin</p>
                        <p class="text-xs text-gray-500">LaundryPro</p>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Mobile Header -->
            <header class="md:hidden bg-white border-b border-gray-200 px-4 py-3">
                <div class="flex items-center justify-between">
                    <a href="{{ route('dashboard') }}" class="flex items-center space-x-2">
                        <div class="w-8 h-8 bg-gradient-to-br from-blue-400 to-blue-600 rounded-lg flex items-center justify-center">
                            <i class="fas fa-tshirt text-white text-sm"></i>
                        </div>
                        <span class="text-lg font-bold text-gray-800">LaundryPro</span>
                    </a>
                    <div class="text-sm text-gray-600">
                        <i class="fas fa-user mr-1"></i> Admin
                    </div>
                </div>
            </header>

            <!-- Flash Messages -->
            @if(session('success'))
                <div class="mx-4 mt-4">
                    <div class="bg-green-50 border-l-4 border-green-500 text-green-700 px-4 py-3 rounded-lg shadow-sm" role="alert">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle mr-3"></i>
                            <span>{{ session('success') }}</span>
                        </div>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="mx-4 mt-4">
                    <div class="bg-red-50 border-l-4 border-red-500 text-red-700 px-4 py-3 rounded-lg shadow-sm" role="alert">
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-circle mr-3"></i>
                            <span>{{ session('error') }}</span>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto bg-gray-50 p-4 md:p-6 mb-16 md:mb-0">
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Bottom Navigation (Mobile Only) -->
    <x-bottom-nav />

    <script>
        // Sidebar toggle for desktop
        const sidebar = document.getElementById('sidebar');
        const toggleBtn = document.getElementById('toggleSidebar');
        const sidebarTexts = document.querySelectorAll('.sidebar-text');
        const logoText = document.getElementById('logoText');
        
        let isCollapsed = false;
        
        toggleBtn?.addEventListener('click', function() {
            isCollapsed = !isCollapsed;
            
            if (isCollapsed) {
                sidebar.classList.remove('w-64');
                sidebar.classList.add('w-20');
                sidebarTexts.forEach(text => text.classList.add('hidden'));
                logoText.classList.add('hidden');
            } else {
                sidebar.classList.remove('w-20');
                sidebar.classList.add('w-64');
                sidebarTexts.forEach(text => text.classList.remove('hidden'));
                logoText.classList.remove('hidden');
            }
        });

        // Auto-hide flash messages
        setTimeout(function() {
            document.querySelectorAll('[role="alert"]').forEach(function(alert) {
                alert.style.transition = 'opacity 0.5s';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            });
        }, 5000);
    </script>
    @stack('scripts')
</body>
</html>
