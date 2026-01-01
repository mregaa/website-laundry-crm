@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Layanan</h1>
        <a href="{{ route('services.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg">
            <i class="fas fa-plus mr-2"></i>Layanan Baru
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <!-- Services Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($services as $service)
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex justify-between items-start mb-4">
                <h3 class="text-xl font-bold text-gray-800">{{ $service->name }}</h3>
                <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $service->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                    {{ $service->is_active ? 'Aktif' : 'Tidak Aktif' }}
                </span>
            </div>

            <p class="text-gray-600 mb-4 text-sm">{{ $service->description }}</p>

            <div class="border-t pt-4 space-y-2">
                <div class="flex justify-between items-center">
                    <span class="text-gray-600 text-sm">Harga:</span>
                    <span class="text-lg font-bold text-blue-600">{{ rupiah($service->price) }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600 text-sm">Satuan:</span>
                    <span class="text-sm font-medium">{{ ucfirst($service->unit) }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600 text-sm">Waktu Estimasi:</span>
                    <span class="text-sm font-medium">
                        @if($service->estimated_time >= 1440)
                            {{ round($service->estimated_time / 1440) }} hari
                        @elseif($service->estimated_time >= 60)
                            {{ round($service->estimated_time / 60) }} jam
                        @else
                            {{ $service->estimated_time }} menit
                        @endif
                    </span>
                </div>
            </div>

            <div class="flex justify-end gap-2 mt-4 pt-4 border-t">
                <a href="{{ route('services.edit', $service) }}" class="text-blue-600 hover:text-blue-800">
                    <i class="fas fa-edit"></i> Ubah
                </a>
                <form action="{{ route('services.destroy', $service) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-red-600 hover:text-red-800">
                        <i class="fas fa-trash"></i> Hapus
                    </button>
                </form>
            </div>
        </div>
        @empty
        <div class="col-span-3 bg-white rounded-lg shadow-md p-12 text-center">
            <i class="fas fa-box-open text-4xl text-gray-400 mb-3"></i>
            <p class="text-gray-500 text-lg">Tidak ada layanan ditemukan</p>
            <a href="{{ route('services.create') }}" class="text-blue-600 hover:underline mt-2 inline-block">Buat layanan pertama</a>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $services->links() }}
    </div>
</div>
@endsection
