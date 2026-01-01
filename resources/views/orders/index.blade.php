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
        <form method="GET" action="{{ route('orders.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Cari</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Nomor pesanan, pelanggan..." 
                       class="w-full px-4 py-2 border rounded-lg">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select name="status" class="w-full px-4 py-2 border rounded-lg">
                    <option value="">Semua Status</option>
                    <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>Diproses</option>
                    <option value="ready" {{ request('status') == 'ready' ? 'selected' : '' }}>Siap Diambil</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Selesai</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status Pembayaran</label>
                <select name="payment_status" class="w-full px-4 py-2 border rounded-lg">
                    <option value="">Semua Status Pembayaran</option>
                    <option value="pending" {{ request('payment_status') == 'pending' ? 'selected' : '' }}>Tertunda</option>
                    <option value="partial" {{ request('payment_status') == 'partial' ? 'selected' : '' }}>Sebagian</option>
                    <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Lunas</option>
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg flex-1">
                    <i class="fas fa-search mr-2"></i>Filter
                </button>
                <a href="{{ route('orders.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg flex-1 text-center">
                    <i class="fas fa-times mr-2"></i>Bersihkan
                </a>
            </div>
        </form>
    </div>

    <!-- Mobile Cards (< md) -->
    <div class="md:hidden space-y-4">
        @forelse($orders as $order)
        <div class="bg-white rounded-lg shadow-md p-4 border-l-4 
            @if($order->status == 'completed') border-green-500
            @elseif($order->status == 'ready') border-blue-500
            @elseif($order->status == 'in_progress') border-yellow-500
            @else border-gray-400
            @endif">
            
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
        </div>
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
@endsection
