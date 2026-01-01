@extends('layouts.app')

@section('title', 'Inventory Item Details')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6 flex justify-between items-center">
        <h1 class="text-3xl font-bold">{{ $inventory->name }}</h1>
        <div class="space-x-2">
            <a href="{{ route('inventory.edit', $inventory) }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
                <i class="fas fa-edit"></i> Ubah
            </a>
            <a href="{{ route('inventory.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">
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
        <!-- Item Details -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-xl font-semibold mb-4">Informasi Item</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-gray-600">SKU</p>
                        <p class="font-semibold">{{ $inventory->sku }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600">Kategori</p>
                        <p class="font-semibold">{{ ucfirst(str_replace('_', ' ', $inventory->category)) }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600">Jumlah Saat Ini</p>
                        <p class="font-semibold text-2xl {{ $inventory->isLowStock() ? 'text-red-600' : 'text-green-600' }}">
                            {{ $inventory->quantity }} {{ $inventory->unit }}
                        </p>
                    </div>
                    <div>
                        <p class="text-gray-600">Tingkat Pemesanan Ulang</p>
                        <p class="font-semibold">{{ $inventory->reorder_level }} {{ $inventory->unit }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600">Unit Price</p>
                        <p class="font-semibold">${{ number_format($inventory->unit_price, 2) }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600">Total Value</p>
                        <p class="font-semibold">${{ number_format($inventory->quantity * $inventory->unit_price, 2) }}</p>
                    </div>
                    @if($inventory->max_stock_level)
                    <div>
                        <p class="text-gray-600">Tingkat Stok Maksimal</p>
                        <p class="font-semibold">{{ $inventory->max_stock_level }} {{ $inventory->unit }}</p>
                    </div>
                    @endif
                    <div>
                        <p class="text-gray-600">Status</p>
                        <p>
                            <span class="px-3 py-1 rounded-full text-sm
                                {{ $inventory->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $inventory->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </p>
                    </div>
                </div>
                @if($inventory->description)
                    <div class="mt-4">
                        <p class="text-gray-600">Description</p>
                        <p class="font-semibold">{{ $inventory->description }}</p>
                    </div>
                @endif

                @if($inventory->isLowStock())
                    <div class="mt-4 bg-red-50 border border-red-200 rounded p-4">
                        <p class="text-red-800">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>Peringatan Stok Rendah:</strong> Jumlah saat ini di bawah tingkat pemesanan ulang. Silakan isi stok segera.
                        </p>
                    </div>
                @endif
            </div>

            <!-- Transaction History -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold mb-4">Riwayat Transaksi</h2>
                
                <!-- Mobile Card View -->
                <div class="md:hidden space-y-3">
                    @forelse($inventory->inventoryTransactions as $transaction)
                        <div class="border rounded-lg p-4 {{ $transaction->type === 'stock_in' ? 'bg-green-50 border-green-200' : 'bg-red-50 border-red-200' }}">
                            <div class="flex justify-between items-start mb-2">
                                <div>
                                    <p class="text-xs text-gray-500">{{ $transaction->created_at->format('M d, Y H:i') }}</p>
                                    <span class="inline-block mt-1 px-2 py-1 rounded text-xs font-semibold
                                        {{ $transaction->type === 'stock_in' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ ucfirst(str_replace('_', ' ', $transaction->type)) }}
                                    </span>
                                </div>
                                <p class="text-xl font-bold {{ $transaction->quantity > 0 ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $transaction->quantity > 0 ? '+' : '' }}{{ $transaction->quantity }}
                                </p>
                            </div>
                            <div class="mt-2 pt-2 border-t {{ $transaction->type === 'stock_in' ? 'border-green-200' : 'border-red-200' }}">
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Saldo Setelah:</span>
                                    <span class="font-semibold">{{ $transaction->balance_after }} {{ $inventory->unit }}</span>
                                </div>
                                @if($transaction->notes)
                                    <p class="text-xs text-gray-600 mt-2">{{ $transaction->notes }}</p>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8 text-gray-500">
                            <i class="fas fa-clipboard-list text-4xl mb-2"></i>
                            <p>Belum ada transaksi</p>
                        </div>
                    @endforelse
                </div>

                <!-- Desktop Table View -->
                <div class="hidden md:block overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left">Tanggal</th>
                                <th class="px-4 py-2 text-left">Type</th>
                                <th class="px-4 py-2 text-right">Quantity</th>
                                <th class="px-4 py-2 text-right">Balance</th>
                                <th class="px-4 py-2 text-left">Notes</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($inventory->inventoryTransactions as $transaction)
                                <tr class="border-t hover:bg-gray-50 transition">
                                    <td class="px-4 py-3">{{ $transaction->created_at->format('M d, Y H:i') }}</td>
                                    <td class="px-4 py-3">
                                        <span class="px-2 py-1 rounded text-sm
                                            {{ $transaction->type === 'stock_in' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ ucfirst(str_replace('_', ' ', $transaction->type)) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-right font-semibold
                                        {{ $transaction->quantity > 0 ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $transaction->quantity > 0 ? '+' : '' }}{{ $transaction->quantity }} {{ $inventory->unit }}
                                    </td>
                                    <td class="px-4 py-3 text-right">{{ $transaction->balance_after }} {{ $inventory->unit }}</td>
                                    <td class="px-4 py-3">{{ $transaction->notes ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                                        Belum ada transaksi
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Adjust Stock -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold mb-4">Sesuaikan Stok</h2>
                <form action="{{ route('inventory.adjust', $inventory) }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-gray-700 mb-2">Type *</label>
                        <select name="type" class="w-full border rounded px-3 py-2" required>
                            <option value="stock_in">Stok Masuk (Tambah)</option>
                            <option value="stock_out">Stok Keluar (Kurangi)</option>
                            <option value="adjustment">Adjustment</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 mb-2">Quantity *</label>
                        <input type="number" name="quantity" step="0.01" min="0.01" 
                               class="w-full border rounded px-3 py-2" required>
                        <small class="text-gray-500">Saat ini: {{ $inventory->quantity }} {{ $inventory->unit }}</small>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 mb-2">Reference Number</label>
                        <input type="text" name="reference_number" 
                               class="w-full border rounded px-3 py-2" 
                               placeholder="e.g., PO-12345">
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 mb-2">Notes</label>
                        <textarea name="notes" class="w-full border rounded px-3 py-2" rows="3"></textarea>
                    </div>
                    <button type="submit" class="w-full bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
                        <i class="fas fa-sync"></i> Sesuaikan Stok
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
