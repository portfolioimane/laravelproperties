<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PhotoGallery;
use App\Models\Room;

class PhotoGallerySeeder extends Seeder
{
    public function run()
    {
        $room = Room::first();

        PhotoGallery::insert([
            [
                'room_id' => $room->id ?? 1,
                'photo_url' => 'deluxe1.jpg',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'room_id' => $room->id ?? 1,
                'photo_url' => 'deluxe2.jpg',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'room_id' => $room->id ?? 1,
                'photo_url' => 'deluxe3.jpg',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }
}
