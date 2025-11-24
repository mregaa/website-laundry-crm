<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    /**
     * Display a listing of customers.
     */
    public function index(Request $request)
    {
        $query = Customer::query();

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->has('tier')) {
            $query->where('membership_tier', $request->tier);
        }

        $customers = $query->withCount('orders')
                          ->latest()
                          ->paginate(15);

        return view('customers.index', compact('customers'));
    }

    /**
     * Show the form for creating a new customer.
     */
    public function create()
    {
        return view('customers.create');
    }

    /**
     * Store a newly created customer in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:customers,email',
            'phone' => 'required|string|unique:customers,phone',
            'address' => 'nullable|string',
            'birthdate' => 'nullable|date',
        ]);

        $customer = Customer::create($validated);

        return redirect()->route('customers.show', $customer)
                        ->with('success', 'Customer created successfully.');
    }

    /**
     * Display the specified customer.
     */
    public function show(Customer $customer)
    {
        $customer->load([
            'orders' => fn($q) => $q->latest()->limit(10),
            'loyaltyTransactions' => fn($q) => $q->latest()->limit(10)
        ]);

        $stats = [
            'total_orders' => $customer->orders()->count(),
            'total_spent' => $customer->totalSpent(),
            'pending_orders' => $customer->orders()->where('payment_status', '!=', 'paid')->count(),
            'loyalty_points' => $customer->loyalty_points,
        ];

        return view('customers.show', compact('customer', 'stats'));
    }

    /**
     * Show the form for editing the specified customer.
     */
    public function edit(Customer $customer)
    {
        return view('customers.edit', compact('customer'));
    }

    /**
     * Update the specified customer in storage.
     */
    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:customers,email,' . $customer->id,
            'phone' => 'required|string|unique:customers,phone,' . $customer->id,
            'address' => 'nullable|string',
            'birthdate' => 'nullable|date',
        ]);

        $customer->update($validated);

        return redirect()->route('customers.show', $customer)
                        ->with('success', 'Customer updated successfully.');
    }

    /**
     * Remove the specified customer from storage.
     */
    public function destroy(Customer $customer)
    {
        $customer->delete();

        return redirect()->route('customers.index')
                        ->with('success', 'Customer deleted successfully.');
    }

    /**
     * Manage loyalty points.
     */
    public function manageLoyalty(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'action' => 'required|in:add,redeem',
            'points' => 'required|integer|min:1',
            'description' => 'required|string',
        ]);

        if ($validated['action'] === 'add') {
            $customer->addLoyaltyPoints($validated['points'], $validated['description']);
            $message = 'Loyalty points added successfully.';
        } else {
            $success = $customer->redeemLoyaltyPoints($validated['points'], $validated['description']);
            $message = $success ? 'Loyalty points redeemed successfully.' : 'Insufficient loyalty points.';
        }

        return back()->with('success', $message);
    }
}
