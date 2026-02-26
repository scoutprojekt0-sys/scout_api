<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthRateLimitTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_is_rate_limited_after_ten_attempts(): void
    {
        $email = 'rate-register@example.com';
        $payload = [
            'name' => 'Rate User',
            'email' => $email,
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
            'role' => 'player',
            'city' => 'Istanbul',
        ];

        $response = $this->postJson('/api/auth/register', $payload);
        $response->assertStatus(201);

        for ($i = 0; $i < 9; $i++) {
            $retry = $this->postJson('/api/auth/register', $payload);
            $retry->assertStatus(422);
        }

        $blocked = $this->postJson('/api/auth/register', $payload);
        $blocked->assertStatus(429);
    }

    public function test_login_is_rate_limited_after_ten_failed_attempts(): void
    {
        for ($i = 0; $i < 10; $i++) {
            $response = $this->postJson('/api/auth/login', [
                'email' => 'does-not-exist@example.com',
                'password' => 'wrong-password',
            ]);

            $response->assertStatus(422);
        }

        $response = $this->postJson('/api/auth/login', [
            'email' => 'does-not-exist@example.com',
            'password' => 'wrong-password',
        ]);

        $response->assertStatus(429);
    }
}
