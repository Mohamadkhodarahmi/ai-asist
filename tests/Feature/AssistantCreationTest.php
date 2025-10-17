<?php

namespace Tests\Feature;

use App\Models\Business;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AssistantCreationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_assistant_with_name_and_file(): void
    {
        Storage::fake('local');

        $user = User::factory()->create();
        $file = UploadedFile::fake()->create('test.pdf', 1000, 'application/pdf');

        $response = $this->actingAs($user)->post('/business', [
            'name' => 'My Test Assistant',
            'document' => $file,
        ]);

        $response->assertRedirect('/chat');
        $response->assertSessionHas('status', 'Assistant created successfully!');

        // Check that business was created
        $this->assertDatabaseHas('businesses', [
            'name' => 'My Test Assistant',
        ]);

        // Check that user is associated with business
        $user->refresh();
        $this->assertNotNull($user->business_id);

        // Check that knowledge file was created
        $business = $user->business;
        $this->assertCount(1, $business->knowledgeFiles);
        
        $knowledgeFile = $business->knowledgeFiles->first();
        $this->assertEquals('test.pdf', $knowledgeFile->original_name);
        $this->assertEquals('pending', $knowledgeFile->status);
    }

    public function test_user_cannot_create_assistant_without_name(): void
    {
        $user = User::factory()->create();
        $file = UploadedFile::fake()->create('test.pdf', 1000, 'application/pdf');

        $response = $this->actingAs($user)->post('/business', [
            'name' => '',
            'document' => $file,
        ]);

        $response->assertSessionHasErrors(['name']);
    }

    public function test_user_cannot_create_assistant_without_file(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/business', [
            'name' => 'My Test Assistant',
        ]);

        $response->assertSessionHasErrors(['document']);
    }

    public function test_user_cannot_create_assistant_with_invalid_file_type(): void
    {
        $user = User::factory()->create();
        $file = UploadedFile::fake()->create('test.txt', 1000, 'text/plain');

        $response = $this->actingAs($user)->post('/business', [
            'name' => 'My Test Assistant',
            'document' => $file,
        ]);

        $response->assertSessionHasErrors(['document']);
    }

    public function test_user_cannot_create_assistant_with_file_too_large(): void
    {
        $user = User::factory()->create();
        $file = UploadedFile::fake()->create('test.pdf', 15000, 'application/pdf'); // 15MB

        $response = $this->actingAs($user)->post('/business', [
            'name' => 'My Test Assistant',
            'document' => $file,
        ]);

        $response->assertSessionHasErrors(['document']);
    }

    public function test_unauthenticated_user_cannot_create_assistant(): void
    {
        $file = UploadedFile::fake()->create('test.pdf', 1000, 'application/pdf');

        $response = $this->post('/business', [
            'name' => 'My Test Assistant',
            'document' => $file,
        ]);

        $response->assertRedirect('/login');
    }
}