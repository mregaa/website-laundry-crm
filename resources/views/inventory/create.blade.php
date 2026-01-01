@extends('layouts.app')

@section('title', 'Add Inventory Item')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-2xl mx-auto">
        <div class="mb-6">
            <h1 class="text-3xl font-bold">Add New Inventory Item</h1>
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

        <form action="{{ route('inventory.store') }}" method="POST">
            @csrf
            
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-gray-700 mb-2">Nama *</label>
                        <input type="text" name="name" value="{{ old('name') }}" 
                               class="w-full border rounded px-3 py-2" required>
                    </div>

                    <div>
                        <label class="block text-gray-700 mb-2">SKU *</label>
                        <input type="text" name="sku" value="{{ old('sku') }}" 
                               class="w-full border rounded px-3 py-2" required>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-gray-700 mb-2">Description</label>
                        <textarea name="description" class="w-full border rounded px-3 py-2" rows="3">{{ old('description') }}</textarea>
                    </div>

                    <div>
                        <label class="block text-gray-700 mb-2">Kategori *</label>
                        <select name="category" class="w-full border rounded px-3 py-2" required>
                            <option value="">Select Kategori</option>
                            <option value="detergent">Detergent</option>
                            <option value="fabric_softener">Fabric Softener</option>
                            <option value="bleach">Bleach</option>
                            <option value="starch">Starch</option>
                            <option value="hangers">Hangers</option>
                            <option value="bags">Bags</option>
                            <option value="other">Other</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-gray-700 mb-2">Unit *</label>
                        <input type="text" name="unit" value="{{ old('unit', 'pcs') }}" 
                               class="w-full border rounded px-3 py-2" 
                               placeholder="e.g., kg, liter, pcs" required>
                    </div>

                    <div>
                        <label class="block text-gray-700 mb-2">Initial Quantity *</label>
                        <input type="number" name="quantity" step="0.01" min="0" 
                               value="{{ old('quantity', 0) }}" 
                               class="w-full border rounded px-3 py-2" required>
                    </div>

                    <div>
                        <label class="block text-gray-700 mb-2">Unit Price *</label>
                        <input type="number" name="unit_price" step="0.01" min="0" 
                               value="{{ old('unit_price') }}" 
                               class="w-full border rounded px-3 py-2" required>
                    </div>

                    <div>
                        <label class="block text-gray-700 mb-2">Tingkat Pemesanan Ulang *</label>
                        <input type="number" name="reorder_level" step="0.01" min="0" 
                               value="{{ old('reorder_level') }}" 
                               class="w-full border rounded px-3 py-2" required>
                        <small class="text-gray-500">Peringatan ketika stok mencapai tingkat ini</small>
                    </div>

                    <div>
                        <label class="block text-gray-700 mb-2">Tingkat Stok Maksimal</label>
                        <input type="number" name="max_stock_level" step="0.01" min="0" 
                               value="{{ old('max_stock_level') }}" 
                               class="w-full border rounded px-3 py-2">
                    </div>
                </div>
            </div>

            <div class="mt-6 flex justify-end space-x-2">
                <a href="{{ route('inventory.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded">
                    Batal
                </a>
                <button type="submit" class="bg-green-500 hover:bg-green-600 text-white px-6 py-2 rounded">
                    <i class="fas fa-save"></i> Simpan Item
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
