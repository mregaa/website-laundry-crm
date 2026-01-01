@extends('layouts.app')

@section('title', 'Tambah Reward')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-2xl mx-auto">
        <div class="mb-6">
            <h1 class="text-3xl font-bold">Tambah New Reward</h1>
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

        <form action="{{ route('rewards.store') }}" method="POST">
            @csrf
            
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="space-y-4">
                    <div>
                        <label class="block text-gray-700 mb-2">Reward Nama *</label>
                        <input type="text" name="name" value="{{ old('name') }}" 
                               class="w-full border rounded px-3 py-2" 
                               placeholder="e.g., $5 Discount" required>
                    </div>

                    <div>
                        <label class="block text-gray-700 mb-2">Description *</label>
                        <textarea name="description" class="w-full border rounded px-3 py-2" rows="3" required>{{ old('description') }}</textarea>
                    </div>

                    <div>
                        <label class="block text-gray-700 mb-2">Poin Dibutuhkan *</label>
                        <input type="number" name="points_required" min="1" 
                               value="{{ old('points_required') }}" 
                               class="w-full border rounded px-3 py-2" required>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-gray-700 mb-2">Discount Jumlah ($)</label>
                            <input type="number" name="discount_amount" step="0.01" min="0" 
                                   value="{{ old('discount_amount') }}" 
                                   class="w-full border rounded px-3 py-2" 
                                   placeholder="0.00">
                            <small class="text-gray-500">Fixed dollar discount</small>
                        </div>
                        <div>
                            <label class="block text-gray-700 mb-2">Discount Persentase (%)</label>
                            <input type="number" name="discount_percentage" min="0" max="100" 
                                   value="{{ old('discount_percentage') }}" 
                                   class="w-full border rounded px-3 py-2" 
                                   placeholder="0">
                            <small class="text-gray-500">Persentase discount</small>
                        </div>
                    </div>

                    <div>
                        <label class="flex items-center">
                            <input type="checkbox" name="is_active" value="1" 
                                   {{ old('is_active', true) ? 'checked' : '' }} class="mr-2">
                            <span class="text-gray-700">Active</span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="mt-6 flex justify-end space-x-2">
                <a href="{{ route('rewards.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded">
                    Batal
                </a>
                <button type="submit" class="bg-green-500 hover:bg-green-600 text-white px-6 py-2 rounded">
                    <i class="fas fa-save"></i> Tambah Reward
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
