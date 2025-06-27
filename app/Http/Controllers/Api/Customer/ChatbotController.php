<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Property;

class ChatbotController extends Controller
{
    public function handleMessage(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:500',
        ]);

        $userMessage = $request->input('message');
        Log::info('Received chatbot message', ['message' => $userMessage]);

        // Fetch all properties and format for RAG
        $properties = Property::all()->map(function ($property) {
            return [
                'id' => $property->id,
                'title' => $property->title,
                'content' => $property->description,
                'metadata' => [
                    'owner_id' => $property->owner_id,
                    'price' => $property->price,
                    'image' => $property->image,
                    'address' => $property->address,
                    'city' => $property->city,
                    'type' => $property->type,
                    'offer_type' => $property->offer_type,
                    'area' => $property->area,
                    'rooms' => $property->rooms,
                    'bathrooms' => $property->bathrooms,
                    'featured' => $property->featured,
                    'created_at' => $property->created_at->toDateTimeString(),
                    'updated_at' => $property->updated_at->toDateTimeString(),
                ],
            ];
        });

        $payload = [
            'message' => $userMessage,
            'properties' => $properties,
        ];

        $response = Http::post('http://localhost:8000/chat', $payload);

        if ($response->failed()) {
            Log::error('Chatbot service unavailable', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            return response()->json(['error' => 'Chatbot service unavailable'], 503);
        }

        $botReply = $response->json('reply') ?? 'Sorry, I could not process your request.';
        Log::info('Chatbot reply', ['reply' => $botReply]);

        return response()->json([
            'reply' => $botReply,
        ]);
    }
}
