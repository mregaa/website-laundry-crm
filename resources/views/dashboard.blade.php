@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex justify-between items-center">
        <h1 class="text-3xl font-bold text-gray-800">
            <i class="fas fa-dashboard"></i> Dashboard
        </h1>
        <div class="text-gray-600">
            {{ now()->format('l, F j, Y') }}
        </div>
    </div>

    <!-- Today's Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Today's Orders</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $todayStats['orders'] }}</p>
                </div>
                <div class="bg-blue-100 rounded-full p-3">
                    <i class="fas fa-shopping-cart text-blue-600 text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Today's Revenue</p>
                    <p class="text-3xl font-bold text-green-600">${{ number_format($todayStats['revenue'], 2) }}</p>
                </div>
                <div class="bg-green-100 rounded-full p-3">
                    <i class="fas fa-dollar-sign text-green-600 text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">New Customers</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $todayStats['new_customers'] }}</p>
                </div>
                <div class="bg-purple-100 rounded-full p-3">
                    <i class="fas fa-user-plus text-purple-600 text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Completed Today</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $todayStats['completed_orders'] }}</p>
                </div>
                <div class="bg-yellow-100 rounded-full p-3">
                    <i class="fas fa-check-circle text-yellow-600 text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- This Month's Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg shadow-md p-6 text-white">
            <p class="text-blue-100 text-sm">Monthly Orders</p>
            <p class="text-3xl font-bold">{{ $monthStats['orders'] }}</p>
        </div>

        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg shadow-md p-6 text-white">
            <p class="text-green-100 text-sm">Monthly Revenue</p>
            <p class="text-3xl font-bold">${{ number_format($monthStats['revenue'], 2) }}</p>
        </div>

        <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-lg shadow-md p-6 text-white">
            <p class="text-red-100 text-sm">Monthly Expenses</p>
            <p class="text-3xl font-bold">${{ number_format($monthStats['expenses'], 2) }}</p>
        </div>

        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg shadow-md p-6 text-white">
            <p class="text-purple-100 text-sm">Monthly Profit</p>
            <p class="text-3xl font-bold">${{ number_format($monthStats['profit'], 2) }}</p>
        </div>
    </div>

    <!-- Alerts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Overdue Orders -->
        @if($overdueOrders > 0)
        <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded">
            <div class="flex items-center">
                <i class="fas fa-exclamation-triangle text-red-500 text-xl mr-3"></i>
                <div>
                    <p class="font-bold text-red-800">{{ $overdueOrders }} Overdue Orders</p>
                    <p class="text-red-600 text-sm">Orders past their delivery date need attention</p>
                </div>
            </div>
        </div>
        @endif

        <!-- Low Stock Alert -->
        @if($lowStockItems->count() > 0)
        <div class="bg-orange-50 border-l-4 border-orange-500 p-4 rounded">
            <div class="flex items-center">
                <i class="fas fa-box text-orange-500 text-xl mr-3"></i>
                <div>
                    <p class="font-bold text-orange-800">{{ $lowStockItems->count() }} Low Stock Items</p>
                    <p class="text-orange-600 text-sm">Inventory items need restocking</p>
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- Order Status Distribution & Recent Orders -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Order Status -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-xl font-bold mb-4 text-gray-800">
                <i class="fas fa-chart-pie"></i> Orders by Status
            </h3>
            <div class="space-y-3">
                @foreach(['received', 'washing', 'drying', 'ironing', 'ready'] as $status)
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600 capitalize">{{ str_replace('_', ' ', $status) }}</span>
                        <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full font-semibold">
                            {{ $ordersByStatus->get($status, 0) }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Top Customers -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-xl font-bold mb-4 text-gray-800">
                <i class="fas fa-star"></i> Top Customers (This Month)
            </h3>
            <div class="space-y-3">
                @forelse($topCustomers as $customer)
                    <div class="flex items-center justify-between border-b pb-2">
                        <div>
                            <p class="font-semibold text-gray-800">{{ $customer->name }}</p>
                            <p class="text-sm text-gray-500">{{ $customer->phone }}</p>
                        </div>
                        <span class="text-green-600 font-bold">${{ number_format($customer->total_spent, 2) }}</span>
                    </div>
                @empty
                    <p class="text-gray-500 text-center">No data available</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Recent Orders -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold text-gray-800">
                <i class="fas fa-clock"></i> Recent Orders
            </h3>
            <a href="{{ route('orders.index') }}" class="text-blue-600 hover:text-blue-800">
                View All <i class="fas fa-arrow-right"></i>
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order #</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($recentOrders as $order)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <a href="{{ route('orders.show', $order) }}" class="text-blue-600 hover:underline">
                                    {{ $order->order_number }}
                                </a>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $order->customer->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs rounded-full 
                                    @if($order->status == 'completed') bg-green-100 text-green-800
                                    @elseif($order->status == 'cancelled') bg-red-100 text-red-800
                                    @else bg-blue-100 text-blue-800
                                    @endif">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">${{ number_format($order->total, 2) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $order->created_at->format('M d, Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500">No orders found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Low Stock Items -->
    @if($lowStockItems->count() > 0)
    <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-xl font-bold mb-4 text-gray-800">
            <i class="fas fa-exclamation-circle text-orange-500"></i> Low Stock Items
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($lowStockItems as $item)
                <div class="border rounded-lg p-4 bg-orange-50">
                    <p class="font-semibold text-gray-800">{{ $item->name }}</p>
                    <p class="text-sm text-gray-600">SKU: {{ $item->sku }}</p>
                    <div class="mt-2 flex justify-between items-center">
                        <span class="text-orange-600 font-bold">{{ $item->quantity }} {{ $item->unit }}</span>
                        <span class="text-xs text-gray-500">Reorder at: {{ $item->reorder_level }}</span>
                    </div>
                    <a href="{{ route('inventory.show', $item) }}" class="mt-2 inline-block text-blue-600 hover:underline text-sm">
                        Manage Stock <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection
