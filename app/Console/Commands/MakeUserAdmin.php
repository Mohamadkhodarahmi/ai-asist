<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class MakeUserAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:make-admin {email? : The email of the user to make admin}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make a user an administrator';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $email = $this->argument('email');

        // If no email provided, ask for it
        if (! $email) {
            $email = $this->ask('Enter the email address of the user to make admin');
        }

        if (! $email) {
            $this->error('Email address is required.');

            return self::FAILURE;
        }

        // Find the user
        $user = User::where('email', $email)->first();

        if (! $user) {
            $this->error("User with email '{$email}' not found.");

            return self::FAILURE;
        }

        // Check if already admin
        if ($user->is_admin) {
            $this->info("User '{$user->name}' ({$user->email}) is already an administrator.");

            return self::SUCCESS;
        }

        // Make user admin
        $user->is_admin = true;
        $user->save();

        $this->info("✓ User '{$user->name}' ({$user->email}) has been granted administrator privileges.");

        return self::SUCCESS;
    }
}
