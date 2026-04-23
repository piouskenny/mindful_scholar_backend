<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\School;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Admin User
        User::create([
            'name' => 'Admin User',
            'username' => 'admin',
            'email' => 'admin@mindfulscholar.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // Sample Schools
        School::create(['name' => 'University of Lagos', 'short_name' => 'UNILAG']);
        School::create(['name' => 'Covenant University', 'short_name' => 'CU']);
        School::create(['name' => 'Babcock University', 'short_name' => 'BU']);
    }
}
