<?php

namespace App\Console\Commands;

use App\Models\Business;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateTestUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:create-test {--count=1 : Number of users to create}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create test users with businesses for testing group features';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $count = (int) $this->option('count');

        for ($i = 1; $i <= $count; $i++) {
            $name = 'Test User '.$i;
            $email = 'test'.uniqid().'@example.com';

            // Create business first
            $business = Business::create([
                'name' => $name."'s Business",
            ]);

            // Create user
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make('password'),
                'business_id' => $business->id,
            ]);

            $this->info("✓ Created user: {$user->name} ({$user->email})");
        }

        $this->newLine();
        $this->info("Created {$count} test user(s) successfully!");
        $this->info("Default password: password");

        return Command::SUCCESS;
    }
}
