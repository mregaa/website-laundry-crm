@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Dashboard</h1>
            <p class="text-sm text-gray-600 mt-1">
                <i class="fas fa-calendar-day"></i> {{ now()->locale('id')->translatedFormat('l, j F Y') }}
            </p>
        </div>
        <x-button href="{{ route('orders.create') }}" icon="fas fa-plus" size="lg">
            Order Baru
        </x-button>
    </div>

    <!-- Quick Stats - Today -->
    <div>
        <h2 class="text-lg font-semibold text-gray-700 mb-3">Statistik Hari Ini</h2>
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4">
            <x-card 
                title="Deadline Hari Ini"
                :value="$todayStats['deadline_today']"
                icon="fas fa-calendar-alt"
                iconBg="bg-red-100"
                iconColor="text-red-600"
                href="{{ route('orders.index') }}"
            />

            <x-card 
                title="Sedang Diproses"
                :value="$ordersByStatus->get('in_progress', 0)"
                icon="fas fa-sync-alt"
                iconBg="bg-yellow-100"
                iconColor="text-yellow-600"
                href="{{ route('orders.index', ['status' => 'in_progress']) }}"
            />

            <x-card 
                title="Siap Diambil"
                :value="$ordersByStatus->get('ready', 0)"
                icon="fas fa-box-open"
                iconBg="bg-blue-100"
                iconColor="text-blue-600"
                href="{{ route('orders.index', ['status' => 'ready']) }}"
            />

            <x-card 
                title="Pendapatan"
                :value="rupiah($todayStats['revenue'])"
                icon="fas fa-rupiah-sign"
                iconBg="bg-green-100"
                iconColor="text-green-600"
                subtitle="Hari ini"
            />
        </div>
    </div>

    <!-- Monthly Stats -->
    <div>
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 mb-3">
            <h2 class="text-lg font-semibold text-gray-700">Statistik Periode</h2>
            
            <!-- Date Range Filter -->
            <form method="GET" action="{{ route('dashboard') }}" class="flex flex-wrap items-center gap-2">
                <div class="flex items-center gap-2">
                    <input type="date" 
                           name="start_date" 
                           value="{{ $startDate }}" 
                           max="{{ date('Y-m-d') }}"
                           class="px-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    
                    <span class="text-gray-500 text-sm">s/d</span>
                    
                    <input type="date" 
                           name="end_date" 
                           value="{{ $endDate }}" 
                           max="{{ date('Y-m-d') }}"
                           class="px-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                
                <div class="flex gap-2">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-1.5 rounded-lg text-sm font-medium transition whitespace-nowrap">
                        <i class="fas fa-filter mr-1"></i>Terapkan
                    </button>
                    <a href="{{ route('dashboard') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-1.5 rounded-lg text-sm font-medium transition whitespace-nowrap">
                        <i class="fas fa-redo mr-1"></i>Reset
                    </a>
                </div>
            </form>
        </div>
        
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4">
            <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-md p-5 text-white">
                <p class="text-blue-100 text-sm mb-2">Total Order</p>
                <p class="text-3xl font-bold">{{ $monthStats['orders'] }}</p>
                <p class="text-xs text-blue-200 mt-2">{{ \Carbon\Carbon::parse($startDate)->format('d M') }} - {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</p>
            </div>

            <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-md p-5 text-white">
                <p class="text-green-100 text-sm mb-2">Pendapatan</p>
                <p class="text-2xl font-bold">{{ rupiah($monthStats['revenue']) }}</p>
                <p class="text-xs text-green-200 mt-2">Total pemasukan</p>
            </div>

            <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-xl shadow-md p-5 text-white">
                <p class="text-red-100 text-sm mb-2">Pengeluaran</p>
                <p class="text-2xl font-bold">{{ rupiah($monthStats['expenses']) }}</p>
                <p class="text-xs text-red-200 mt-2">Total biaya</p>
            </div>

            <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-md p-5 text-white">
                <p class="text-purple-100 text-sm mb-2">Keuntungan</p>
                <p class="text-2xl font-bold">{{ rupiah($monthStats['profit']) }}</p>
                <p class="text-xs text-purple-200 mt-2">Laba bersih</p>
            </div>
        </div>
    </div>

    <!-- Alerts -->
    @if($overdueOrders > 0 || $lowStockItems->count() > 0)
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @if($overdueOrders > 0)
        <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-lg">
            <div class="flex items-start">
                <i class="fas fa-exclamation-triangle text-red-500 text-xl mr-3 mt-1"></i>
                <div>
                    <p class="font-bold text-red-800">{{ $overdueOrders }} Order Terlambat</p>
                    <p class="text-red-600 text-sm mt-1">Order melewati deadline perlu segera diselesaikan</p>
                    <a href="{{ route('orders.index') }}" class="text-red-700 text-sm font-medium hover:underline mt-2 inline-block">
                        Lihat Order <i class="fas fa-arrow-right text-xs"></i>
                    </a>
                </div>
            </div>
        </div>
        @endif

        @if($lowStockItems->count() > 0)
        <div class="bg-orange-50 border-l-4 border-orange-500 p-4 rounded-lg">
            <div class="flex items-start">
                <i class="fas fa-box text-orange-500 text-xl mr-3 mt-1"></i>
                <div>
                    <p class="font-bold text-orange-800">{{ $lowStockItems->count() }} Item Stok Menipis</p>
                    <p class="text-orange-600 text-sm mt-1">Inventaris perlu diisi ulang segera</p>
                    <a href="{{ route('inventory.index') }}" class="text-orange-700 text-sm font-medium hover:underline mt-2 inline-block">
                        Kelola Stok <i class="fas fa-arrow-right text-xs"></i>
                    </a>
                </div>
            </div>
        </div>
        @endif
    </div>
    @endif

    <!-- Status & Top Customers -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 md:gap-6">
        <!-- Order by Status -->
        <div class="bg-white rounded-xl shadow-md p-5 md:p-6">
            <h3 class="text-lg font-bold mb-4 text-gray-800 flex items-center">
                <i class="fas fa-chart-pie text-blue-500 mr-2"></i> 
                Status Order
            </h3>
            <div class="space-y-3">
                @foreach(['in_progress' => 'Diproses', 'ready' => 'Siap Diambil', 'completed' => 'Selesai', 'cancelled' => 'Dibatalkan'] as $status => $label)
                    <div class="flex items-center justify-between py-2 border-b border-gray-100 last:border-0">
                        <span class="text-gray-700 font-medium">{{ $label }}</span>
                        <span class="bg-blue-100 text-blue-700 px-4 py-1 rounded-full text-sm font-semibold">
                            {{ $ordersByStatus->get($status, 0) }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Top Customers -->
        <div class="bg-white rounded-xl shadow-md p-5 md:p-6">
            <h3 class="text-lg font-bold mb-4 text-gray-800 flex items-center">
                <i class="fas fa-trophy text-yellow-500 mr-2"></i> 
                Pelanggan Teratas
            </h3>
            <div class="space-y-3">
                @forelse($topCustomers as $customer)
                    <div class="flex items-center justify-between py-2 border-b border-gray-100 last:border-0">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-user text-gray-600"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-800 text-sm">{{ $customer->name }}</p>
                                <p class="text-xs text-gray-500">{{ $customer->phone }}</p>
                            </div>
                        </div>
                        <span class="text-green-600 font-bold text-sm">{{ rupiah($customer->total_spent) }}</span>
                    </div>
                @empty
                    <p class="text-gray-500 text-center py-4 text-sm">Belum ada data</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Recent Orders -->
    <div class="bg-white rounded-xl shadow-md p-5 md:p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold text-gray-800 flex items-center">
                <i class="fas fa-clock text-blue-500 mr-2"></i> 
                Order Terbaru
            </h3>
            <a href="{{ route('orders.index') }}" class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                Lihat Semua <i class="fas fa-arrow-right text-xs"></i>
            </a>
        </div>
        <div class="overflow-x-auto -mx-5 md:mx-0">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50 sticky top-0">
                    <tr>
                        <th class="px-4 md:px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Tanggal Order</th>
                        <th class="px-4 md:px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">No. Order</th>
                        <th class="px-4 md:px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Pelanggan</th>
                        <th class="px-4 md:px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Status</th>
                        <th class="px-4 md:px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Total</th>
                        <th class="px-4 md:px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase hidden md:table-cell">Tanggal</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($recentOrders as $order)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-4 md:px-6 py-3 md:py-4 whitespace-nowrap text-sm text-gray-600">
                                {{ $order->created_at->format('d M Y') ?? ''}}
                            </td>
                            <td class="px-4 md:px-6 py-3 md:py-4 whitespace-nowrap">
                                <a href="{{ route('orders.show', $order) }}" class="text-blue-600 hover:underline font-medium text-sm">
                                    {{ $order->order_number }}
                                </a>
                            </td>
                            <td class="px-4 md:px-6 py-3 md:py-4">
                                <p class="font-medium text-gray-800 text-sm">{{ $order->customer->name }}</p>
                            </td>
                            <td class="px-4 md:px-6 py-3 md:py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs rounded-full font-medium
                                    @if($order->status == 'completed') bg-green-100 text-green-700
                                    @elseif($order->status == 'cancelled') bg-red-100 text-red-700
                                    @elseif($order->status == 'ready') bg-blue-100 text-blue-700
                                    @else bg-yellow-100 text-yellow-700
                                    @endif">
                                    {{ statusLabel($order->status, 'order') }}
                                </span>
                            </td>
                            <td class="px-4 md:px-6 py-3 md:py-4 whitespace-nowrap font-semibold text-gray-800 text-sm">
                                {{ rupiah($order->total) }}
                            </td>
                            <td class="px-4 md:px-6 py-3 md:py-4 whitespace-nowrap text-gray-600 text-sm hidden md:table-cell">
                                {{ $order->created_at->format('d M Y') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                <i class="fas fa-inbox text-4xl text-gray-300 mb-2"></i>
                                <p>Belum ada order</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Low Stock Items (if any) -->
    @if($lowStockItems->count() > 0)
    <div class="bg-white rounded-xl shadow-md p-5 md:p-6">
        <h3 class="text-lg font-bold mb-4 text-gray-800 flex items-center">
            <i class="fas fa-exclamation-circle text-orange-500 mr-2"></i> 
            Stok Menipis
        </h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($lowStockItems as $item)
                <div class="border-2 border-orange-200 rounded-lg p-4 bg-orange-50 hover:shadow-md transition">
                    <div class="flex justify-between items-start mb-2">
                        <p class="font-semibold text-gray-800">{{ $item->name }}</p>
                        <span class="text-xs bg-orange-200 text-orange-800 px-2 py-1 rounded">Low</span>
                    </div>
                    <p class="text-sm text-gray-600 mb-3">SKU: {{ $item->sku }}</p>
                    <div class="flex justify-between items-center">
                        <div>
                            <span class="text-orange-600 font-bold text-lg">{{ $item->quantity }}</span>
                            <span class="text-gray-500 text-sm"> {{ $item->unit }}</span>
                        </div>
                        <a href="{{ route('inventory.show', $item) }}" class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                            Kelola <i class="fas fa-arrow-right text-xs"></i>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection
