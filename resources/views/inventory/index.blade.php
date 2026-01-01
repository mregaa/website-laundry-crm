@extends('layouts.app')

@section('title', 'Inventory Management')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6 flex justify-between items-center">
        <h1 class="text-3xl font-bold">Inventory Management</h1>
        <a href="{{ route('inventory.create') }}" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded">
            <i class="fas fa-plus"></i> Add Item
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-md p-4 mb-6">
        <form action="{{ route('inventory.index') }}" method="GET" class="flex flex-wrap gap-4">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-gray-700 mb-2">Kategori</label>
                <select name="category" class="w-full border rounded px-3 py-2">
                    <option value="">Semua Kategori</option>
                    <option value="detergent" {{ request('category') === 'detergent' ? 'selected' : '' }}>Detergent</option>
                    <option value="fabric_softener" {{ request('category') === 'fabric_softener' ? 'selected' : '' }}>Fabric Softener</option>
                    <option value="bleach" {{ request('category') === 'bleach' ? 'selected' : '' }}>Bleach</option>
                    <option value="starch" {{ request('category') === 'starch' ? 'selected' : '' }}>Starch</option>
                    <option value="hangers" {{ request('category') === 'hangers' ? 'selected' : '' }}>Hangers</option>
                    <option value="bags" {{ request('category') === 'bags' ? 'selected' : '' }}>Bags</option>
                    <option value="other" {{ request('category') === 'other' ? 'selected' : '' }}>Other</option>
                </select>
            </div>
            <div class="flex items-end">
                <label class="flex items-center">
                    <input type="checkbox" name="low_stock" value="1" {{ request('low_stock') ? 'checked' : '' }} class="mr-2">
                    <span class="text-gray-700">Tampilkan Hanya Stok Rendah</span>
                </label>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                    <i class="fas fa-filter"></i> Filter
                </button>
                <a href="{{ route('inventory.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">
                    <i class="fas fa-times"></i> Bersihkan
                </a>
            </div>
        </form>
    </div>

    <!-- Mobile Cards (< md) -->
    <div class="md:hidden space-y-4">
        @forelse($items as $item)
        <div class="bg-white rounded-lg shadow-md p-4 border-l-4 {{ $item->isLowStock() ? 'border-red-500 bg-red-50' : 'border-green-500' }}">
            
            <!-- Item Name & Status -->
            <div class="flex justify-between items-start mb-3">
                <div>
                    <h3 class="text-lg font-bold text-gray-900">{{ $item->name }}</h3>
                    <p class="text-xs text-gray-500 mt-1">
                        <i class="fas fa-barcode mr-1"></i>
                        SKU: {{ $item->sku }}
                    </p>
                </div>
                @if($item->isLowStock())
                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                        <i class="fas fa-exclamation-triangle"></i> Stok Rendah
                    </span>
                @else
                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                        <i class="fas fa-check"></i> Tersedia
                    </span>
                @endif
            </div>

            <!-- Category -->
            <div class="mb-3">
                <span class="px-2 py-1 rounded text-sm bg-gray-100 text-gray-700">
                    {{ ucfirst(str_replace('_', ' ', $item->category)) }}
                </span>
            </div>

            <!-- Quantity & Reorder Level -->
            <div class="grid grid-cols-2 gap-4 mb-3 pb-3 border-b border-gray-200">
                <div>
                    <p class="text-xs text-gray-500 mb-1">Stok Saat Ini</p>
                    <p class="text-lg font-bold {{ $item->isLowStock() ? 'text-red-600' : 'text-gray-900' }}">
                        {{ $item->quantity }} {{ $item->unit }}
                    </p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 mb-1">Batas Pemesanan</p>
                    <p class="text-lg font-bold text-gray-900">
                        {{ $item->reorder_level }} {{ $item->unit }}
                    </p>
                </div>
            </div>

            <!-- Price & Actions -->
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-xs text-gray-500 mb-1">Harga per Unit</p>
                    <p class="text-lg font-bold text-blue-600">${{ number_format($item->unit_price, 2) }}</p>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('inventory.show', $item) }}" 
                       class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition">
                        <i class="fas fa-eye"></i>
                    </a>
                    <a href="{{ route('inventory.edit', $item) }}" 
                       class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg font-medium transition">
                        <i class="fas fa-edit"></i>
                    </a>
                </div>
            </div>
        </div>
        @empty
        <div class="bg-white rounded-lg shadow-md p-12 text-center text-gray-500">
            <i class="fas fa-box-open text-5xl mb-4 text-gray-400"></i>
            <p class="text-lg font-medium">Tidak ada item ditemukan</p>
        </div>
        @endforelse
    </div>

    <!-- Desktop Table (>= md) -->
    <div class="hidden md:block bg-white rounded-lg shadow-md overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50 sticky top-0">
                <tr>
                    <th class="px-4 py-3 text-left">SKU</th>
                    <th class="px-4 py-3 text-left">Nama</th>
                    <th class="px-4 py-3 text-left">Kategori</th>
                    <th class="px-4 py-3 text-right">Quantity</th>
                    <th class="px-4 py-3 text-right">Tingkat Pemesanan Ulang</th>
                    <th class="px-4 py-3 text-right">Unit Price</th>
                    <th class="px-4 py-3 text-center">Status</th>
                    <th class="px-4 py-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                    <tr class="border-t hover:bg-gray-50 transition {{ $item->isLowStock() ? 'bg-red-50' : '' }}">
                        <td class="px-4 py-3">{{ $item->sku }}</td>
                        <td class="px-4 py-3 font-semibold">{{ $item->name }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 rounded text-sm bg-gray-100">
                                {{ ucfirst(str_replace('_', ' ', $item->category)) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right font-semibold {{ $item->isLowStock() ? 'text-red-600' : '' }}">
                            {{ $item->quantity }} {{ $item->unit }}
                        </td>
                        <td class="px-4 py-3 text-right">{{ $item->reorder_level }} {{ $item->unit }}</td>
                        <td class="px-4 py-3 text-right">${{ number_format($item->unit_price, 2) }}</td>
                        <td class="px-4 py-3 text-center">
                            @if($item->isLowStock())
                                <span class="px-2 py-1 rounded text-sm bg-red-100 text-red-800">
                                    <i class="fas fa-exclamation-triangle"></i> Stok Rendah
                                </span>
                            @else
                                <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">
                                    <i class="fas fa-check"></i> Tersedia
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex justify-center space-x-2">
                                <a href="{{ route('inventory.show', $item) }}" 
                                   class="text-blue-500 hover:text-blue-700" title="Lihat">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('inventory.edit', $item) }}" 
                                   class="text-yellow-500 hover:text-yellow-700" title="Ubah">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-4 py-8 text-center text-gray-500">
                            Tidak ada item ditemukan
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $items->links() }}
    </div>
</div>
@endsection
