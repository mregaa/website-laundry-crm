@extends('layouts.app')

@section('title', 'Pelanggan')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex justify-between items-center">
        <h1 class="text-3xl font-bold text-gray-800">
            <i class="fas fa-users"></i> Pelanggan
        </h1>
        <a href="{{ route('customers.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition">
            <i class="fas fa-plus"></i> Tambah Pelanggan
        </a>
    </div>

    <!-- Search and Filter -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <form method="GET" action="{{ route('customers.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Cari</label>
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Cari berdasarkan nama, telepon, atau email"
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tingkat Keanggotaan</label>
                <select name="tier" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Semua Tingkat</option>
                    <option value="bronze" {{ request('tier') == 'bronze' ? 'selected' : '' }}>Bronze</option>
                    <option value="silver" {{ request('tier') == 'silver' ? 'selected' : '' }}>Silver</option>
                    <option value="gold" {{ request('tier') == 'gold' ? 'selected' : '' }}>Gold</option>
                    <option value="platinum" {{ request('tier') == 'platinum' ? 'selected' : '' }}>Platinum</option>
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition">
                    <i class="fas fa-search"></i> Cari
                </button>
                <a href="{{ route('customers.index') }}" class="flex-1 bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg transition text-center">
                    <i class="fas fa-times"></i> Bersihkan
                </a>
            </div>
        </form>
    </div>

    <!-- Mobile Cards (< md) -->
    <div class="md:hidden space-y-4">
        @forelse($customers as $customer)
        <div class="bg-white rounded-lg shadow-md p-4 border-l-4 
            @if($customer->membership_tier == 'platinum') border-purple-500
            @elseif($customer->membership_tier == 'gold') border-yellow-500
            @elseif($customer->membership_tier == 'silver') border-gray-400
            @else border-orange-500
            @endif">
            
            <!-- Name & Tier -->
            <div class="flex justify-between items-start mb-3">
                <div>
                    <h3 class="text-lg font-bold text-gray-900">{{ $customer->name }}</h3>
                    <p class="text-xs text-gray-500 mt-1">
                        <i class="far fa-calendar mr-1"></i>
                        Anggota sejak {{ $customer->created_at->format('M Y') }}
                    </p>
                </div>
                <span class="px-3 py-1 text-xs font-semibold rounded-full
                    @if($customer->membership_tier == 'platinum') bg-purple-100 text-purple-800
                    @elseif($customer->membership_tier == 'gold') bg-yellow-100 text-yellow-800
                    @elseif($customer->membership_tier == 'silver') bg-gray-200 text-gray-800
                    @else bg-orange-100 text-orange-800
                    @endif">
                    {{ ucfirst($customer->membership_tier) }}
                </span>
            </div>

            <!-- Contact Info -->
            <div class="mb-3 pb-3 border-b border-gray-200">
                <p class="text-sm text-gray-900">
                    <i class="fas fa-phone mr-2 text-blue-600"></i>
                    {{ $customer->phone }}
                </p>
                @if($customer->email)
                <p class="text-sm text-gray-600 mt-1">
                    <i class="fas fa-envelope mr-2 text-blue-600"></i>
                    {{ $customer->email }}
                </p>
                @endif
            </div>

            <!-- Stats -->
            <div class="flex justify-between items-center mb-4">
                <div>
                    <p class="text-xs text-gray-500 mb-1">Poin Loyalitas</p>
                    <div class="flex items-center">
                        <i class="fas fa-star text-yellow-500 mr-2"></i>
                        <span class="text-lg font-bold text-gray-900">{{ $customer->loyalty_points }}</span>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-xs text-gray-500 mb-1">Total Pesanan</p>
                    <p class="text-lg font-bold text-blue-600">{{ $customer->orders_count }}</p>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex gap-2">
                <a href="{{ route('customers.show', $customer) }}" 
                   class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-center py-3 rounded-lg font-medium transition">
                    <i class="fas fa-eye mr-2"></i>Lihat Detail
                </a>
                <a href="{{ route('customers.edit', $customer) }}" 
                   class="flex-1 bg-green-600 hover:bg-green-700 text-white text-center py-3 rounded-lg font-medium transition">
                    <i class="fas fa-edit mr-2"></i>Edit
                </a>
            </div>
        </div>
        @empty
        <div class="bg-white rounded-lg shadow-md p-12 text-center text-gray-500">
            <i class="fas fa-users text-5xl mb-4 text-gray-400"></i>
            <p class="text-lg font-medium">Tidak ada pelanggan ditemukan</p>
        </div>
        @endforelse
    </div>

    <!-- Desktop Table (>= md) -->
    <div class="hidden md:block bg-white rounded-lg shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50 sticky top-0">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kontak</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tingkat</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Poin Loyalitas</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pesanan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($customers as $customer)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div>
                                    <div class="font-medium text-gray-900">{{ $customer->name }}</div>
                                    <div class="text-sm text-gray-500">Anggota sejak {{ $customer->created_at->format('M Y') }}</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $customer->phone }}</div>
                                @if($customer->email)
                                    <div class="text-sm text-gray-500">{{ $customer->email }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-3 py-1 text-xs rounded-full font-semibold
                                    @if($customer->membership_tier == 'platinum') bg-purple-100 text-purple-800
                                    @elseif($customer->membership_tier == 'gold') bg-yellow-100 text-yellow-800
                                    @elseif($customer->membership_tier == 'silver') bg-gray-200 text-gray-800
                                    @else bg-orange-100 text-orange-800
                                    @endif">
                                    {{ ucfirst($customer->membership_tier) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <i class="fas fa-star text-yellow-500 mr-2"></i>
                                    <span class="font-semibold">{{ $customer->loyalty_points }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-gray-900">{{ $customer->orders_count }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                <a href="{{ route('customers.show', $customer) }}" class="text-blue-600 hover:text-blue-900">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('customers.edit', $customer) }}" class="text-green-600 hover:text-green-900">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                Tidak ada pelanggan ditemukan
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $customers->links() }}
    </div>
</div>
@endsection
