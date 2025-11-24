<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Customer;
use App\Models\Service;
use App\Models\Payment;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Display a listing of orders.
     */
    public function index(Request $request)
    {
        $query = Order::with('customer');

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhereHas('customer', function ($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $orders = $query->latest()->paginate(20);

        return view('orders.index', compact('orders'));
    }

    /**
     * Show the form for creating a new order.
     */
    public function create()
    {
        $customers = Customer::orderBy('name')->get();
        $services = Service::where('is_active', true)->get();

        return view('orders.create', compact('customers', 'services'));
    }

    /**
     * Store a newly created order.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'pickup_date' => 'nullable|date',
            'delivery_date' => 'nullable|date|after_or_equal:pickup_date',
            'special_instructions' => 'nullable|string',
            'express_service' => 'boolean',
            'items' => 'required|array|min:1',
            'items.*.service_id' => 'required|exists:services,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.notes' => 'nullable|string',
        ]);

        $order = Order::create([
            'customer_id' => $validated['customer_id'],
            'pickup_date' => $validated['pickup_date'] ?? null,
            'delivery_date' => $validated['delivery_date'] ?? null,
            'special_instructions' => $validated['special_instructions'] ?? null,
            'express_service' => $validated['express_service'] ?? false,
        ]);

        foreach ($validated['items'] as $item) {
            $service = Service::findOrFail($item['service_id']);
            
            $order->items()->create([
                'service_id' => $service->id,
                'quantity' => $item['quantity'],
                'unit_price' => $service->price,
                'notes' => $item['notes'] ?? null,
            ]);
        }

        $order->calculateTotal();
        $order->save();

        return redirect()->route('orders.show', $order)
                        ->with('success', 'Order created successfully.');
    }

    /**
     * Display the specified order.
     */
    public function show(Order $order)
    {
        $order->load(['customer', 'items.service', 'payments', 'statusHistories']);

        return view('orders.show', compact('order'));
    }

    /**
     * Show the form for editing the order.
     */
    public function edit(Order $order)
    {
        $customers = Customer::orderBy('name')->get();
        $services = Service::where('is_active', true)->get();
        $order->load('items');

        return view('orders.edit', compact('order', 'customers', 'services'));
    }

    /**
     * Update the specified order.
     */
    public function update(Request $request, Order $order)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'pickup_date' => 'nullable|date',
            'delivery_date' => 'nullable|date|after_or_equal:pickup_date',
            'special_instructions' => 'nullable|string',
            'express_service' => 'boolean',
            'discount' => 'nullable|numeric|min:0',
        ]);

        $order->update($validated);
        $order->calculateTotal();
        $order->save();

        return redirect()->route('orders.show', $order)
                        ->with('success', 'Order updated successfully.');
    }

    /**
     * Update order status.
     */
    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|in:received,sorting,washing,drying,ironing,folding,ready,out_for_delivery,completed,cancelled',
            'notes' => 'nullable|string',
        ]);

        $order->update(['status' => $validated['status']]);

        return back()->with('success', 'Order status updated successfully.');
    }

    /**
     * Add payment to order.
     */
    public function addPayment(Request $request, Order $order)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:cash,card,bank_transfer,e-wallet',
            'notes' => 'nullable|string',
        ]);

        Payment::create([
            'order_id' => $order->id,
            'amount' => $validated['amount'],
            'payment_method' => $validated['payment_method'],
            'notes' => $validated['notes'] ?? null,
            'paid_at' => now(),
        ]);

        return back()->with('success', 'Payment added successfully.');
    }

    /**
     * Delete the order.
     */
    public function destroy(Order $order)
    {
        $order->delete();

        return redirect()->route('orders.index')
                        ->with('success', 'Order deleted successfully.');
    }

    /**
     * Track order status.
     */
    public function track(Request $request)
    {
        $orderNumber = $request->input('order_number');
        
        if (!$orderNumber) {
            return view('orders.track');
        }

        $order = Order::where('order_number', $orderNumber)
                     ->with(['customer', 'statusHistories'])
                     ->first();

        if (!$order) {
            return view('orders.track')->with('error', 'Order not found.');
        }

        return view('orders.track', compact('order'));
    }
}
