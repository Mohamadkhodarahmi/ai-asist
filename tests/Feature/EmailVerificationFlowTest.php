<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class EmailVerificationFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_email_verification_page(): void
    {
        $user = User::factory()->unverified()->create();

        $response = $this->actingAs($user)->get('/email/verify');

        $response->assertStatus(200);
        $response->assertViewIs('auth.verify-email');
    }

    public function test_user_can_view_email_correction_page(): void
    {
        $user = User::factory()->unverified()->create();

        $response = $this->actingAs($user)->get('/email/correct');

        $response->assertStatus(200);
        $response->assertViewIs('auth.correct-email');
    }

    public function test_user_can_update_email_address(): void
    {
        $user = User::factory()->unverified()->create([
            'password' => Hash::make('password'),
        ]);

        $newEmail = 'newemail@example.com';

        $response = $this->actingAs($user)->post('/email/update', [
            'email' => $newEmail,
            'password' => 'password',
        ]);

        $response->assertRedirect('/email/verify');
        $response->assertSessionHas('status', 'email-updated');
        $response->assertSessionHas('old_email', $user->email);
        $response->assertSessionHas('new_email', $newEmail);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'email' => $newEmail,
            'email_verified_at' => null,
        ]);
    }

    public function test_user_cannot_update_email_without_password(): void
    {
        $user = User::factory()->unverified()->create([
            'password' => Hash::make('password'),
        ]);

        $response = $this->actingAs($user)->post('/email/update', [
            'email' => 'newemail@example.com',
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors(['password']);
    }

    public function test_user_cannot_update_email_to_existing_email(): void
    {
        $existingUser = User::factory()->create();
        $user = User::factory()->unverified()->create([
            'password' => Hash::make('password'),
        ]);

        $response = $this->actingAs($user)->post('/email/update', [
            'email' => $existingUser->email,
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors(['email']);
    }

    public function test_user_can_view_delete_account_page(): void
    {
        $user = User::factory()->unverified()->create();

        $response = $this->actingAs($user)->get('/email/delete-account');

        $response->assertStatus(200);
        $response->assertViewIs('auth.delete-account');
    }

    public function test_user_can_delete_account_and_redirect_to_registration(): void
    {
        $user = User::factory()->unverified()->create([
            'password' => Hash::make('password'),
            'email' => 'test@example.com',
        ]);

        $response = $this->actingAs($user)->post('/email/delete-account', [
            'password' => 'password',
            'confirm_delete' => '1',
        ]);

        $response->assertRedirect('/register');
        $response->assertSessionHas('status', 'account-deleted');
        $response->assertSessionHas('email', 'test@example.com');

        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
        ]);
    }

    public function test_user_cannot_delete_account_without_confirmation(): void
    {
        $user = User::factory()->unverified()->create([
            'password' => Hash::make('password'),
        ]);

        $response = $this->actingAs($user)->post('/email/delete-account', [
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors(['confirm_delete']);
    }

    public function test_user_cannot_delete_account_with_wrong_password(): void
    {
        $user = User::factory()->unverified()->create([
            'password' => Hash::make('password'),
        ]);

        $response = $this->actingAs($user)->post('/email/delete-account', [
            'password' => 'wrong-password',
            'confirm_delete' => '1',
        ]);

        $response->assertSessionHasErrors(['password']);
    }

    public function test_unauthenticated_user_cannot_access_email_correction(): void
    {
        $response = $this->get('/email/correct');

        $response->assertRedirect('/login');
    }

    public function test_unauthenticated_user_cannot_access_delete_account(): void
    {
        $response = $this->get('/email/delete-account');

        $response->assertRedirect('/login');
    }
}