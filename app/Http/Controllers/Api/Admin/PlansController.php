<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\Request;

class PlansController extends Controller
{
    /**
     * Display a listing of all plans.
     * Accessible by admin users only.
     */
    public function index()
    {
        // TODO: Add authorization (e.g., middleware or policies)
        $plans = Plan::all();
        return response()->json($plans);
    }

    /**
     * Show a single plan by ID.
     */
    public function show(Plan $plan)
    {
        return response()->json($plan);
    }

    /**
     * Update the specified plan.
     * Accessible by admin users only.
     */
    public function update(Request $request, Plan $plan)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:plans,name,' . $plan->id,
            'max_properties' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'duration_days' => 'nullable|integer|min:0',
        ]);

        $plan->update($validated);

        return response()->json($plan);
    }

    /**
     * Delete a plan (optional).
     */
    public function destroy(Plan $plan)
    {
        $plan->delete();
        return response()->json(['message' => 'Plan deleted successfully']);
    }
}
