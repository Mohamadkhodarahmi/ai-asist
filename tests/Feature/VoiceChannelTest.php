<?php

namespace Tests\Feature;

use App\Events\VoiceChannelJoined;
use App\Models\Group;
use App\Models\User;
use App\Models\VoiceChannel;
use App\Models\VoiceSession;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class VoiceChannelTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_voice_channel(): void
    {
        $user = User::factory()->create();
        $group = Group::factory()->create(['owner_id' => $user->id]);

        $response = $this->actingAs($user)
            ->post(route('groups.voice-channels.store', $group), [
                'name' => 'Test Voice Channel',
                'description' => 'Test description',
            ]);

        $this->assertDatabaseHas('voice_channels', [
            'group_id' => $group->id,
            'name' => 'Test Voice Channel',
            'description' => 'Test description',
        ]);
    }

    public function test_user_can_join_voice_channel(): void
    {
        $user = User::factory()->create();
        $group = Group::factory()->create();
        $group->addMember($user);

        $voiceChannel = VoiceChannel::factory()->create(['group_id' => $group->id]);

        Event::fake();

        $response = $this->actingAs($user)
            ->post(route('groups.voice-channels.join', [$group, $voiceChannel]));

        $this->assertDatabaseHas('voice_sessions', [
            'voice_channel_id' => $voiceChannel->id,
            'user_id' => $user->id,
        ]);

        Event::assertDispatched(VoiceChannelJoined::class);
    }

    public function test_user_can_leave_voice_channel(): void
    {
        $user = User::factory()->create();
        $group = Group::factory()->create();
        $group->addMember($user);

        $voiceChannel = VoiceChannel::factory()->create(['group_id' => $group->id]);
        $session = VoiceSession::factory()->create([
            'voice_channel_id' => $voiceChannel->id,
            'user_id' => $user->id,
            'joined_at' => now(),
        ]);

        $response = $this->actingAs($user)
            ->delete(route('groups.voice-channels.leave', [$group, $voiceChannel]));

        $session->refresh();
        $this->assertNotNull($session->left_at);
    }

    public function test_voice_channel_participant_count(): void
    {
        $group = Group::factory()->create();
        $voiceChannel = VoiceChannel::factory()->create(['group_id' => $group->id]);

        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $group->addMember($user1);
        $group->addMember($user2);

        // Create active sessions
        VoiceSession::factory()->create([
            'voice_channel_id' => $voiceChannel->id,
            'user_id' => $user1->id,
            'joined_at' => now(),
        ]);

        VoiceSession::factory()->create([
            'voice_channel_id' => $voiceChannel->id,
            'user_id' => $user2->id,
            'joined_at' => now(),
        ]);

        $this->assertEquals(2, $voiceChannel->participants_count);
    }

    public function test_voice_channel_is_full(): void
    {
        $group = Group::factory()->create();
        $voiceChannel = VoiceChannel::factory()->create([
            'group_id' => $group->id,
            'max_participants' => 2,
        ]);

        $this->assertFalse($voiceChannel->isFull());

        // Add participants up to the limit
        $users = User::factory()->count(2)->create();
        foreach ($users as $user) {
            $group->addMember($user);
            VoiceSession::factory()->create([
                'voice_channel_id' => $voiceChannel->id,
                'user_id' => $user->id,
                'joined_at' => now(),
            ]);
        }

        $voiceChannel->refresh();
        $this->assertTrue($voiceChannel->isFull());
    }
}
