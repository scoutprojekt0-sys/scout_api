<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\UpdateMeRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    public function register(RegisterRequest $request): JsonResponse
    {
        $data = $request->validated();

        $user = User::create([
            'name' => $data['name'],
            'email' => strtolower($data['email']),
            'password' => Hash::make($data['password']),
            'role' => $data['role'],
            'city' => $data['city'] ?? null,
            'phone' => $data['phone'] ?? null,
        ]);

        [$token, $expiresAt] = $this->issueToken($user);

        return response()->json([
            'ok' => true,
            'message' => 'Kayit basarili.',
            'data' => [
                'token' => $token,
                'expires_at' => $expiresAt,
                'user' => $user,
            ],
        ], 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->validated();
        $email = strtolower($credentials['email']);
        $ip = (string) $request->ip();
        $lockKey = 'auth-lock:'.$email.'|'.$ip;
        $attemptKey = 'auth-attempt:'.$email.'|'.$ip;

        if (RateLimiter::tooManyAttempts($lockKey, 1)) {
            $seconds = RateLimiter::availableIn($lockKey);

            return response()->json([
                'ok' => false,
                'message' => 'Cok fazla hatali deneme. Lutfen daha sonra tekrar deneyin.',
                'retry_after' => $seconds,
            ], Response::HTTP_LOCKED);
        }

        /** @var User|null $user */
        $user = User::query()->where('email', $email)->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            RateLimiter::hit($attemptKey, 15 * 60);
            $attempts = RateLimiter::attempts($attemptKey);

            if ($attempts >= 5) {
                RateLimiter::hit($lockKey, 15 * 60);
                RateLimiter::clear($attemptKey);
            }

            Log::channel('security')->warning('Login failed', [
                'email' => $email,
                'ip' => $ip,
                'attempts' => $attempts,
                'locked' => $attempts >= 5,
            ]);

            throw ValidationException::withMessages([
                'email' => ['E-posta veya sifre hatali.'],
            ]);
        }

        RateLimiter::clear($attemptKey);
        RateLimiter::clear($lockKey);

        // Keep only latest 5 tokens for each user.
        $tokenCount = $user->tokens()->count();
        if ($tokenCount >= 5) {
            $user->tokens()->oldest()->limit($tokenCount - 4)->delete();
        }

        [$token, $expiresAt] = $this->issueToken($user);

        Log::channel('security')->info('Login success', [
            'user_id' => $user->id,
            'email' => $email,
            'ip' => $ip,
        ]);

        return response()->json([
            'ok' => true,
            'message' => 'Giris basarili.',
            'data' => [
                'token' => $token,
                'expires_at' => $expiresAt,
                'user' => $user,
            ],
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()?->currentAccessToken()?->delete();

        return response()->json([
            'ok' => true,
            'message' => 'Cikis yapildi.',
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'ok' => true,
            'data' => $request->user(),
        ]);
    }

    public function updateMe(UpdateMeRequest $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();
        $data = $request->validated();

        if (array_key_exists('email', $data)) {
            $data['email'] = strtolower($data['email']);
        }

        if (array_key_exists('password', $data)) {
            $data['password'] = Hash::make($data['password']);
            $user->tokens()->delete();
        }

        $user->fill($data);
        $user->save();

        return response()->json([
            'ok' => true,
            'message' => 'Profil guncellendi.',
            'data' => $user->fresh(),
        ]);
    }

    private function issueToken(User $user): array
    {
        $expirationMinutes = (int) config('sanctum.expiration');
        $expiresAt = $expirationMinutes > 0 ? now()->addMinutes($expirationMinutes) : null;

        $plainTextToken = $user->createToken('api-token', $user->tokenAbilities(), $expiresAt)->plainTextToken;

        return [
            $plainTextToken,
            $expiresAt instanceof Carbon ? $expiresAt->toISOString() : null,
        ];
    }
}
