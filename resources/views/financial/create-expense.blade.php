@extends('layouts.app')

@section('title', 'Add Expense')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-2xl mx-auto">
        <div class="mb-6">
            <h1 class="text-3xl font-bold">Tambah Pengeluaran Baru</h1>
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

        <form action="{{ route('financial.store-expense') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="space-y-4">
                    <div>
                        <label class="block text-gray-700 mb-2">Kategori *</label>
                        <select name="category" class="w-full border rounded px-3 py-2" required>
                            <option value="">Select Kategori</option>
                            <option value="salary">Salary</option>
                            <option value="utilities">Utilities</option>
                            <option value="supplies">Supplies</option>
                            <option value="maintenance">Maintenance</option>
                            <option value="marketing">Marketing</option>
                            <option value="rent">Rent</option>
                            <option value="equipment">Equipment</option>
                            <option value="transportation">Transportation</option>
                            <option value="other">Other</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-gray-700 mb-2">Jumlah *</label>
                        <input type="number" name="amount" step="0.01" min="0.01" 
                               value="{{ old('amount') }}" 
                               class="w-full border rounded px-3 py-2" required>
                    </div>

                    <div>
                        <label class="block text-gray-700 mb-2">Vendor</label>
                        <input type="text" name="vendor" 
                               value="{{ old('vendor') }}" 
                               class="w-full border rounded px-3 py-2" 
                               placeholder="e.g., ABC Suppliers">
                    </div>

                    <div>
                        <label class="block text-gray-700 mb-2">Description *</label>
                        <textarea name="description" class="w-full border rounded px-3 py-2" rows="3" required>{{ old('description') }}</textarea>
                    </div>

                    <div>
                        <label class="block text-gray-700 mb-2">Expense Tanggal *</label>
                        <input type="date" name="expense_date" 
                               value="{{ old('expense_date', date('Y-m-d')) }}" 
                               class="w-full border rounded px-3 py-2" required>
                    </div>

                    <div>
                        <label class="block text-gray-700 mb-2">Receipt (Optional)</label>
                        <input type="file" name="receipt" accept=".jpg,.jpeg,.png,.pdf" 
                               class="w-full border rounded px-3 py-2">
                        <small class="text-gray-500">Format yang didukung: JPG, PNG, PDF (Maks: 2MB)</small>
                    </div>
                </div>
            </div>

            <div class="mt-6 flex justify-end space-x-2">
                <a href="{{ route('financial.expenses') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded">
                    Batal
                </a>
                <button type="submit" class="bg-green-500 hover:bg-green-600 text-white px-6 py-2 rounded">
                    <i class="fas fa-save"></i> Simpan Expense
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
