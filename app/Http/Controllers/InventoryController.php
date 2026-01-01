<?php

namespace App\Http\Controllers;

use App\Models\InventoryItem;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    /**
     * Display a listing of inventory items.
     */
    public function index(Request $request)
    {
        $query = InventoryItem::query();

        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        if ($request->has('low_stock') && $request->low_stock) {
            $query->whereRaw('quantity <= reorder_level');
        }

        $items = $query->latest()->paginate(20);

        return view('inventory.index', compact('items'));
    }

    /**
     * Show the form for creating a new inventory item.
     */
    public function create()
    {
        return view('inventory.create');
    }

    /**
     * Store a newly created inventory item.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|max:100|unique:inventory_items,sku',
            'description' => 'nullable|string|max:1000',
            'category' => 'required|in:detergent,fabric_softener,bleach,starch,hangers,bags,other',
            'quantity' => 'required|numeric|min:0|max:999999',
            'unit' => 'required|string|max:50',
            'unit_price' => 'required|numeric|min:0|max:999999',
            'reorder_level' => 'required|numeric|min:0|max:999999',
            'max_stock_level' => 'nullable|numeric|min:0|max:999999',
        ]);

        InventoryItem::create($validated);

        return redirect()->route('inventory.index')
                        ->with('success', 'Inventory item created successfully.');
    }

    /**
     * Display the specified inventory item.
     */
    public function show(InventoryItem $inventory)
    {
        $inventory->load(['inventoryTransactions' => fn($q) => $q->latest()->limit(20)]);

        return view('inventory.show', compact('inventory'));
    }

    /**
     * Show the form for editing the inventory item.
     */
    public function edit(InventoryItem $inventory)
    {
        return view('inventory.edit', compact('inventory'));
    }

    /**
     * Update the inventory item.
     */
    public function update(Request $request, InventoryItem $inventory)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|max:100|unique:inventory_items,sku,' . $inventory->id,
            'description' => 'nullable|string|max:1000',
            'category' => 'required|in:detergent,fabric_softener,bleach,starch,hangers,bags,other',
            'unit' => 'required|string|max:50',
            'unit_price' => 'required|numeric|min:0|max:999999',
            'reorder_level' => 'required|numeric|min:0|max:999999',
            'max_stock_level' => 'nullable|numeric|min:0|max:999999',
            'is_active' => 'boolean',
        ]);

        $inventory->update($validated);

        return redirect()->route('inventory.show', $inventory)
                        ->with('success', 'Inventory item updated successfully.');
    }

    /**
     * Adjust inventory stock.
     */
    public function adjustStock(Request $request, InventoryItem $inventory)
    {
        $validated = $request->validate([
            'type' => 'required|in:stock_in,stock_out,adjustment',
            'quantity' => 'required|numeric|min:0.01|max:999999',
            'reference_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:500',
        ]);

        // Prevent negative stock for stock_out operations
        if ($validated['type'] === 'stock_out') {
            if ($inventory->quantity < $validated['quantity']) {
                return back()->with('error', 'Insufficient stock. Available: ' . $inventory->quantity . ' ' . $inventory->unit);
            }
        }
        
        // Prevent exceeding max stock level for stock_in
        if (($validated['type'] === 'stock_in' || $validated['type'] === 'adjustment') && $inventory->max_stock_level) {
            if (($inventory->quantity + $validated['quantity']) > $inventory->max_stock_level) {
                return back()->with('warning', 'Adding this quantity will exceed maximum stock level of ' . $inventory->max_stock_level . ' ' . $inventory->unit);
            }
        }

        if ($validated['type'] === 'stock_in' || $validated['type'] === 'adjustment') {
            $inventory->addStock(
                $validated['quantity'],
                $validated['reference_number'] ?? null,
                $validated['notes'] ?? null
            );
            $message = 'Stock added successfully.';
        } else {
            $success = $inventory->removeStock(
                $validated['quantity'],
                $validated['type'],
                null,
                $validated['notes'] ?? null
            );
            
            if (!$success) {
                return back()->with('error', 'Insufficient stock. Available: ' . $inventory->quantity . ' ' . $inventory->unit);
            }
            
            $message = 'Stock removed successfully.';
        }

        return back()->with('success', $message);
    }

    /**
     * Delete the inventory item.
     */
    public function destroy(InventoryItem $inventory)
    {
        // Check if inventory has transactions
        if ($inventory->inventoryTransactions()->count() > 0) {
            return back()->with('error', 'Cannot delete inventory item with transaction history. Please deactivate instead.');
        }

        $inventory->delete();

        return redirect()->route('inventory.index')
                        ->with('success', 'Inventory item deleted successfully.');
    }
}
