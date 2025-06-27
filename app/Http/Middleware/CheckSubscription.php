<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckSubscription
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();

        if (!$user || $user->role !== 'owner') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $subscription = $user->subscription;
        if (!$subscription) {
            return response()->json(['message' => 'No subscription found.'], 403);
        }

        // Only check limit if the request is trying to add a property
        if ($request->isMethod('POST') && $request->routeIs('properties.store')) {
            $currentCount = $user->properties()->count();

            if ($currentCount >= $subscription->max_properties) {
                return response()->json(['message' => 'Property limit reached. Please upgrade your plan.'], 403);
            }
        }

        return $next($request);
    }
}
