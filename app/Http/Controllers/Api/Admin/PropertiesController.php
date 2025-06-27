<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\PhotoGallery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class PropertiesController extends Controller
{
    public function index()
    {
        $properties = Property::with('photoGallery','owner')->get();
        Log::info('Fetched all properties', ['properties' => $properties]);
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
    'city' => 'required|string|max:255',          // ✅ new
    'type' => 'required|string|max:100',          // ✅ new
    'offer_type' => 'required|string|max:100',    // ✅ new
    'photoGallery' => 'nullable|array',
    'photoGallery.*' => 'image|mimes:jpg,jpeg,png|max:2048',
]);


    if ($validator->fails()) {
        Log::error('Validation failed for property store', ['errors' => $validator->errors()]);
        return response()->json(['errors' => $validator->errors()], 422);
    }

    $data = $request->all();

    // Force owner_id to be the authenticated user's ID
    $data['owner_id'] = auth()->id();

    // Upload main image
    if ($request->hasFile('image')) {
        $path = $request->file('image')->store('images/properties', 'public');
        $data['image'] = '/storage/' . $path;
        Log::info('Property main image uploaded', ['path' => $path]);
    }

    $property = Property::create($data);

    // Upload photo gallery
    if ($request->hasFile('photoGallery')) {
        foreach ($request->file('photoGallery') as $photo) {
            $path = $photo->store('images/properties', 'public');
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
    'city' => 'sometimes|required|string|max:255',         // ✅ new
    'type' => 'sometimes|required|string|max:100',         // ✅ new
    'offer_type' => 'sometimes|required|string|max:100',   // ✅ new
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

    // Replace main image
    if ($request->hasFile('image')) {
        if ($property->image) {
            Storage::delete('public/' . ltrim($property->image, '/storage/'));
        }
        $path = $request->file('image')->store('images/properties', 'public');
        $data['image'] = '/storage/' . $path;
    }

    $property->update($data);

    // Keep the list of URLs to keep (from request)
    $urlsToKeep = $request->input('existingGalleryUrls', []);

    // Get all gallery images for this property
    $existingGalleryPhotos = PhotoGallery::where('property_id', $property->id)->get();

    foreach ($existingGalleryPhotos as $photo) {
        // If photo's URL is NOT in the URLs to keep => delete file and DB record
        if (!in_array($photo->photo_url, $urlsToKeep)) {
            // Delete the file from storage
            Storage::delete('public/' . ltrim($photo->photo_url, '/storage/'));

            // Delete DB record
            $photo->delete();
        }
    }

    // Add new gallery images (uploaded)
    if ($request->hasFile('photoGallery')) {
        foreach ($request->file('photoGallery') as $photo) {
            $path = $photo->store('images/properties', 'public');
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

        // Delete main image
        if ($property->image) {
            Storage::delete('public/' . $property->image);
        }

        // Delete related gallery images
        foreach ($property->photoGallery as $photo) {
            Storage::delete('public/' . $photo->photo_url);
            $photo->delete();
        }

        // Delete property
        $property->delete();
        Log::info('Property deleted', ['property_id' => $id]);
        return response()->json(null, 204);
    }


    public function toggleFeatured($propertyId)
    {
        // Find the room by ID
        $property = Property::findOrFail($propertyId);

        // Toggle the featured status
        $property->featured = !$property->featured;

        // Save the room with the updated featured status
        $property->save();

        // Return the updated room as a JSON response
        return response()->json($property);
    }
}
