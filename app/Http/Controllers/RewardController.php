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
            'description' => 'required|string|max:1000',
            'points_required' => 'required|integer|min:1|max:999999',
            'discount_amount' => 'nullable|numeric|min:0|max:999999',
            'discount_percentage' => 'nullable|integer|min:0|max:100',
            'is_active' => 'boolean',
        ]);
        
        // Validate that at least one discount type is provided
        if (empty($validated['discount_amount']) && empty($validated['discount_percentage'])) {
            return back()->withInput()->withErrors([
                'discount_amount' => 'Either discount amount or discount percentage must be specified.'
            ]);
        }
        
        // Validate that only one discount type is provided
        if (!empty($validated['discount_amount']) && !empty($validated['discount_percentage'])) {
            return back()->withInput()->withErrors([
                'discount_amount' => 'Please specify either discount amount or discount percentage, not both.'
            ]);
        }

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
            'description' => 'required|string|max:1000',
            'points_required' => 'required|integer|min:1|max:999999',
            'discount_amount' => 'nullable|numeric|min:0|max:999999',
            'discount_percentage' => 'nullable|integer|min:0|max:100',
            'is_active' => 'boolean',
        ]);
        
        // Validate that at least one discount type is provided
        if (empty($validated['discount_amount']) && empty($validated['discount_percentage'])) {
            return back()->withInput()->withErrors([
                'discount_amount' => 'Either discount amount or discount percentage must be specified.'
            ]);
        }
        
        // Validate that only one discount type is provided
        if (!empty($validated['discount_amount']) && !empty($validated['discount_percentage'])) {
            return back()->withInput()->withErrors([
                'discount_amount' => 'Please specify either discount amount or discount percentage, not both.'
            ]);
        }

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

        // Check if reward is active
        if (!$reward->is_active) {
            return back()->with('error', 'This reward is currently not available.');
        }

        $customer = Customer::findOrFail($validated['customer_id']);

        // Check if customer has sufficient points
        if ($customer->loyalty_points < $reward->points_required) {
            return back()->with('error', 'Insufficient loyalty points. You need ' . $reward->points_required . ' points but only have ' . $customer->loyalty_points . ' points.');
        }

        // Check if customer has already redeemed this reward recently (within 30 days)
        $recentRedemption = $customer->customerRewards()
            ->where('reward_id', $reward->id)
            ->where('redeemed_at', '>=', now()->subDays(30))
            ->where(function($q) {
                $q->whereNull('expires_at')
                  ->orWhere('expires_at', '>=', now());
            })
            ->exists();

        if ($recentRedemption) {
            return back()->with('error', 'You have already redeemed this reward recently. Please use your existing reward first.');
        }

        // Attempt to redeem points
        $success = $customer->redeemLoyaltyPoints(
            $reward->points_required,
            "Redeemed reward: {$reward->name}"
        );

        if ($success) {
            // Create customer reward record
            $customer->customerRewards()->create([
                'reward_id' => $reward->id,
                'redeemed_at' => now(),
                'expires_at' => now()->addDays(30),
            ]);

            return back()->with('success', 'Reward redeemed successfully! ' . $customer->loyalty_points . ' points remaining.');
        }

        return back()->with('error', 'Failed to redeem reward. Please try again.');
    }
}
