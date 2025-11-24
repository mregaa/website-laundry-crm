@extends('layouts.app')

@section('title', 'Create Order')

@section('content')
<div class="max-w-5xl mx-auto">
    <div class="bg-white rounded-lg shadow-md p-8">
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-800">
                <i class="fas fa-plus-circle"></i> Create New Order
            </h1>
        </div>

        <form method="POST" action="{{ route('orders.store') }}" id="orderForm" class="space-y-6">
            @csrf

            <!-- Customer Selection -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Customer <span class="text-red-500">*</span>
                    </label>
                    <select name="customer_id" required
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('customer_id') border-red-500 @enderror">
                        <option value="">Select Customer</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                                {{ $customer->name }} - {{ $customer->phone }}
                            </option>
                        @endforeach
                    </select>
                    @error('customer_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Express Service
                    </label>
                    <div class="flex items-center mt-3">
                        <input type="checkbox" name="express_service" value="1" {{ old('express_service') ? 'checked' : '' }}
                               class="h-5 w-5 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label class="ml-2 text-gray-700">
                            Mark as express service (+20% charge)
                        </label>
                    </div>
                </div>
            </div>

            <!-- Dates -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Pickup Date
                    </label>
                    <input type="date" name="pickup_date" value="{{ old('pickup_date', date('Y-m-d')) }}"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Delivery Date
                    </label>
                    <input type="date" name="delivery_date" value="{{ old('delivery_date') }}"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <!-- Order Items -->
            <div>
                <div class="flex justify-between items-center mb-4">
                    <label class="block text-sm font-medium text-gray-700">
                        Order Items <span class="text-red-500">*</span>
                    </label>
                    <button type="button" onclick="addOrderItem()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm transition">
                        <i class="fas fa-plus"></i> Add Item
                    </button>
                </div>

                <div id="orderItems" class="space-y-4">
                    <div class="order-item border rounded-lg p-4 bg-gray-50">
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                            <div class="md:col-span-5">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Service</label>
                                <select name="items[0][service_id]" required
                                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="">Select Service</option>
                                    @foreach($services as $service)
                                        <option value="{{ $service->id }}" data-price="{{ $service->price }}" data-unit="{{ $service->unit }}">
                                            {{ $service->name }} - ${{ number_format($service->price, 2) }}/{{ $service->unit }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Quantity</label>
                                <input type="number" name="items[0][quantity]" step="0.01" min="0.01" required
                                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>

                            <div class="md:col-span-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                                <input type="text" name="items[0][notes]" placeholder="Optional notes"
                                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>

                            <div class="md:col-span-1 flex items-end">
                                <button type="button" onclick="removeOrderItem(this)" class="w-full bg-red-500 hover:bg-red-600 text-white px-3 py-2 rounded-lg transition">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Special Instructions -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Special Instructions
                </label>
                <textarea name="special_instructions" rows="3" placeholder="Any special handling requirements..."
                          class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('special_instructions') }}</textarea>
            </div>

            <!-- Submit Buttons -->
            <div class="flex justify-end space-x-4 pt-4 border-t">
                <a href="{{ route('orders.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-6 py-2 rounded-lg transition">
                    Cancel
                </a>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition">
                    <i class="fas fa-save"></i> Create Order
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
let itemIndex = 1;

function addOrderItem() {
    const container = document.getElementById('orderItems');
    const newItem = `
        <div class="order-item border rounded-lg p-4 bg-gray-50">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                <div class="md:col-span-5">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Service</label>
                    <select name="items[${itemIndex}][service_id]" required
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Select Service</option>
                        @foreach($services as $service)
                            <option value="{{ $service->id }}" data-price="{{ $service->price }}" data-unit="{{ $service->unit }}">
                                {{ $service->name }} - ${{ number_format($service->price, 2) }}/{{ $service->unit }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Quantity</label>
                    <input type="number" name="items[${itemIndex}][quantity]" step="0.01" min="0.01" required
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="md:col-span-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                    <input type="text" name="items[${itemIndex}][notes]" placeholder="Optional notes"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="md:col-span-1 flex items-end">
                    <button type="button" onclick="removeOrderItem(this)" class="w-full bg-red-500 hover:bg-red-600 text-white px-3 py-2 rounded-lg transition">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>
    `;
    
    container.insertAdjacentHTML('beforeend', newItem);
    itemIndex++;
}

function removeOrderItem(button) {
    const items = document.querySelectorAll('.order-item');
    if (items.length > 1) {
        button.closest('.order-item').remove();
    } else {
        alert('At least one item is required');
    }
}
</script>
@endpush
@endsection
