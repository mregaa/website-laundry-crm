@extends('layouts.app')

@section('title', 'Ubah Customer')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-2xl mx-auto">
        <div class="mb-6">
            <h1 class="text-3xl font-bold">Ubah Customer</h1>
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

        <form action="{{ route('customers.update', $customer) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="space-y-4">
                    <div>
                        <label class="block text-gray-700 mb-2">Nama *</label>
                        <input type="text" name="name" value="{{ old('name', $customer->name) }}" 
                               class="w-full border rounded px-3 py-2" required>
                    </div>

                    <div>
                        <label class="block text-gray-700 mb-2">Email</label>
                        <input type="email" name="email" value="{{ old('email', $customer->email) }}" 
                               class="w-full border rounded px-3 py-2">
                    </div>

                    <div>
                        <label class="block text-gray-700 mb-2">Telepon *</label>
                        <input type="text" name="phone" value="{{ old('phone', $customer->phone) }}" 
                               class="w-full border rounded px-3 py-2" required>
                    </div>

                    <div>
                        <label class="block text-gray-700 mb-2">Alamat</label>
                        <textarea name="address" class="w-full border rounded px-3 py-2" rows="3">{{ old('address', $customer->address) }}</textarea>
                    </div>

                    <div>
                        <label class="block text-gray-700 mb-2">Tanggal Lahir</label>
                        <input type="date" name="birthdate" 
                               value="{{ old('birthdate', $customer->birthdate ? $customer->birthdate->format('Y-m-d') : '') }}" 
                               class="w-full border rounded px-3 py-2">
                    </div>
                </div>
            </div>

            <div class="mt-6 flex justify-end space-x-2">
                <a href="{{ route('customers.show', $customer) }}" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded">
                    Batal
                </a>
                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded">
                    <i class="fas fa-save"></i> Perbarui Customer
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
