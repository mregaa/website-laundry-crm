@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-3xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Detail Layanan</h1>
            <a href="{{ route('services.index') }}" class="text-gray-600 hover:text-gray-800">
                <i class="fas fa-arrow-left mr-2"></i>Kembali to Services
            </a>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex justify-between items-start mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800 mb-2">{{ $service->name }}</h2>
                    <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $service->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $service->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('services.edit', $service) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                        <i class="fas fa-edit mr-1"></i> Ubah
                    </a>
                    <form action="{{ route('services.destroy', $service) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded">
                            <i class="fas fa-trash mr-1"></i> Hapus
                        </button>
                    </form>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Price</label>
                    <p class="text-2xl font-bold text-blue-600">${{ number_format($service->price, 2) }}</p>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Unit</label>
                    <p class="text-lg text-gray-800">{{ ucfirst($service->unit) }}</p>
                </div>
            </div>

            @if($service->description)
            <div class="mb-6">
                <label class="block text-sm font-bold text-gray-700 mb-2">Description</label>
                <p class="text-gray-600">{{ $service->description }}</p>
            </div>
            @endif

            <div class="border-t pt-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Informasi Tambahan</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm text-gray-600">Tambahd At</label>
                        <p class="text-gray-800">{{ $service->created_at->format('M d, Y h:i A') }}</p>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-600">Last Perbaruid</label>
                        <p class="text-gray-800">{{ $service->updated_at->format('M d, Y h:i A') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
