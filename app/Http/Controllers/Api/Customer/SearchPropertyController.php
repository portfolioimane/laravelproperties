<?php
namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Property;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;



class SearchPropertyController extends Controller
{
    public function getFilterOptions()
    {
        return response()->json([
            'types' => Property::select('type')->distinct()->pluck('type'),
            'offers' => Property::select('offer_type')->distinct()->pluck('offer_type'),
            'cities' => Property::select('city')->distinct()->pluck('city'),
            'priceRanges' => [
                ['label' => 'Any Price', 'min' => null, 'max' => null],
                ['label' => 'Under 500,000 MAD', 'min' => 0, 'max' => 500000],
                ['label' => '500,000 – 1,000,000 MAD', 'min' => 500000, 'max' => 1000000],
                ['label' => '1,000,000 – 2,000,000MAD', 'min' => 1000000, 'max' => 2000000],
                ['label' => 'Over 2M MAD', 'min' => 2000000, 'max' => null],
            ],
        ]);
    }
public function search(Request $request)
{
    $query = Property::query();

    if ($request->filled('type')) {
        $query->where('type', $request->type);
    }

    if ($request->filled('offer_type')) {
        $query->where('offer_type', $request->offer_type);
    }

    if ($request->filled('city')) {
        $query->where('city', $request->city);
    }

    // Safely convert min_price and max_price to integers or null
    $minPrice = ($request->filled('min_price') && $request->min_price !== null && $request->min_price !== '') 
        ? (int) $request->min_price 
        : null;

    $maxPrice = ($request->filled('max_price') && $request->max_price !== null && $request->max_price !== '') 
        ? (int) $request->max_price 
        : null;

    // Debug log for received price filters
    \Log::info('Received Price Filters', [
        'min_price_raw' => $request->min_price,
        'max_price_raw' => $request->max_price,
        'min_price' => $minPrice,
        'max_price' => $maxPrice,
    ]);

    // Apply price filters only if min or max price is set (not null)
    if (!is_null($minPrice)) {
        $query->where('price', '>=', $minPrice);
    }
    if (!is_null($maxPrice)) {
        $query->where('price', '<=', $maxPrice);
    }

    $results = $query->latest()->get();

    // Debug matched property prices
    \Log::info('Matched Property Prices', $results->pluck('price')->all());

    return response()->json($results);
}




}
