<?php

namespace App\Http\Controllers\Api\Owner;

use App\Http\Controllers\Controller;
use App\Models\ContactCRM;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class OwnerContactCRMController extends Controller
{
    // Get all contacts related to properties owned by the authenticated owner
  public function index()
{
    $ownerId = Auth::id();

    $contacts = ContactCRM::whereHas('property', function ($query) use ($ownerId) {
        $query->where('owner_id', $ownerId);
    })
    ->with('property') // Eager load the related property
    ->get();

    return response()->json($contacts);
}


    // Show a single contact if belongs to owner
    public function show($id)
    {
        $ownerId = Auth::id();

        $contact = ContactCRM::where('id', $id)
            ->whereHas('property', function ($query) use ($ownerId) {
                $query->where('owner_id', $ownerId);
            })
            ->firstOrFail();

        return response()->json($contact);
    }

    // Store a new contact (property must belong to owner)
    public function store(Request $request)
    {
        $ownerId = Auth::id();

        $data = $request->validate([
            'property_id' => [
                'required',
                'integer',
                Rule::exists('properties', 'id')->where(function ($query) use ($ownerId) {
                    $query->where('owner_id', $ownerId);
                }),
            ],
            'client_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone_whatsapp' => 'nullable|string|max:50',
            'project_type' => 'nullable|string|max:255',
            'lead_source' => 'nullable|string|max:255',
            'stage' => ['required', Rule::in(['lead', 'nurture', 'conversion', 'closed', 'follow_up'])],
            'last_contact' => 'nullable|date',
            'next_step' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'competitor' => 'nullable|string|max:255',
            'assigned_to' => 'nullable|integer|exists:users,id',
        ]);

        $contact = ContactCRM::create($data);

        return response()->json($contact, 201);
    }

    // Update a contact if it belongs to owner
    public function update(Request $request, $id)
    {
        $ownerId = Auth::id();

        $contact = ContactCRM::where('id', $id)
            ->whereHas('property', function ($query) use ($ownerId) {
                $query->where('owner_id', $ownerId);
            })
            ->firstOrFail();

        $data = $request->validate([
            'property_id' => [
                'sometimes',
                'integer',
                Rule::exists('properties', 'id')->where(function ($query) use ($ownerId) {
                    $query->where('owner_id', $ownerId);
                }),
            ],
            'client_name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|max:255',
            'phone_whatsapp' => 'nullable|string|max:50',
            'project_type' => 'nullable|string|max:255',
            'lead_source' => 'nullable|string|max:255',
            'stage' => [Rule::in(['lead', 'nurture', 'conversion', 'closed', 'follow_up'])],
            'last_contact' => 'nullable|date',
            'next_step' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'competitor' => 'nullable|string|max:255',
            'assigned_to' => 'nullable|integer|exists:users,id',
        ]);

        $contact->update($data);

        return response()->json($contact);
    }

    // Delete a contact if it belongs to owner
    public function destroy($id)
    {
        $ownerId = Auth::id();

        $contact = ContactCRM::where('id', $id)
            ->whereHas('property', function ($query) use ($ownerId) {
                $query->where('owner_id', $ownerId);
            })
            ->firstOrFail();

        $contact->delete();

        return response()->json(['message' => 'Contact deleted successfully']);
    }
}
