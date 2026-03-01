<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\UpdateMeRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(RegisterRequest $request): JsonResponse
    {
        $data = $request->validated();

        /** @var User $user */
        $user = DB::transaction(function () use ($data): User {
            $user = User::create([
                'name' => $data['name'],
                'email' => strtolower($data['email']),
                'password' => Hash::make($data['password']),
                'role' => $data['role'],
                'city' => $data['city'] ?? null,
                'phone' => $data['phone'] ?? null,
            ]);

            $this->createRoleProfile($user);

            return $user;
        });

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'ok' => true,
            'message' => 'Kayit basarili.',
            'data' => [
                'token' => $token,
                'user' => $user,
            ],
        ], 201);
    }

    private function createRoleProfile(User $user): void
    {
        if ($user->role === 'player') {
            DB::table('player_profiles')->insert([
                'user_id' => (int) $user->id,
                'updated_at' => now(),
            ]);

            return;
        }

        if ($user->role === 'team') {
            DB::table('team_profiles')->insert([
                'user_id' => (int) $user->id,
                'team_name' => (string) $user->name,
                'updated_at' => now(),
            ]);

            return;
        }

        if (in_array($user->role, ['manager', 'coach', 'scout'], true)) {
            DB::table('staff_profiles')->insert([
                'user_id' => (int) $user->id,
                'role_type' => $user->role,
                'updated_at' => now(),
            ]);
        }
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->validated();
        $email = strtolower($credentials['email']);

        /** @var User|null $user */
        $user = User::query()->where('email', $email)->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['E-posta veya sifre hatali.'],
            ]);
        }

        // Keep only latest 5 tokens for each user.
        $tokenCount = $user->tokens()->count();
        if ($tokenCount >= 5) {
            $user->tokens()->oldest()->limit($tokenCount - 4)->delete();
        }

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'ok' => true,
            'message' => 'Giris basarili.',
            'data' => [
                'token' => $token,
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
}
