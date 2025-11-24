@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Inventory Management</h1>
        <button class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg">
            <i class="fas fa-plus mr-2"></i>Add Item
        </button>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <!-- Inventory Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($items as $item)
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <h3 class="text-xl font-bold text-gray-800">{{ $item->name }}</h3>
                    <p class="text-sm text-gray-500">SKU: {{ $item->sku }}</p>
                </div>
                @if($item->isLowStock())
                    <span class="px-2 py-1 bg-red-100 text-red-800 text-xs font-semibold rounded-full">
                        <i class="fas fa-exclamation-triangle"></i> Low Stock
                    </span>
                @else
                    <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">
                        In Stock
                    </span>
                @endif
            </div>

            <div class="space-y-3 mb-4">
                <div class="flex justify-between items-center">
                    <span class="text-gray-600 text-sm">Category:</span>
                    <span class="text-sm font-medium">{{ ucfirst($item->category) }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600 text-sm">Quantity:</span>
                    <span class="text-lg font-bold {{ $item->isLowStock() ? 'text-red-600' : 'text-green-600' }}">
                        {{ $item->quantity }} {{ $item->unit }}
                    </span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600 text-sm">Reorder Level:</span>
                    <span class="text-sm font-medium">{{ $item->reorder_level }} {{ $item->unit }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600 text-sm">Unit Price:</span>
                    <span class="text-sm font-medium">${{ number_format($item->unit_price, 2) }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600 text-sm">Total Value:</span>
                    <span class="text-lg font-bold text-blue-600">
                        ${{ number_format($item->quantity * $item->unit_price, 2) }}
                    </span>
                </div>
            </div>

            <div class="flex gap-2 pt-4 border-t">
                <button onclick="adjustStock({{ $item->id }}, 'add')" class="flex-1 bg-green-100 hover:bg-green-200 text-green-700 px-4 py-2 rounded text-sm font-medium">
                    <i class="fas fa-plus"></i> Add
                </button>
                <button onclick="adjustStock({{ $item->id }}, 'remove')" class="flex-1 bg-red-100 hover:bg-red-200 text-red-700 px-4 py-2 rounded text-sm font-medium">
                    <i class="fas fa-minus"></i> Remove
                </button>
                <button class="bg-blue-100 hover:bg-blue-200 text-blue-700 px-4 py-2 rounded text-sm">
                    <i class="fas fa-edit"></i>
                </button>
            </div>
        </div>
        @empty
        <div class="col-span-3 bg-white rounded-lg shadow-md p-12 text-center">
            <i class="fas fa-boxes text-4xl text-gray-400 mb-3"></i>
            <p class="text-gray-500 text-lg">No inventory items found</p>
            <button class="text-blue-600 hover:underline mt-2">Add your first item</button>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $items->links() }}
    </div>
</div>

<script>
function adjustStock(itemId, action) {
    const quantity = prompt(`Enter quantity to ${action}:`);
    if (quantity && !isNaN(quantity) && quantity > 0) {
        // Create form and submit
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/inventory/${itemId}/adjust`;
        
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        form.innerHTML = `
            <input type="hidden" name="_token" value="${csrfToken}">
            <input type="hidden" name="action" value="${action}">
            <input type="hidden" name="quantity" value="${quantity}">
        `;
        
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endsection
