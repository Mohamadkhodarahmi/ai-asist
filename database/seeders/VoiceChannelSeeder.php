<?php

namespace Database\Seeders;

use App\Models\Group;
use App\Models\VoiceChannel;
use Illuminate\Database\Seeder;

class VoiceChannelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a default "General" voice channel for existing groups
        $groups = Group::whereDoesntHave('voiceChannels')->get();

        foreach ($groups as $group) {
            VoiceChannel::create([
                'group_id' => $group->id,
                'name' => 'General',
                'description' => 'General voice channel for group discussions',
                'is_active' => true,
                'max_participants' => 10,
            ]);
        }

        $this->command->info('Created voice channels for '.$groups->count().' groups.');
    }
}
