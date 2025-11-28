<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Laundry CRM') - LaundryPro</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @stack('styles')
</head>
<body class="min-h-screen flex flex-col bg-gray-100">
    <!-- Navigation -->
    <nav class="bg-blue-600 text-white shadow-lg">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('dashboard') }}" class="text-2xl font-bold">
                        <i class="fas fa-tshirt"></i> LaundryPro
                    </a>
                </div>
                <div class="hidden md:flex space-x-6">
                    <a href="{{ route('dashboard') }}" class="hover:bg-blue-700 px-3 py-2 rounded transition">
                        <i class="fas fa-dashboard"></i> Dashboard
                    </a>
                    <a href="{{ route('customers.index') }}" class="hover:bg-blue-700 px-3 py-2 rounded transition">
                        <i class="fas fa-users"></i> Customers
                    </a>
                    <a href="{{ route('orders.index') }}" class="hover:bg-blue-700 px-3 py-2 rounded transition">
                        <i class="fas fa-shopping-cart"></i> Orders
                    </a>
                    <a href="{{ route('services.index') }}" class="hover:bg-blue-700 px-3 py-2 rounded transition">
                        <i class="fas fa-cogs"></i> Services
                    </a>
                    <a href="{{ route('financial.index') }}" class="hover:bg-blue-700 px-3 py-2 rounded transition">
                        <i class="fas fa-dollar-sign"></i> Financial
                    </a>
                    <a href="{{ route('rewards.index') }}" class="hover:bg-blue-700 px-3 py-2 rounded transition">
                        <i class="fas fa-gift"></i> Rewards
                    </a>
                </div>
                <button id="mobileMenuBtn" class="md:hidden">
                    <i class="fas fa-bars text-2xl"></i>
                </button>
            </div>
            <!-- Mobile Menu -->
            <div id="mobileMenu" class="hidden md:hidden pb-4">
                <a href="{{ route('dashboard') }}" class="block py-2 px-4 hover:bg-blue-700 rounded">
                    <i class="fas fa-dashboard"></i> Dashboard
                </a>
                <a href="{{ route('customers.index') }}" class="block py-2 px-4 hover:bg-blue-700 rounded">
                    <i class="fas fa-users"></i> Customers
                </a>
                <a href="{{ route('orders.index') }}" class="block py-2 px-4 hover:bg-blue-700 rounded">
                    <i class="fas fa-shopping-cart"></i> Orders
                </a>
                <a href="{{ route('services.index') }}" class="block py-2 px-4 hover:bg-blue-700 rounded">
                    <i class="fas fa-cogs"></i> Services
                </a>
                <a href="{{ route('financial.index') }}" class="block py-2 px-4 hover:bg-blue-700 rounded">
                    <i class="fas fa-dollar-sign"></i> Financial
                </a>
                <a href="{{ route('inventory.index') }}" class="block py-2 px-4 hover:bg-blue-700 rounded">
                    <i class="fas fa-boxes"></i> Inventory
                </a>
                <a href="{{ route('rewards.index') }}" class="block py-2 px-4 hover:bg-blue-700 rounded">
                    <i class="fas fa-gift"></i> Rewards
                </a>
            </div>
        </div>
    </nav>

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="container mx-auto px-4 mt-4">
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="container mx-auto px-4 mt-4">
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        </div>
    @endif

    <!-- Main Content -->
    <main class="container mx-auto px-4 py-8 flex-1">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white mt-auto">
        <div class="container mx-auto px-4 py-6 text-center">
            <p>&copy; {{ date('Y') }} LaundryPro CRM System. All rights reserved.</p>
        </div>
    </footer>

    <script>
        // Mobile menu toggle
        document.getElementById('mobileMenuBtn')?.addEventListener('click', function() {
            document.getElementById('mobileMenu').classList.toggle('hidden');
        });

        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            document.querySelectorAll('[role="alert"]').forEach(function(alert) {
                alert.style.display = 'none';
            });
        }, 5000);
    </script>
    @stack('scripts')
</body>
</html>
