<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    /**
     * Display a listing of services.
     */
    public function index()
    {
        $services = Service::latest()->paginate(15);

        return view('services.index', compact('services'));
    }

    /**
     * Show the form for creating a new service.
     */
    public function create()
    {
        return view('services.create');
    }

    /**
     * Store a newly created service.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'unit' => 'required|in:kg,piece,item,load',
            'is_active' => 'boolean',
        ]);

        Service::create($validated);

        return redirect()->route('services.index')
                        ->with('success', 'Service created successfully.');
    }

    /**
     * Display the service.
     */
    public function show(Service $service)
    {
        return view('services.show', compact('service'));
    }

    /**
     * Show the form for editing the service.
     */
    public function edit(Service $service)
    {
        return view('services.edit', compact('service'));
    }

    /**
     * Update the service.
     */
    public function update(Request $request, Service $service)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'unit' => 'required|in:kg,piece,item,load',
            'is_active' => 'boolean',
        ]);

        $service->update($validated);

        return redirect()->route('services.index')
                        ->with('success', 'Service updated successfully.');
    }

    /**
     * Delete the service.
     */
    public function destroy(Service $service)
    {
        // Check if service has been used in any orders
        if ($service->orderItems()->count() > 0) {
            return back()->with('error', 'Cannot delete service that has been used in orders. Please deactivate instead.');
        }

        $service->delete();

        return redirect()->route('services.index')
                        ->with('success', 'Service deleted successfully.');
    }
}
