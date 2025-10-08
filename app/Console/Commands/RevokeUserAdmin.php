<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class RevokeUserAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:revoke-admin {email? : The email of the user to revoke admin access}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Revoke administrator privileges from a user';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $email = $this->argument('email');

        // If no email provided, ask for it
        if (! $email) {
            $email = $this->ask('Enter the email address of the user to revoke admin access');
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

        // Check if not admin
        if (! $user->is_admin) {
            $this->info("User '{$user->name}' ({$user->email}) is not an administrator.");

            return self::SUCCESS;
        }

        // Revoke admin
        $user->is_admin = false;
        $user->save();

        $this->info("✓ Administrator privileges have been revoked from '{$user->name}' ({$user->email}).");

        return self::SUCCESS;
    }
}
