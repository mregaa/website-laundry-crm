{{-- Bottom Navigation for Mobile --}}
<nav class="md:hidden fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 z-50 safe-bottom">
    <div class="grid grid-cols-5 h-16">
        {{-- Dashboard --}}
        <a href="{{ route('dashboard') }}" 
           class="flex flex-col items-center justify-center {{ request()->routeIs('dashboard') ? 'text-blue-600' : 'text-gray-600' }} hover:text-blue-600 transition-colors">
            <i class="fas fa-home text-xl mb-1"></i>
            <span class="text-xs font-medium">Dashboard</span>
        </a>
        
        {{-- Order Baru --}}
        <a href="{{ route('orders.index') }}" 
           class="flex flex-col items-center justify-center {{ request()->routeIs('orders.index') && !request('status') ? 'text-blue-600' : 'text-gray-600' }} hover:text-blue-600 transition-colors">
            <div class="relative">
                {{-- <div class="absolute -inset-2 bg-blue-500 rounded-full opacity-20"></div> --}}
                <i class="fas fa-box text-2xl relative"></i>
            </div>
            <span class="text-xs font-medium mt-1">Orderan</span>
        </a>
        
        {{-- Proses --}}
        <a href="{{ route('orders.index', ['status' => 'in_progress']) }}" 
           class="flex flex-col items-center justify-center {{ request()->routeIs('orders.index') && request('status') === 'in_progress' ? 'text-blue-600' : 'text-gray-600' }} hover:text-blue-600 transition-colors">
            <i class="fas fa-sync-alt text-xl mb-1"></i>
            <span class="text-xs font-medium">Proses</span>
        </a>
        
        {{-- Siap Ambil --}}
        <a href="{{ route('orders.index', ['status' => 'ready']) }}" 
           class="flex flex-col items-center justify-center {{ request()->routeIs('orders.index') && request('status') === 'ready' ? 'text-blue-600' : 'text-gray-600' }} hover:text-blue-600 transition-colors">
            <i class="fas fa-box-open text-xl mb-1"></i>
            <span class="text-xs font-medium">Siap Ambil</span>
        </a>
        
        {{-- Pelanggan --}}
        <a href="{{ route('customers.index') }}" 
           class="flex flex-col items-center justify-center {{ request()->routeIs('customers.*') ? 'text-blue-600' : 'text-gray-600' }} hover:text-blue-600 transition-colors">
            <i class="fas fa-users text-xl mb-1"></i>
            <span class="text-xs font-medium">Pelanggan</span>
        </a>
    </div>
</nav>

{{-- Add padding to prevent content from hiding behind bottom nav on mobile --}}
<div class="md:hidden h-16"></div>
