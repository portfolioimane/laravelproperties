<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Plan;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        Plan::insert([
            [
                'name' => 'free',
                'price' => 0,
                'max_properties' => 1,
                'duration_days' => null,
            ],
            [
                'name' => 'basic',
                'price' => 100,
                'max_properties' => 5,
                'duration_days' => 30,
            ],
            [
                'name' => 'premium',
                'price' => 250,
                'max_properties' => 20,
                'duration_days' => 365,
            ],
        ]);
    }
}
