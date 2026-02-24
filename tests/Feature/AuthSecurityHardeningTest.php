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

    public function test_sessions_endpoint_lists_user_tokens_and_marks_current(): void
    {
        $user = User::factory()->create(['role' => 'player']);
        $tokenOne = $user->createToken('device-1', $user->tokenAbilities())->plainTextToken;
        $user->createToken('device-2', $user->tokenAbilities());
        $currentTokenId = (int) explode('|', $tokenOne)[0];

        $response = $this
            ->withHeader('Authorization', 'Bearer '.$tokenOne)
            ->getJson('/api/auth/sessions')
            ->assertOk()
            ->assertJsonPath('ok', true);

        $sessions = $response->json('data');
        $this->assertCount(2, $sessions);
        $this->assertTrue(collect($sessions)->contains(fn ($s) => (int) $s['id'] === $currentTokenId && $s['is_current'] === true));
    }

    public function test_revoke_session_deletes_target_token(): void
    {
        $user = User::factory()->create(['role' => 'team']);
        $current = $user->createToken('current', $user->tokenAbilities())->plainTextToken;
        $other = $user->createToken('other', $user->tokenAbilities())->plainTextToken;
        $otherId = (int) explode('|', $other)[0];

        $this->withHeader('Authorization', 'Bearer '.$current)
            ->deleteJson('/api/auth/sessions/'.$otherId)
            ->assertOk()
            ->assertJsonPath('ok', true);

        $this->assertDatabaseMissing('personal_access_tokens', ['id' => $otherId]);
    }

    public function test_revoke_session_rejects_current_token_id(): void
    {
        $user = User::factory()->create(['role' => 'player']);
        $current = $user->createToken('current', $user->tokenAbilities())->plainTextToken;
        $currentId = (int) explode('|', $current)[0];

        $this->withHeader('Authorization', 'Bearer '.$current)
            ->deleteJson('/api/auth/sessions/'.$currentId)
            ->assertStatus(422)
            ->assertJsonPath('ok', false);
    }

    public function test_logout_all_revokes_other_tokens_only(): void
    {
        $user = User::factory()->create(['role' => 'coach']);
        $current = $user->createToken('current', $user->tokenAbilities())->plainTextToken;
        $otherOne = $user->createToken('other-1', $user->tokenAbilities())->plainTextToken;
        $otherTwo = $user->createToken('other-2', $user->tokenAbilities())->plainTextToken;
        $currentId = (int) explode('|', $current)[0];
        $otherOneId = (int) explode('|', $otherOne)[0];
        $otherTwoId = (int) explode('|', $otherTwo)[0];

        $this->withHeader('Authorization', 'Bearer '.$current)
            ->deleteJson('/api/auth/sessions')
            ->assertOk()
            ->assertJsonPath('data.revoked_count', 2);

        $this->assertDatabaseHas('personal_access_tokens', ['id' => $currentId]);
        $this->assertDatabaseMissing('personal_access_tokens', ['id' => $otherOneId]);
        $this->assertDatabaseMissing('personal_access_tokens', ['id' => $otherTwoId]);
    }

    public function test_refresh_rotates_current_token(): void
    {
        $user = User::factory()->create(['role' => 'manager']);
        $current = $user->createToken('current', $user->tokenAbilities())->plainTextToken;
        $currentId = (int) explode('|', $current)[0];

        $response = $this->withHeader('Authorization', 'Bearer '.$current)
            ->postJson('/api/auth/refresh')
            ->assertOk()
            ->assertJsonPath('ok', true);

        $newToken = (string) $response->json('data.token');
        $newTokenId = (int) explode('|', $newToken)[0];

        $this->assertNotEquals($currentId, $newTokenId);
        $this->assertDatabaseMissing('personal_access_tokens', ['id' => $currentId]);
        $this->assertDatabaseHas('personal_access_tokens', ['id' => $newTokenId]);
    }
}
