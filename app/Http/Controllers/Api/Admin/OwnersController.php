<?php

namespace App\Http\Controllers\Api\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class OwnersController extends Controller
{
    /**
     * Get all owners (users with role = 'owner')
     */
    public function index()
    {
        $owners = User::where('role', 'owner')->get();

        return response()->json($owners);
    }

    /**
     * Create a new owner
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|unique:users,email',
            'phone'   => 'nullable|string|max:50',
            'password' => 'required|string|min:6',  // you may hash it
        ]);

        $owner = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'phone'    => $validated['phone'] ?? null,
            'role'     => 'owner',           // force role = owner
            'password' => bcrypt($validated['password']),
        ]);

        return response()->json($owner, 201);
    }

    /**
     * Update an existing owner by ID
     */
    public function update(Request $request, $id)
    {
        $owner = User::where('role', 'owner')->find($id);

        if (!$owner) {
            return response()->json(['message' => 'Owner not found'], 404);
        }

        $validated = $request->validate([
            'name'    => 'sometimes|required|string|max:255',
            'email'   => "sometimes|required|email|unique:users,email,{$id}",
            'phone'   => 'sometimes|nullable|string|max:50',
            'password' => 'sometimes|nullable|string|min:6',
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = bcrypt($validated['password']);
        }

        $owner->update($validated);

        return response()->json($owner);
    }
}

