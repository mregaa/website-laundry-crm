<?php

namespace App\Http\Controllers;

use App\Models\Reward;
use App\Models\Customer;
use Illuminate\Http\Request;

class RewardController extends Controller
{
    /**
     * Display a listing of rewards.
     */
    public function index()
    {
        $rewards = Reward::paginate(15);

        return view('rewards.index', compact('rewards'));
    }    /**
     * Show the form for creating a new reward.
     */
    public function create()
    {
        return view('rewards.create');
    }

    /**
     * Store a newly created reward.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'points_required' => 'required|integer|min:1',
            'discount_amount' => 'nullable|numeric|min:0',
            'discount_percentage' => 'nullable|integer|min:0|max:100',
            'is_active' => 'boolean',
        ]);

        Reward::create($validated);

        return redirect()->route('rewards.index')
                        ->with('success', 'Reward created successfully.');
    }

    /**
     * Show the form for editing the reward.
     */
    public function edit(Reward $reward)
    {
        return view('rewards.edit', compact('reward'));
    }

    /**
     * Update the reward.
     */
    public function update(Request $request, Reward $reward)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'points_required' => 'required|integer|min:1',
            'discount_amount' => 'nullable|numeric|min:0',
            'discount_percentage' => 'nullable|integer|min:0|max:100',
            'is_active' => 'boolean',
        ]);

        $reward->update($validated);

        return redirect()->route('rewards.index')
                        ->with('success', 'Reward updated successfully.');
    }

    /**
     * Redeem a reward for a customer.
     */
    public function redeem(Request $request, Reward $reward)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
        ]);

        $customer = Customer::findOrFail($validated['customer_id']);

        if ($customer->loyalty_points < $reward->points_required) {
            return back()->with('error', 'Insufficient loyalty points.');
        }

        $success = $customer->redeemLoyaltyPoints(
            $reward->points_required,
            "Redeemed reward: {$reward->name}"
        );

        if ($success) {
            $customer->customerRewards()->create([
                'reward_id' => $reward->id,
                'redeemed_at' => now(),
                'expires_at' => now()->addDays(30),
            ]);

            return back()->with('success', 'Reward redeemed successfully.');
        }

        return back()->with('error', 'Failed to redeem reward.');
    }
}
