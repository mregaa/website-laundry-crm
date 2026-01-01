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

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhereHas('customer', function ($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $orders = $query->latest()->paginate(20)->appends($request->all());

        return view('orders.index', compact('orders'));
    }

    /**
     * Show the form for creating a new order.
     */
    public function create()
    {
        $customers = Customer::orderBy('name')->get();
        $services = Service::where('is_active', true)->get();
        $rewards = \App\Models\Reward::where('is_active', true)->orderBy('points_required')->get();

        return view('orders.create', compact('customers', 'services', 'rewards'));
    }

    /**
     * Store a newly created order.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'pickup_date' => 'nullable|date|after_or_equal:today',
            'delivery_date' => 'nullable|date|after_or_equal:pickup_date',
            'special_instructions' => 'nullable|string|max:1000',
            'express_service' => 'boolean',
            'items' => 'required|array|min:1',
            'items.*.service_id' => 'required|exists:services,id',
            'items.*.quantity' => 'required|numeric|min:0.01|max:10000',
            'items.*.notes' => 'nullable|string|max:500',
            'reward_id' => 'nullable|exists:rewards,id',
        ]);

        $customer = Customer::findOrFail($validated['customer_id']);

        // Validate reward if selected
        $appliedReward = null;
        if (!empty($validated['reward_id'])) {
            $reward = \App\Models\Reward::findOrFail($validated['reward_id']);
            
            // Check if customer has enough points
            if ($customer->loyalty_points < $reward->points_required) {
                return back()->withInput()->withErrors([
                    'reward_id' => "Insufficient points. You need {$reward->points_required} points but only have {$customer->loyalty_points} points."
                ]);
            }
            
            $appliedReward = $reward;
        }

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

        // Apply reward discount if selected
        if ($appliedReward) {
            $discountAmount = 0;
            
            if ($appliedReward->discount_amount) {
                $discountAmount = $appliedReward->discount_amount;
            } elseif ($appliedReward->discount_percentage) {
                $discountAmount = ($order->subtotal * $appliedReward->discount_percentage) / 100;
            }
            
            // Apply discount
            $order->discount = $discountAmount;
            $order->calculateTotal();
            
            // Deduct loyalty points
            $customer->redeemLoyaltyPoints(
                $appliedReward->points_required,
                "Redeemed {$appliedReward->name} for order {$order->order_number}"
            );
            
            // Create customer reward record
            $customer->customerRewards()->create([
                'reward_id' => $appliedReward->id,
                'redeemed_at' => now(),
                'order_id' => $order->id,
            ]);
        }

        $order->save();

        return redirect()->route('orders.show', $order)
                        ->with('success', 'Order created successfully.' . ($appliedReward ? " {$appliedReward->name} applied!" : ''));
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
        // Prevent updating completed or cancelled orders
        if (in_array($order->status, ['completed', 'cancelled'])) {
            return back()->with('error', 'Cannot update ' . $order->status . ' orders.');
        }
        
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'pickup_date' => 'nullable|date|after_or_equal:today',
            'delivery_date' => 'nullable|date|after_or_equal:pickup_date',
            'special_instructions' => 'nullable|string|max:1000',
            'express_service' => 'boolean',
            'discount' => 'nullable|numeric|min:0|max:999999',
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
            'status' => 'required|in:in_progress,ready,completed,cancelled',
            'notes' => 'nullable|string|max:1000',
        ]);

        $oldStatus = $order->status;
        
        // Prevent invalid transitions
        if ($oldStatus === 'completed') {
            return back()->with('error', 'Cannot change status of completed order.');
        }
        
        if ($oldStatus === 'cancelled') {
            return back()->with('error', 'Cannot change status of cancelled order.');
        }
        
        // If already at this status, do nothing (idempotency)
        if ($oldStatus === $validated['status']) {
            return back()->with('info', 'Order is already at this status.');
        }
        
        $order->update(['status' => $validated['status']]);

        // If order is completed AND fully paid, award loyalty points
        if ($validated['status'] === 'completed' && $oldStatus !== 'completed' && $order->payment_status === 'paid') {
            // Calculate loyalty points: +10 per order + 2 per kg
            $totalWeight = $order->items->sum('quantity');
            $orderPoints = 10;
            $weightPoints = (int)($totalWeight * 2);
            $totalPoints = $orderPoints + $weightPoints;

            // Award loyalty points
            $order->customer->addLoyaltyPoints(
                $totalPoints,
                "Order {$order->order_number} completed (+10 base + {$weightPoints} for {$totalWeight}kg)",
                $order->id
            );
        }

        // Log status history with notes
        if (!empty($validated['notes'])) {
            $order->statusHistories()->create([
                'status' => $validated['status'],
                'notes' => $validated['notes'],
                'changed_at' => now(),
            ]);
        }

        return back()->with('success', 'Order status updated successfully.');
    }

    /**
     * Add payment to order.
     */
    public function addPayment(Request $request, Order $order)
    {
        $remainingBalance = $order->getRemainingBalance();
        
        // Prevent payment if already fully paid
        if ($remainingBalance <= 0) {
            return back()->with('error', 'Order is already fully paid.');
        }
        
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01|max:' . $remainingBalance,
            'payment_method' => 'required|in:cash,card,bank_transfer,e-wallet',
            'notes' => 'nullable|string|max:500',
        ]);

        // Create payment record
        Payment::create([
            'order_id' => $order->id,
            'amount' => $validated['amount'],
            'payment_method' => $validated['payment_method'],
            'notes' => $validated['notes'] ?? null,
            'paid_at' => now(),
        ]);

        // Update paid amount
        $order->paid_amount += $validated['amount'];
        
        // Update payment status
        $oldPaymentStatus = $order->payment_status;
        if ($order->paid_amount >= $order->total) {
            $order->payment_status = 'paid';
        } elseif ($order->paid_amount > 0) {
            $order->payment_status = 'partial';
        }
        
        $order->save();
        
        // If order is completed AND just became fully paid, award loyalty points
        if ($order->status === 'completed' && $order->payment_status === 'paid' && $oldPaymentStatus !== 'paid') {
            // Calculate loyalty points: +10 per order + 2 per kg
            $totalWeight = $order->items->sum('quantity');
            $orderPoints = 10;
            $weightPoints = (int)($totalWeight * 2);
            $totalPoints = $orderPoints + $weightPoints;

            // Award loyalty points
            $order->customer->addLoyaltyPoints(
                $totalPoints,
                "Order {$order->order_number} completed and paid (+10 base + {$weightPoints} for {$totalWeight}kg)",
                $order->id
            );
        }

        // Create transaction for this payment
        \App\Models\Transaction::create([
            'transaction_number' => 'TXN-' . date('Ymd') . '-' . str_pad(\App\Models\Transaction::whereDate('created_at', today())->count() + 1, 4, '0', STR_PAD_LEFT),
            'type' => 'income',
            'category' => 'order_payment',
            'amount' => $validated['amount'],
            'order_id' => $order->id,
            'transaction_date' => now(),
            'description' => "Payment received for order {$order->order_number}",
        ]);

        return back()->with('success', 'Payment added successfully.');
    }

    /**
     * Delete the order.
     */
    public function destroy(Order $order)
    {
        // Prevent deletion of orders with payments or completed status
        if ($order->payments()->count() > 0) {
            return back()->with('error', 'Cannot delete order with existing payments. Please cancel the order instead.');
        }
        
        if ($order->transactions()->count() > 0) {
            return back()->with('error', 'Cannot delete order with financial transactions. Please cancel the order instead.');
        }
        
        if (in_array($order->status, ['completed', 'out_for_delivery'])) {
            return back()->with('error', 'Cannot delete order that is completed or out for delivery. Please cancel the order instead.');
        }

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
