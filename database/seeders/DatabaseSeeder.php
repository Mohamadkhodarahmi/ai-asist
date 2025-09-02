<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Business;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Call the business seeder first
        $this->call(BusinessSeeder::class);

        // Get the created business
        $business = Business::first();

        // Create an admin user for the business
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@test.com',
            'password' => Hash::make('password'),
            'business_id' => $business->id
        ]);

        // Create some example users if needed
        // User::factory(10)->create();
    }
}
