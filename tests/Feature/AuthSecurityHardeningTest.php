<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class AuthSecurityHardeningTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_is_locked_after_five_failed_attempts(): void
    {
        $this->withoutMiddleware(ThrottleRequests::class);

        User::factory()->create([
            'email' => 'lock@test.com',
            'password' => Hash::make('Password123'),
        ]);

        for ($i = 0; $i < 5; $i++) {
            $this->postJson('/api/auth/login', [
                'email' => 'lock@test.com',
                'password' => 'WrongPassword999',
            ])->assertStatus(422);
        }

        $this->postJson('/api/auth/login', [
            'email' => 'lock@test.com',
            'password' => 'WrongPassword999',
        ])->assertStatus(423)->assertJsonPath('ok', false);
    }

    public function test_role_change_revokes_existing_tokens(): void
    {
        $user = User::factory()->create(['role' => 'team']);
        $user->createToken('token-1', ['team']);
        $user->createToken('token-2', ['team']);

        $this->assertDatabaseCount('personal_access_tokens', 2);

        $user->update(['role' => 'scout']);

        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_security_command_revokes_only_legacy_wildcard_tokens(): void
    {
        $user = User::factory()->create(['role' => 'player']);
        $user->createToken('legacy-token');
        $user->createToken('new-token', $user->tokenAbilities());

        $this->assertDatabaseCount('personal_access_tokens', 2);

        $this->artisan('security:revoke-legacy-tokens')->assertExitCode(0);

        $this->assertDatabaseCount('personal_access_tokens', 1);

        $remainingAbilities = DB::table('personal_access_tokens')->value('abilities');
        $abilities = json_decode((string) $remainingAbilities, true);

        $this->assertIsArray($abilities);
        $this->assertFalse(in_array('*', $abilities, true));
    }

    public function test_login_returns_expiring_token_and_persists_expires_at(): void
    {
        Config::set('sanctum.expiration', 60);

        User::factory()->create([
            'email' => 'expires@test.com',
            'password' => Hash::make('Password123'),
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'expires@test.com',
            'password' => 'Password123',
        ])->assertOk();

        $response->assertJsonPath('data.expires_at', fn ($value) => is_string($value) && $value !== '');

        $latestExpiresAt = DB::table('personal_access_tokens')->max('expires_at');
        $this->assertNotNull($latestExpiresAt);
    }
}
