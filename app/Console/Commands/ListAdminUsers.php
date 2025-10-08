<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class ListAdminUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:list-admins';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all users with administrator privileges';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $admins = User::where('is_admin', true)
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'created_at']);

        if ($admins->isEmpty()) {
            $this->info('No administrators found.');

            return self::SUCCESS;
        }

        $this->info("Found {$admins->count()} administrator(s):");
        $this->newLine();

        $tableData = $admins->map(function ($admin) {
            return [
                'ID' => $admin->id,
                'Name' => $admin->name,
                'Email' => $admin->email,
                'Member Since' => $admin->created_at->format('Y-m-d'),
            ];
        })->toArray();

        $this->table(
            ['ID', 'Name', 'Email', 'Member Since'],
            $tableData
        );

        return self::SUCCESS;
    }
}
