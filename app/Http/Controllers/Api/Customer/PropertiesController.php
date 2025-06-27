<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PropertiesController extends Controller
{
    // Fetch all properties with amenities and photo gallery
    public function getAllProperties()
    {
        $properties = Property::with('photoGallery', 'owner')->get();

        Log::info('Properties fetched with photo gallery: ', $properties->toArray());

        return response()->json($properties);
    }

    // Fetch a specific property by ID with amenities and photo gallery
    public function show($id)
    {
        $property = Property::with('photoGallery', 'owner')->findOrFail($id);

        Log::info('Property fetched with photo gallery: ', $property->toArray());

        return response()->json($property);
    }

    // Fetch featured properties with amenities and photo gallery
    public function getFeaturedProperties()
    {
        $featuredProperties = Property::where('featured', true)
            ->with('photoGallery')
            ->latest()
            ->take(4)
            ->get();

        Log::info('Featured properties fetched with photo gallery: ', $featuredProperties->toArray());

        return response()->json($featuredProperties, 200);
    }

        public function getPopularProperties()
    {
        $popularProperties = Property::with('owner', 'photoGallery')
            ->withCount('contacts')
            ->orderByDesc('contacts_count')
            ->limit(8)
            ->get();

        Log::info('Popular properties (most contacted) fetched: ', $popularProperties->toArray());

        return response()->json($popularProperties, 200);
    }
}
