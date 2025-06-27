<?php

namespace App\Http\Controllers\Api\Owner;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\PhotoGallery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class OwnerPropertiesController extends Controller
{
    public function index()
    {
        $ownerId = auth()->id();

        $properties = Property::with('photoGallery', 'owner')
            ->where('owner_id', $ownerId)
            ->get();

        Log::info('Fetched properties for owner', ['owner_id' => $ownerId, 'properties' => $properties]);

        return response()->json($properties);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'area' => 'required|integer',
            'rooms' => 'required|integer',
            'bathrooms' => 'required|integer',
            'address' => 'required|string|max:255',
            // New fields validation
            'city' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'offer_type' => 'required|string|max:255',
            //
            'photoGallery' => 'nullable|array',
            'photoGallery.*' => 'image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($validator->fails()) {
            Log::error('Validation failed for property store', ['errors' => $validator->errors()]);
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $request->all();
        $data['owner_id'] = auth()->id();

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('images/owner/properties', 'public');
            $data['image'] = '/storage/' . $path;
            Log::info('Property main image uploaded', ['path' => $path]);
        }

        $property = Property::create($data);

        if ($request->hasFile('photoGallery')) {
            foreach ($request->file('photoGallery') as $photo) {
                $path = $photo->store('images/owner/properties', 'public');
                PhotoGallery::create([
                    'property_id' => $property->id,
                    'photo_url' => '/storage/' . $path,
                ]);
            }
        }

        Log::info('Property created', ['property' => $property]);
        return response()->json($property, 201);
    }

    public function show($id)
    {
        $property = Property::with('photoGallery')->findOrFail($id);
        Log::info('Fetched property by ID', ['property' => $property]);
        return response()->json($property);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'sometimes|required|string|max:255',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'area' => 'sometimes|required|integer',
            'rooms' => 'sometimes|required|integer',
            'bathrooms' => 'sometimes|required|integer',
            'address' => 'required|string|max:255',
            // New fields validation - accept sometimes because update might be partial
            'city' => 'sometimes|required|string|max:255',
            'type' => 'sometimes|required|string|max:255',
            'offer_type' => 'sometimes|required|string|max:255',
            //
            'photoGallery' => 'nullable|array',
            'photoGallery.*' => 'image|mimes:jpg,jpeg,png|max:2048',
            'existingGalleryUrls' => 'nullable|array',
            'existingGalleryUrls.*' => 'string',
        ]);

        if ($validator->fails()) {
            Log::error('Validation failed for property update', ['errors' => $validator->errors()]);
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $property = Property::findOrFail($id);
        $data = $request->all();

        // Handle main image replacement
        if ($request->hasFile('image')) {
            if ($property->image) {
                Storage::delete('public/' . ltrim($property->image, '/storage/'));
            }
            $path = $request->file('image')->store('images/owner/properties', 'public');
            $data['image'] = '/storage/' . $path;
        }

        $property->update($data);

        $urlsToKeep = $request->input('existingGalleryUrls', []);
        $existingGalleryPhotos = PhotoGallery::where('property_id', $property->id)->get();

        foreach ($existingGalleryPhotos as $photo) {
            if (!in_array($photo->photo_url, $urlsToKeep)) {
                Storage::delete('public/' . ltrim($photo->photo_url, '/storage/'));
                $photo->delete();
            }
        }

        if ($request->hasFile('photoGallery')) {
            foreach ($request->file('photoGallery') as $photo) {
                $path = $photo->store('images/owner/properties', 'public');
                PhotoGallery::create([
                    'property_id' => $property->id,
                    'photo_url' => '/storage/' . $path,
                ]);
            }
        }

        Log::info('Property updated', ['property' => $property]);
        return response()->json($property);
    }

    public function destroy($id)
    {
        $property = Property::findOrFail($id);

        if ($property->image) {
            Storage::delete('public/' . ltrim($property->image, '/storage/'));
        }

        foreach ($property->photoGallery as $photo) {
            Storage::delete('public/' . ltrim($photo->photo_url, '/storage/'));
            $photo->delete();
        }

        $property->delete();
        Log::info('Property deleted', ['property_id' => $id]);
        return response()->json(null, 204);
    }

    public function toggleFeatured($propertyId)
    {
        $property = Property::findOrFail($propertyId);
        $property->featured = !$property->featured;
        $property->save();

        return response()->json($property);
    }
}
