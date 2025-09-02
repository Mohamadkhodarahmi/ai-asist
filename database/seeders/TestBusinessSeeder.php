<?php

namespace Database\Seeders;

use App\Models\Business;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TestBusinessSeeder extends Seeder
{
    public function run(): void
    {
        $business = Business::create([
            'name' => 'Test Business',
            'email' => 'test@business.com',
        ]);

        User::create([
            'name' => 'Test Admin',
            'email' => 'admin@test.com',
            'password' => Hash::make('password'),
            'business_id' => $business->id,
        ]);
    }
}