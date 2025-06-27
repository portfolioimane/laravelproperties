<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
  public function run()
{
    \App\Models\User::create([
        'name' => 'Admin',
        'email' => 'admin@example.com',
        'password' => bcrypt('password'),
        'role' => 'admin',
    ]);
    \App\Models\User::create([
        'name' => 'Customer',
        'email' => 'customer@example.com',
        'password' => bcrypt('password'),
        'role' => 'customer',
    ]);

        \App\Models\User::create([
            'name' => 'Owner One',
            'email' => 'owner1@example.com',
            'password' => bcrypt('password'),
            'role' => 'owner',
        ]);

        \App\Models\User::create([
            'name' => 'Owner Two',
            'email' => 'owner2@example.com',
            'password' => bcrypt('password'),
            'role' => 'owner',
        ]);

        \App\Models\User::create([
            'name' => 'Owner Three',
            'email' => 'owner3@example.com',
            'password' => bcrypt('password'),
            'role' => 'owner',
        ]);
    }
}



