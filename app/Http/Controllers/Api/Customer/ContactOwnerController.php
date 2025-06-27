<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\ContactCRM;

class ContactOwnerController extends Controller
{
    public function submit(Request $request)
    {
        // Validate only the required fields from the simplified form
        $validator = Validator::make($request->all(), [
            'property_id'    => 'required|integer|exists:properties,id',
            'client_name'    => 'required|string|max:255',
            'email'          => 'required|email|max:255',
            'phone_whatsapp' => 'required|string|max:20',
            'message'        => 'required|string|max:1000', // changed from notes to message
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Create contact
        $crm = ContactCRM::create([
            'property_id'    => $request->property_id,
            'client_name'    => $request->client_name,
            'email'          => $request->email,
            'phone_whatsapp' => $request->phone_whatsapp,
            'message'        => $request->message,  // changed from notes to message
            'stage'          => 'lead', // Default stage
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Contact request sent successfully!',
            'data'    => $crm,
        ]);
    }
}
