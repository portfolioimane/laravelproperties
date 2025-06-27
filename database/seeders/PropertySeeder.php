<?php

namespace Database\Seeders;

use App\Models\Property;
use Illuminate\Database\Seeder;

class PropertySeeder extends Seeder
{
    public function run(): void
    {
        Property::create([
            'title' => 'Modern Apartment in Fès',
            'description' => '2nd floor, sunny, well located...',
            'price' => 1200000,  // numeric price without quotes or commas
            'image' => 'uploads/img1.jpg',
            'area' => 120,
            'rooms' => 3,
            'bathrooms' => 2,
            'address' => '123 Rue de Fès, Fès',
            'city' => 'Fès',
            'type' => 'apartment',
            'offer_type' => 'sale',
            'owner_id' => 2,
            'featured' => true,
        ]);

        Property::create([
            'title' => 'Spacious Villa in Marrakesh',
            'description' => 'Luxurious villa with a large garden...',
            'price' => 3500000,  // numeric price without quotes or commas
            'image' => 'uploads/img2.jpg',
            'area' => 250,
            'rooms' => 5,
            'bathrooms' => 4,
            'address' => '456 Avenue des Jardins, Marrakesh',
            'city' => 'Marrakesh',
            'type' => 'villa',
            'offer_type' => 'rent',
            'owner_id' => 3,
            'featured' => false,
        ]);
    }
}
