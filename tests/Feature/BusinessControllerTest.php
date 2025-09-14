<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BusinessControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_create_business()
    {
        $user = User::factory()->create(['business_id' => null]);
        
        $response = $this->actingAs($user)
            ->postJson('/api/v1/businesses', [
                'name' => 'Test Business'
            ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'id',
                    'name',
                    'created_at',
                    'updated_at'
                ]
            ]);

        $this->assertDatabaseHas('businesses', [
            'name' => 'Test Business'
        ]);

        $this->assertEquals(
            $response->json('data.id'),
            $user->fresh()->business_id
        );
    }

    public function test_unauthenticated_user_cannot_create_business()
    {
        $response = $this->postJson('/api/v1/businesses', [
            'name' => 'Test Business'
        ]);

        $response->assertStatus(401);
    }

    public function test_business_name_is_required()
    {
        $user = User::factory()->create(['business_id' => null]);
        
        $response = $this->actingAs($user)
            ->postJson('/api/v1/businesses', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }
}