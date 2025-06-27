<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Property;
use App\Models\ContactCRM;
use App\Models\Subscription;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    // Get count of all owners
    public function owners()
    {
        $ownerCount = User::where('role', 'owner')->count();
        return response()->json(['count' => $ownerCount]);
    }

    // Get count of all properties
    public function properties()
    {
        $propertyCount = Property::count();
        return response()->json(['count' => $propertyCount]);
    }

    // Get count of properties owned by authenticated owner
    public function myProperties()
    {
        $user = Auth::user();

        if ($user->role !== 'owner') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $myPropertyCount = Property::where('owner_id', $user->id)->count();
        return response()->json(['count' => $myPropertyCount]);
    }

    // Get count of CRM contacts for properties owned by authenticated owner
    public function myContacts()
    {
        $user = Auth::user();

        if ($user->role !== 'owner') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $propertyIds = Property::where('owner_id', $user->id)->pluck('id');
        $myContactCount = ContactCrm::whereIn('property_id', $propertyIds)->count();

        return response()->json(['count' => $myContactCount]);
    }

    // Get count of free and paid subscriptions based on payment_method
    public function subscriptions()
    {
        $paid = Subscription::whereNotNull('payment_method')->count();
        $free = Subscription::whereNull('payment_method')->count();

        return response()->json([
            'paid' => $paid,
            'free' => $free,
        ]);
    }
}
