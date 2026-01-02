@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Pesanan</h1>
        <a href="{{ route('orders.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg">
            <i class="fas fa-plus mr-2"></i>Pesanan Baru
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-md p-4 mb-6">
        <!-- Mobile Filter Form -->
        <form method="GET" action="{{ route('orders.index') }}" class="md:hidden">
            <!-- Search Bar with Buttons -->
            <div class="flex gap-2 mb-3">
                <input type="text" 
                       name="search" 
                       value="{{ request('search') }}" 
                       placeholder="Cari pesanan..." 
                       class="flex-1 min-w-0 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                
                <button type="button" 
                        onclick="toggleMobileFilters(event)"
                        class="flex-shrink-0 bg-gray-600 hover:bg-gray-700 text-white w-10 h-10 rounded-lg flex items-center justify-center"
                        aria-label="Toggle filter">
                    <i class="fas fa-filter"></i>
                </button>
                
                <button type="submit" 
                        class="flex-shrink-0 bg-blue-600 hover:bg-blue-700 text-white w-10 h-10 rounded-lg flex items-center justify-center"
                        aria-label="Search">
                    <i class="fas fa-search"></i>
                </button>
            </div>
            
            <!-- Collapsible Filter Options -->
            <div id="mobileFilters" class="hidden space-y-3 pt-3 border-t border-gray-200">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="">Semua Status</option>
                        <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>Diproses</option>
                        <option value="ready" {{ request('status') == 'ready' ? 'selected' : '' }}>Siap Diambil</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Selesai</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status Pembayaran</label>
                    <select name="payment_status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="">Semua Status Pembayaran</option>
                        <option value="pending" {{ request('payment_status') == 'pending' ? 'selected' : '' }}>Tertunda</option>
                        <option value="partial" {{ request('payment_status') == 'partial' ? 'selected' : '' }}>Sebagian</option>
                        <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Lunas</option>
                        <option value="refunded" {{ request('payment_status') == 'refunded' ? 'selected' : '' }}>Dikembalikan</option>
                    </select>
                </div>
                
                <div class="flex gap-2 pt-2">
                    <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium">
                        <i class="fas fa-check mr-2"></i>Terapkan
                    </button>
                    <a href="{{ route('orders.index') }}" class="flex-1 bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg text-center font-medium">
                        <i class="fas fa-times mr-2"></i>Bersihkan
                    </a>
                </div>
            </div>
        </form>
        
        <!-- Desktop Filter Form -->
        <form method="GET" action="{{ route('orders.index') }}" class="hidden md:grid md:grid-cols-4 gap-3">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Cari</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Nomor pesanan..." 
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select name="status" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">Semua Status</option>
                    <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>Diproses</option>
                    <option value="ready" {{ request('status') == 'ready' ? 'selected' : '' }}>Siap Diambil</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Selesai</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status Pembayaran</label>
                <select name="payment_status" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">Semua</option>
                    <option value="pending" {{ request('payment_status') == 'pending' ? 'selected' : '' }}>Belum Dibayar</option>
                    <option value="partial" {{ request('payment_status') == 'partial' ? 'selected' : '' }}>Sebagian</option>
                    <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Lunas</option>
                    <option value="refunded" {{ request('payment_status') == 'refunded' ? 'selected' : '' }}>Dikembalikan</option>
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded-lg transition text-sm whitespace-nowrap">
                    <i class="fas fa-search"></i>
                </button>
                <a href="{{ route('orders.index') }}" class="flex-1 bg-gray-500 hover:bg-gray-600 text-white px-3 py-2 rounded-lg transition text-center text-sm whitespace-nowrap">
                    <i class="fas fa-times"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Mobile Cards (< md) -->
    <div class="md:hidden space-y-4">
        @forelse($orders as $order)
        <!-- Swipeable Container -->
        <div class="relative overflow-hidden rounded-lg shadow-md" data-swipe-container>
            <!-- Action Tray (Hidden by default, revealed on swipe left) -->
            <div class="absolute right-0 top-0 h-full flex items-center gap-2 px-4 bg-gradient-to-l from-blue-600 to-blue-500 translate-x-full transition-transform duration-300" data-action-tray>
                <!-- Update Status Button -->
                <button 
                    type="button"
                    onclick="openStatusModal({{ $order->id }}, '{{ $order->status }}')"
                    class="bg-white text-blue-600 px-4 py-3 rounded-lg font-medium whitespace-nowrap shadow-lg hover:bg-blue-50 transition"
                    aria-label="Ubah status pesanan {{ $order->order_number }}">
                    <i class="fas fa-edit mr-1 max-w-[16px]"></i>Ubah Status
                </button>
            </div>
            
            <!-- Card Content (Swipeable) -->
            <div class="bg-white rounded-lg p-4 border-l-4 transition-transform duration-300
                @if($order->status == 'completed') border-green-500
                @elseif($order->status == 'ready') border-blue-500
                @elseif($order->status == 'in_progress') border-yellow-500
                @else border-gray-400
                @endif" data-card-content>
            
            <!-- Order Number & Date -->
            <div class="flex justify-between items-start mb-3">
                <div>
                    <a href="{{ route('orders.show', $order) }}" class="text-lg font-bold text-blue-600 hover:text-blue-800">
                        {{ $order->order_number }}
                    </a>
                    <p class="text-xs text-gray-500 mt-1">
                        <i class="far fa-calendar mr-1"></i>
                        {{ $order->order_date ? $order->order_date->format('d M Y') : $order->created_at->format('d M Y') }}
                    </p>
                </div>
                <span class="px-3 py-1 text-xs font-semibold rounded-full 
                    @if($order->status == 'completed') bg-green-100 text-green-800
                    @elseif($order->status == 'ready') bg-blue-100 text-blue-800
                    @elseif($order->status == 'in_progress') bg-yellow-100 text-yellow-800
                    @else bg-gray-100 text-gray-800
                    @endif">
                    {{ statusLabel($order->status, 'order') }}
                </span>
            </div>

            <!-- Customer Info -->
            <div class="mb-3 pb-3 border-b border-gray-200">
                <p class="font-semibold text-gray-900">{{ $order->customer->name }}</p>
                <p class="text-sm text-gray-600">
                    <i class="fas fa-phone mr-1"></i>
                    {{ $order->customer->phone }}
                </p>
            </div>

            <!-- Payment & Total -->
            <div class="flex justify-between items-center mb-4">
                <div>
                    <p class="text-xs text-gray-500 mb-1">Status Pembayaran</p>
                    <span class="px-2 py-1 text-xs font-semibold rounded-full
                        @if($order->payment_status == 'paid') bg-green-100 text-green-800
                        @elseif($order->payment_status == 'partial') bg-yellow-100 text-yellow-800
                        @elseif($order->payment_status == 'refunded') bg-purple-100 text-purple-800
                        @else bg-red-100 text-red-800
                        @endif">
                        {{ statusLabel($order->payment_status, 'payment') }}
                    </span>
                </div>
                <div class="text-right">
                    <p class="text-xs text-gray-500 mb-1">Total</p>
                    <p class="text-lg font-bold text-gray-900">{{ rupiah($order->total) }}</p>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex gap-2">
                <a href="{{ route('orders.show', $order) }}" 
                   class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-center py-3 rounded-lg font-medium transition">
                    <i class="fas fa-eye mr-2"></i>Lihat Detail
                </a>
                @if ($order->status === 'ready' && $order->whatsappUrl())
                    <a href="{{ $order->whatsappUrl() }}"
                    target="_blank" rel="noopener noreferrer"
                    class="flex-1 bg-green-600 hover:bg-green-700 text-white text-center py-3 rounded-lg font-medium transition">
                        <i class="fab fa-whatsapp text-lg mr-2"></i> WhatsApp
                    </a>
                @else
                    <a href="{{ route('orders.edit', $order) }}"
                    class="flex-1 bg-green-600 hover:bg-green-700 text-white text-center py-3 rounded-lg font-medium transition">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                @endif
            </div>
            </div><!-- End Card Content -->
        </div><!-- End Swipeable Container -->
        @empty
        <div class="bg-white rounded-lg shadow-md p-12 text-center text-gray-500">
            <i class="fas fa-inbox text-5xl mb-4 text-gray-400"></i>
            <p class="text-lg font-medium">Tidak ada pesanan ditemukan</p>
        </div>
        @endforelse
    </div>

    <!-- Desktop Table (>= md) -->
    <div class="hidden md:block bg-white rounded-lg shadow-md overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50 sticky top-0">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No. Pesanan</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pelanggan</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pembayaran</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($orders as $order)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <a href="{{ route('orders.show', $order) }}" class="text-blue-600 hover:underline font-medium">
                            {{ $order->order_number }}
                        </a>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">{{ $order->customer->name }}</div>
                        <div class="text-sm text-gray-500">{{ $order->customer->phone }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $order->order_date ? $order->order_date->format('d M Y') : $order->created_at->format('d M Y') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                            @if($order->status == 'completed') bg-green-100 text-green-800
                            @elseif($order->status == 'ready') bg-blue-100 text-blue-800
                            @elseif($order->status == 'in_progress') bg-yellow-100 text-yellow-800
                            @else bg-gray-100 text-gray-800
                            @endif">
                            {{ statusLabel($order->status, 'order') }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                            @if($order->payment_status == 'paid') bg-green-100 text-green-800
                            @elseif($order->payment_status == 'partial') bg-yellow-100 text-yellow-800
                            @elseif($order->payment_status == 'refunded') bg-purple-100 text-purple-800
                            @else bg-red-100 text-red-800
                            @endif">
                            {{ statusLabel($order->payment_status, 'payment') }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        {{ rupiah($order->total) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <a href="{{ route('orders.show', $order) }}" class="text-blue-600 hover:text-blue-900 mr-3">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('orders.edit', $order) }}" class="text-green-600 hover:text-green-900 mr-3">
                            <i class="fas fa-edit"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                        <i class="fas fa-inbox text-4xl mb-3"></i>
                        <p class="text-lg">Tidak ada pesanan ditemukan</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $orders->links() }}
    </div>
</div>

<!-- Status Update Modal (Mobile) -->
<div id="statusModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center px-4">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold text-gray-900">Ubah Status Pesanan</h3>
            <button onclick="closeStatusModal()" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        
        <form id="statusForm" method="POST" action="">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Status Baru</label>
                <select name="status" id="statusSelect" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="in_progress">Diproses</option>
                    <option value="ready">Siap Diambil</option>
                    <option value="completed">Selesai</option>
                    <option value="cancelled">Dibatalkan</option>
                </select>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Catatan (Opsional)</label>
                <textarea name="notes" rows="3" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Tambahkan catatan perubahan status..."></textarea>
            </div>
            
            <div class="flex gap-3">
                <button type="button" onclick="closeStatusModal()" class="flex-1 bg-gray-500 hover:bg-gray-600 text-white py-3 rounded-lg font-medium transition">
                    Batal
                </button>
                <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-lg font-medium transition">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Swipe Gesture Handler
(function() {
    // Only initialize on mobile (< 768px)
    if (window.innerWidth >= 768) return;
    
    const containers = document.querySelectorAll('[data-swipe-container]');
    let activeContainer = null;
    const SWIPE_THRESHOLD = 50; // minimum distance to trigger swipe
    const ACTION_WIDTH = 160; // width to slide for revealing actions
    
    containers.forEach(container => {
        const content = container.querySelector('[data-card-content]');
        const actionTray = container.querySelector('[data-action-tray]');
        
        let touchStartX = 0;
        let touchStartY = 0;
        let currentTranslateX = 0;
        let isDragging = false;
        
        // Touch start
        content.addEventListener('touchstart', (e) => {
            touchStartX = e.touches[0].clientX;
            touchStartY = e.touches[0].clientY;
            isDragging = false;
            content.style.transition = 'none';
        }, { passive: true });
        
        // Touch move
        content.addEventListener('touchmove', (e) => {
            const touchX = e.touches[0].clientX;
            const touchY = e.touches[0].clientY;
            const diffX = touchX - touchStartX;
            const diffY = touchY - touchStartY;
            
            // Determine if horizontal swipe (prevent vertical scroll interference)
            if (Math.abs(diffX) > Math.abs(diffY) && Math.abs(diffX) > 10) {
                isDragging = true;
                e.preventDefault();
                
                // Only allow left swipe (negative diffX) or closing (if already open)
                if (diffX < 0 || currentTranslateX < 0) {
                    const newTranslate = Math.max(-ACTION_WIDTH, Math.min(0, diffX));
                    content.style.transform = `translateX(${newTranslate}px)`;
                }
            }
        }, { passive: false });
        
        // Touch end
        content.addEventListener('touchend', (e) => {
            if (!isDragging) return;
            
            const touchX = e.changedTouches[0].clientX;
            const diffX = touchX - touchStartX;
            
            content.style.transition = 'transform 0.3s ease';
            actionTray.style.transition = 'transform 0.3s ease';
            
            // Determine final state
            if (diffX < -SWIPE_THRESHOLD) {
                // Swipe left - open actions
                openActions(container);
            } else if (diffX > SWIPE_THRESHOLD && currentTranslateX < 0) {
                // Swipe right - close actions
                closeActions(container);
            } else if (currentTranslateX < 0) {
                // Snap back to open if already open
                openActions(container);
            } else {
                // Snap back to closed
                closeActions(container);
            }
        }, { passive: true });
        
        function openActions(cont) {
            // Close any other open container
            if (activeContainer && activeContainer !== cont) {
                closeActions(activeContainer);
            }
            
            const cardContent = cont.querySelector('[data-card-content]');
            const tray = cont.querySelector('[data-action-tray]');
            
            cardContent.style.transform = `translateX(-${ACTION_WIDTH}px)`;
            tray.style.transform = 'translateX(0)';
            currentTranslateX = -ACTION_WIDTH;
            activeContainer = cont;
        }
        
        function closeActions(cont) {
            const cardContent = cont.querySelector('[data-card-content]');
            const tray = cont.querySelector('[data-action-tray]');
            
            cardContent.style.transform = 'translateX(0)';
            tray.style.transform = 'translateX(100%)';
            currentTranslateX = 0;
            
            if (activeContainer === cont) {
                activeContainer = null;
            }
        }
    });
    
    // Close on tap outside
    document.addEventListener('touchstart', (e) => {
        if (activeContainer && !e.target.closest('[data-swipe-container]')) {
            const cardContent = activeContainer.querySelector('[data-card-content]');
            const tray = activeContainer.querySelector('[data-action-tray]');
            
            cardContent.style.transform = 'translateX(0)';
            tray.style.transform = 'translateX(100%)';
            activeContainer = null;
        }
    }, { passive: true });
})();

// Status Modal Functions
function openStatusModal(orderId, currentStatus) {
    const modal = document.getElementById('statusModal');
    const form = document.getElementById('statusForm');
    const select = document.getElementById('statusSelect');
    
    // Set form action
    form.action = `/orders/${orderId}/status`;
    
    // Set current status
    select.value = currentStatus;
    
    // Show modal
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    document.body.style.overflow = 'hidden';
}

function closeStatusModal() {
    const modal = document.getElementById('statusModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    document.body.style.overflow = '';
    
    // Reset form
    document.getElementById('statusForm').reset();
}

// Close modal on backdrop click
document.getElementById('statusModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeStatusModal();
    }
});

// Close modal on escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeStatusModal();
    }
});

// Mobile Filter Toggle
function toggleMobileFilters(event) {
    const filterDiv = document.getElementById('mobileFilters');
    const filterButton = event ? event.target.closest('button') : null;
    
    if (filterDiv && filterDiv.classList.contains('hidden')) {
        filterDiv.classList.remove('hidden');
        if (filterButton) {
            filterButton.innerHTML = '<i class="fas fa-times"></i>';
        }
    } else if (filterDiv) {
        filterDiv.classList.add('hidden');
        if (filterButton) {
            filterButton.innerHTML = '<i class="fas fa-filter"></i>';
        }
    }
}
</script>
@endsection
