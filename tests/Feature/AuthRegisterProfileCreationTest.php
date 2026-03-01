<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthRegisterProfileCreationTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_creates_player_profile(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => 'Player One',
            'email' => 'player@example.com',
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
            'role' => 'player',
        ]);

        $response->assertCreated()->assertJsonPath('ok', true);
        $userId = (int) $response->json('data.user.id');

        $this->assertDatabaseHas('player_profiles', ['user_id' => $userId]);
    }

    public function test_register_creates_team_profile_with_default_team_name(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => 'Team Alpha',
            'email' => 'team@example.com',
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
            'role' => 'team',
        ]);

        $response->assertCreated()->assertJsonPath('ok', true);
        $userId = (int) $response->json('data.user.id');

        $this->assertDatabaseHas('team_profiles', [
            'user_id' => $userId,
            'team_name' => 'Team Alpha',
        ]);
    }

    public function test_register_creates_staff_profile_for_staff_roles(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => 'Scout User',
            'email' => 'scout@example.com',
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
            'role' => 'scout',
        ]);

        $response->assertCreated()->assertJsonPath('ok', true);
        $userId = (int) $response->json('data.user.id');
        $role = (string) $response->json('data.user.role');

        $this->assertDatabaseHas('staff_profiles', [
            'user_id' => $userId,
            'role_type' => $role,
        ]);
    }

    public function test_register_rejects_duplicate_email_case_insensitive(): void
    {
        $first = $this->postJson('/api/auth/register', [
            'name' => 'User One',
            'email' => 'CaseMail@Example.com',
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
            'role' => 'player',
        ]);

        $first->assertCreated();

        $second = $this->postJson('/api/auth/register', [
            'name' => 'User Two',
            'email' => 'casemail@example.com',
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
            'role' => 'player',
        ]);

        $second->assertStatus(422);
        $second->assertJsonValidationErrors(['email']);
    }
}
