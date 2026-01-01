@extends('layouts.app')

@section('title', 'Ubah Inventory Item')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-2xl mx-auto">
        <div class="mb-6">
            <h1 class="text-3xl font-bold">Ubah Inventory Item</h1>
        </div>

        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('inventory.update', $inventory) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-gray-700 mb-2">Nama *</label>
                        <input type="text" name="name" value="{{ old('name', $inventory->name) }}" 
                               class="w-full border rounded px-3 py-2" required>
                    </div>

                    <div>
                        <label class="block text-gray-700 mb-2">SKU *</label>
                        <input type="text" name="sku" value="{{ old('sku', $inventory->sku) }}" 
                               class="w-full border rounded px-3 py-2" required>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-gray-700 mb-2">Description</label>
                        <textarea name="description" class="w-full border rounded px-3 py-2" rows="3">{{ old('description', $inventory->description) }}</textarea>
                    </div>

                    <div>
                        <label class="block text-gray-700 mb-2">Kategori *</label>
                        <select name="category" class="w-full border rounded px-3 py-2" required>
                            <option value="">Select Kategori</option>
                            <option value="detergent" {{ $inventory->category === 'detergent' ? 'selected' : '' }}>Detergent</option>
                            <option value="fabric_softener" {{ $inventory->category === 'fabric_softener' ? 'selected' : '' }}>Fabric Softener</option>
                            <option value="bleach" {{ $inventory->category === 'bleach' ? 'selected' : '' }}>Bleach</option>
                            <option value="starch" {{ $inventory->category === 'starch' ? 'selected' : '' }}>Starch</option>
                            <option value="hangers" {{ $inventory->category === 'hangers' ? 'selected' : '' }}>Hangers</option>
                            <option value="bags" {{ $inventory->category === 'bags' ? 'selected' : '' }}>Bags</option>
                            <option value="other" {{ $inventory->category === 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-gray-700 mb-2">Unit *</label>
                        <input type="text" name="unit" value="{{ old('unit', $inventory->unit) }}" 
                               class="w-full border rounded px-3 py-2" 
                               placeholder="e.g., kg, liter, pcs" required>
                    </div>

                    <div>
                        <label class="block text-gray-700 mb-2">Jumlah Saat Ini</label>
                        <input type="number" value="{{ $inventory->quantity }}" 
                               class="w-full border rounded px-3 py-2 bg-gray-100" disabled>
                        <small class="text-gray-500">Gunakan "Sesuaikan Stok" untuk mengubah jumlah</small>
                    </div>

                    <div>
                        <label class="block text-gray-700 mb-2">Unit Price *</label>
                        <input type="number" name="unit_price" step="0.01" min="0" 
                               value="{{ old('unit_price', $inventory->unit_price) }}" 
                               class="w-full border rounded px-3 py-2" required>
                    </div>

                    <div>
                        <label class="block text-gray-700 mb-2">Tingkat Pemesanan Ulang *</label>
                        <input type="number" name="reorder_level" step="0.01" min="0" 
                               value="{{ old('reorder_level', $inventory->reorder_level) }}" 
                               class="w-full border rounded px-3 py-2" required>
                    </div>

                    <div>
                        <label class="block text-gray-700 mb-2">Tingkat Stok Maksimal</label>
                        <input type="number" name="max_stock_level" step="0.01" min="0" 
                               value="{{ old('max_stock_level', $inventory->max_stock_level) }}" 
                               class="w-full border rounded px-3 py-2">
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-gray-700 mb-2">Status</label>
                        <select name="is_active" class="w-full border rounded px-3 py-2">
                            <option value="1" {{ $inventory->is_active ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ !$inventory->is_active ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="mt-6 flex justify-end space-x-2">
                <a href="{{ route('inventory.show', $inventory) }}" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded">
                    Batal
                </a>
                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded">
                    <i class="fas fa-save"></i> Perbarui Item
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
