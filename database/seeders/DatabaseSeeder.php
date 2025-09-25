<?php

namespace Database\Seeders;

use App\Models\User;
use Database\Seeders\PlanSeeder;
use App\Models\Plan;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed core plans
        $this->call(PlanSeeder::class);

        $freePlan = Plan::query()->where('slug', 'free')->first();

        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        if ($freePlan) {
            $user->plan()->associate($freePlan);
            $user->save();
        }
    }
}
