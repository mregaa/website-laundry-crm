@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Rewards Program</h1>
        <a href="{{ route('rewards.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg">
            <i class="fas fa-plus mr-2"></i>Tambah Reward
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <!-- Rewards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($rewards as $reward)
        <div class="bg-white rounded-lg shadow-md p-6 border-t-4 {{ $reward->is_active ? 'border-blue-500' : 'border-gray-300' }}">
            <div class="flex justify-between items-start mb-4">
                <div class="flex-1">
                    <h3 class="text-xl font-bold text-gray-800 mb-2">{{ $reward->name }}</h3>
                    <p class="text-gray-600 text-sm">{{ $reward->description }}</p>
                </div>
                <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $reward->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                    {{ $reward->is_active ? 'Active' : 'Inactive' }}
                </span>
            </div>

            <div class="bg-gradient-to-r from-blue-50 to-blue-100 rounded-lg p-4 mb-4">
                <div class="text-center">
                    <div class="text-3xl font-bold text-blue-600 mb-1">
                        {{ number_format($reward->points_required) }}
                    </div>
                    <div class="text-sm text-gray-600">Poin Dibutuhkan</div>
                </div>
            </div>

            <div class="space-y-2 mb-4">
                @if($reward->discount_amount)
                <div class="flex items-center justify-between bg-green-50 rounded p-3">
                    <span class="text-sm text-gray-700">Discount Jumlah:</span>
                    <span class="text-lg font-bold text-green-600">Rp {{ number_format($reward->discount_amount, 2) }}</span>
                </div>
                @endif

                @if($reward->discount_percentage)
                <div class="flex items-center justify-between bg-green-50 rounded p-3">
                    <span class="text-sm text-gray-700">Discount Persentase:</span>
                    <span class="text-lg font-bold text-green-600">{{ $reward->discount_percentage }}%</span>
                </div>
                @endif
            </div>

            <div class="flex gap-2 pt-4 border-t">
                <a href="{{ route('rewards.edit', $reward) }}" class="flex-1 bg-blue-100 hover:bg-blue-200 text-blue-700 px-4 py-2 rounded text-sm font-medium text-center">
                    <i class="fas fa-edit mr-1"></i> Ubah
                </a>
                <form action="{{ route('rewards.destroy', $reward) }}" method="POST" class="flex-1" onsubmit="return confirm('Apakah Anda yakin?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full bg-red-100 hover:bg-red-200 text-red-700 px-4 py-2 rounded text-sm font-medium">
                        <i class="fas fa-trash mr-1"></i> Hapus
                    </button>
                </form>
            </div>
        </div>
        @empty
        <div class="col-span-3 bg-white rounded-lg shadow-md p-12 text-center">
            <i class="fas fa-gift text-4xl text-gray-400 mb-3"></i>
            <p class="text-gray-500 text-lg">Tidak ada hadiah tersedia</p>
            <a href="{{ route('rewards.create') }}" class="text-blue-600 hover:underline mt-2 inline-block">Tambahkan hadiah pertama Anda</a>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $rewards->links() }}
    </div>
</div>
@endsection
