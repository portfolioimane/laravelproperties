<?php

namespace App\Http\Controllers\Api\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Subscription;
use App\Models\Plan;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SubscriptionController extends Controller
{
    // Get all available plans
    public function getPlans()
    {
        $plans = Plan::all();
        return response()->json($plans);
    }

    // Get current user's subscription with plan info
    public function getSubscription()
    {
        $user = Auth::user();

        $subscription = Subscription::with('plan')->where('user_id', $user->id)->first();

        if (!$subscription) {
            return response()->json(null, 200); // no subscription yet
        }

        return response()->json($subscription, 200);
    }

    // Subscribe to a plan by plan_id and payment_method

public function subscribe(Request $request)
{
    $user = Auth::user();

    Log::info('Subscribe called', [
        'user_id' => $user->id,
        'request_data' => $request->all(),
    ]);

    $request->validate([
        'plan_id' => 'required|integer|exists:plans,id',
        'payment_method' => 'required|string|in:stripe,paypal',
    ]);

    $plan = Plan::find($request->plan_id);

    if (!$plan) {
        Log::warning('Invalid plan in subscribe', [
            'plan_id' => $request->plan_id,
            'user_id' => $user->id,
        ]);
        return response()->json(['message' => 'Invalid plan'], 422);
    }

    $expiresAt = $plan->duration_days ? Carbon::now()->addDays($plan->duration_days) : null;

    $subscription = Subscription::updateOrCreate(
        ['user_id' => $user->id],
        [
            'plan_id' => $plan->id,
            'expires_at' => $expiresAt,
            'payment_method' => $request->payment_method,
        ]
    );

    $subscription->load('plan');

    Log::info('Subscription created/updated', [
        'subscription_id' => $subscription->id,
        'user_id' => $user->id,
        'plan_id' => $plan->id,
        'payment_method' => $request->payment_method,
        'expires_at' => $expiresAt ? $expiresAt->toDateTimeString() : null,
    ]);

    return response()->json($subscription, 200);
}

    // Cancel subscription (delete)
    public function cancelSubscription()
    {
        $user = Auth::user();

        $subscription = Subscription::where('user_id', $user->id)->first();

        if (!$subscription) {
            return response()->json(['message' => 'No active subscription found'], 404);
        }

        $subscription->delete();

        return response()->json(['message' => 'Subscription cancelled'], 200);
    }

    // Get user's property count
    public function getPropertyCount()
    {
        $user = Auth::user();

        $count = $user->properties()->count();

        Log::info('Property count for user', [
            'user_id' => $user->id,
            'property_count' => $count,
        ]);

        return response()->json(['count' => $count], 200);
    }

    // Check if user can add a new property based on subscription limit
    public function canAddProperty()
    {
        $user = Auth::user();

        // Load subscription with plan relation
        $subscription = Subscription::with('plan')->where('user_id', $user->id)->first();

        if (!$subscription) {
            return response()->json(['message' => 'No subscription found'], 403);
        }

        $currentCount = $user->properties()->count();
        $maxProperties = $subscription->plan->max_properties;

        if ($currentCount >= $maxProperties) {
            return response()->json(['message' => 'Property limit reached. Please upgrade your plan.'], 403);
        }

        return response()->json(['message' => 'You can add a property'], 200);
    }
}
