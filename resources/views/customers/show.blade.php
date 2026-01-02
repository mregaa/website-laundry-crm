@extends('layouts.app')

@section('title', 'Detail Pelanggan')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6 flex justify-between items-center">
        <h1 class="text-3xl font-bold">{{ $customer->name }}</h1>
        <div class="space-x-2">
            <a href="{{ route('customers.edit', $customer) }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
                <i class="fas fa-edit"></i> Ubah
            </a>
            <a href="{{ route('customers.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Informasi Pelanggan -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-xl font-semibold mb-4">Informasi Pelanggan</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-gray-600">Nama</p>
                        <p class="font-semibold">{{ $customer->name }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600">Email</p>
                        <p class="font-semibold">{{ $customer->email ?? 'Tidak ada' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600">Telepon</p>
                        <p class="font-semibold">{{ $customer->phone }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600">Tanggal Lahir</p>
                        <p class="font-semibold">{{ $customer->birthdate ? $customer->birthdate->format('M d, Y') : 'Tidak ada' }}</p>
                    </div>
                    <div class="col-span-2">
                        <p class="text-gray-600">Alamat</p>
                        <p class="font-semibold">{{ $customer->address ?? 'Tidak ada' }}</p>
                    </div>
                </div>
            </div>

            <!-- Loyalty Information -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-xl font-semibold mb-4">Program Loyalitas</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-gray-600">Poin Loyalitas</p>
                        <p class="text-3xl font-bold text-blue-600">{{ $customer->loyalty_points }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600">Tingkat Keanggotaan</p>
                        <p class="text-2xl font-semibold">
                            <span class="px-3 py-1 rounded
                                @if($customer->membership_tier === 'platinum') bg-purple-100 text-purple-800
                                @elseif($customer->membership_tier === 'gold') bg-yellow-100 text-yellow-800
                                @elseif($customer->membership_tier === 'silver') bg-gray-300 text-gray-800
                                @else bg-orange-100 text-orange-800
                                @endif">
                                {{ ucfirst($customer->membership_tier) }}
                            </span>
                        </p>
                    </div>
                    <div>
                        <p class="text-gray-600">Total Belanja</p>
                        <p class="text-xl font-semibold">Rp {{ number_format($customer->totalSpent(), 2) }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600">Total Pesanan</p>
                        <p class="text-xl font-semibold">{{ $customer->orders->count() }}</p>
                    </div>
                </div>
            </div>

            <!-- Pesanan Terbaru -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold mb-4">Pesanan Terbaru</h2>
                
                <!-- Mobile Card View -->
                <div class="md:hidden space-y-3">
                    @forelse($customer->orders()->latest()->limit(10)->get() as $order)
                        <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                            <div class="flex justify-between items-start mb-2">
                                <div>
                                    <p class="font-bold text-blue-600">{{ $order->order_number }}</p>
                                    <p class="text-xs text-gray-500 mt-1">{{ $order->created_at->format('M d, Y') }}</p>
                                </div>
                                <span class="px-2 py-1 rounded text-xs font-semibold
                                    @if($order->status === 'completed') bg-green-100 text-green-800
                                    @elseif($order->status === 'cancelled') bg-red-100 text-red-800
                                    @else bg-yellow-100 text-yellow-800
                                    @endif">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </div>
                            <div class="flex justify-between items-center mt-3 pt-3 border-t border-gray-300">
                                <p class="text-lg font-bold">Rp {{ number_format($order->total, 2) }}</p>
                                <a href="{{ route('orders.show', $order) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm">
                                    <i class="fas fa-eye"></i> Lihat
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8 text-gray-500">
                            <i class="fas fa-inbox text-4xl mb-2"></i>
                            <p>Belum ada pesanan</p>
                        </div>
                    @endforelse
                </div>

                <!-- Desktop Table View -->
                <div class="hidden md:block overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left">Pesanan #</th>
                                <th class="px-4 py-2 text-left">Tanggal</th>
                                <th class="px-4 py-2 text-left">Status</th>
                                <th class="px-4 py-2 text-right">Total</th>
                                <th class="px-4 py-2 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($customer->orders()->latest()->limit(10)->get() as $order)
                                <tr class="border-t hover:bg-gray-50 transition">
                                    <td class="px-4 py-3">{{ $order->order_number }}</td>
                                    <td class="px-4 py-3">{{ $order->created_at->format('M d, Y') }}</td>
                                    <td class="px-4 py-3">
                                        <span class="px-2 py-1 rounded text-sm
                                            @if($order->status === 'completed') bg-green-100 text-green-800
                                            @elseif($order->status === 'cancelled') bg-red-100 text-red-800
                                            @else bg-yellow-100 text-yellow-800
                                            @endif">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-right font-semibold">Rp {{ number_format($order->total, 2) }}</td>
                                    <td class="px-4 py-3 text-center">
                                        <a href="{{ route('orders.show', $order) }}" class="text-blue-500 hover:text-blue-700">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                                        Belum ada pesanan
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Aksi Panel -->
        <div class="lg:col-span-1">
            <!-- Quick Aksi -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-xl font-semibold mb-4">Quick Aksi</h2>
                <div class="space-y-2">
                    <a href="{{ route('orders.create', ['customer_id' => $customer->id]) }}" 
                       class="block w-full bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded text-center">
                        <i class="fas fa-plus"></i> Pesanan Baru
                    </a>
                    <a href="{{ route('rewards.index') }}" 
                       class="block w-full bg-purple-500 hover:bg-purple-600 text-white px-4 py-2 rounded text-center">
                        <i class="fas fa-gift"></i> Redeem Reward
                    </a>
                </div>
            </div>

            <!-- Loyalty Transactions -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold mb-4">Loyalty History</h2>
                <div class="space-y-3 max-h-96 overflow-y-auto">
                    @forelse($customer->loyaltyTransactions()->latest()->limit(20)->get() as $transaction)
                        <div class="flex justify-between items-start border-b pb-2">
                            <div class="flex-1">
                                <p class="text-sm">{{ $transaction->description }}</p>
                                <p class="text-xs text-gray-500">{{ $transaction->created_at->format('M d, Y') }}</p>
                            </div>
                            <span class="font-semibold {{ $transaction->points > 0 ? 'text-green-600' : 'text-red-600' }}">
                                {{ $transaction->points > 0 ? '+' : '' }}{{ $transaction->points }}
                            </span>
                        </div>
                    @empty
                        <p class="text-gray-500 text-center">No loyalty transactions yet</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
