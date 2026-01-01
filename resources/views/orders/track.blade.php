@extends('layouts.app')

@section('title', 'Track Order')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-3xl mx-auto">
        <h1 class="text-3xl font-bold mb-6 text-center">Track Your Order</h1>

        <!-- Search Form -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <form action="{{ route('orders.track') }}" method="GET">
                <div class="flex gap-2">
                    <input type="text" 
                           name="order_number" 
                           placeholder="Enter your order number (e.g., ORD-20240101-0001)" 
                           value="{{ request('order_number') }}"
                           class="flex-1 border rounded px-4 py-3" 
                           required>
                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded">
                        <i class="fas fa-search"></i> Track
                    </button>
                </div>
            </form>
        </div>

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif

        @isset($order)
            <!-- Order Found -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-2xl font-semibold mb-4">Order {{ $order->order_number }}</h2>
                
                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div>
                        <p class="text-gray-600">Customer</p>
                        <p class="font-semibold">{{ $order->customer->name }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600">Status</p>
                        <p>
                            <span class="px-3 py-1 rounded-full text-sm
                                @if($order->status === 'completed') bg-green-100 text-green-800
                                @elseif($order->status === 'cancelled') bg-red-100 text-red-800
                                @elseif($order->status === 'ready') bg-blue-100 text-blue-800
                                @else bg-yellow-100 text-yellow-800
                                @endif">
                                {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                            </span>
                        </p>
                    </div>
                    <div>
                        <p class="text-gray-600">Pickup Tanggal</p>
                        <p class="font-semibold">{{ $order->pickup_date ? $order->pickup_date->format('M d, Y') : 'Tidak ada' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600">Delivery Tanggal</p>
                        <p class="font-semibold">{{ $order->delivery_date ? $order->delivery_date->format('M d, Y') : 'Tidak ada' }}</p>
                    </div>
                </div>

                <!-- Order Progress -->
                <div class="mb-6">
                    <h3 class="text-lg font-semibold mb-4">Order Progress</h3>
                    <div class="relative">
                        @php
                            $statuses = ['in_progress', 'ready', 'completed'];
                            $statusLabels = ['in_progress' => 'Diproses', 'ready' => 'Siap Diambil', 'completed' => 'Selesai'];
                            $currentIndex = array_search($order->status, $statuses);
                            if ($currentIndex === false) $currentIndex = -1;
                        @endphp

                        <div class="flex justify-between items-center">
                            @foreach($statuses as $index => $status)
                                <div class="flex flex-col items-center flex-1">
                                    <div class="w-10 h-10 rounded-full flex items-center justify-center mb-2
                                        {{ $index <= $currentIndex ? 'bg-green-500 text-white' : 'bg-gray-300 text-gray-600' }}">
                                        @if($index <= $currentIndex)
                                            <i class="fas fa-check"></i>
                                        @else
                                            <i class="fas fa-circle text-xs"></i>
                                        @endif
                                    </div>
                                    <p class="text-xs text-center {{ $index <= $currentIndex ? 'font-semibold' : 'text-gray-500' }}">
                                        {{ $statusLabels[$status] }}
                                    </p>
                                </div>
                                @if($index < count($statuses) - 1)
                                    <div class="flex-1 h-1 {{ $index < $currentIndex ? 'bg-green-500' : 'bg-gray-300' }} -mt-8"></div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Status History -->
                @if($order->statusHistories->count() > 0)
                <div>
                    <h3 class="text-lg font-semibold mb-4">Status History</h3>
                    <div class="space-y-3">
                        @foreach($order->statusHistories->sortByDesc('changed_at') as $history)
                            <div class="flex items-start border-l-4 border-blue-500 pl-4 py-2">
                                <div class="flex-1">
                                    <p class="font-semibold">{{ ucfirst(str_replace('_', ' ', $history->status)) }}</p>
                                    <p class="text-sm text-gray-600">{{ $history->changed_at->format('M d, Y H:i A') }}</p>
                                    @if($history->notes)
                                        <p class="text-sm mt-1">{{ $history->notes }}</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            <!-- Contact Information -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 text-center">
                <p class="text-gray-700">
                    <i class="fas fa-info-circle text-blue-500"></i>
                    Untuk pertanyaan tentang pesanan Anda, silakan hubungi kami atau kunjungi lokasi kami.
                </p>
            </div>
        @endisset
    </div>
</div>
@endsection
