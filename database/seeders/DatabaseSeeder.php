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

        $user = User::query()->firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                // Set a default password if user is created
                'password' => bcrypt('password'),
            ]
        );

        if ($freePlan && $user->plan_id !== $freePlan->id) {
            $user->plan()->associate($freePlan);
            $user->save();
        }
    }
}
