<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\UpdateMeRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(RegisterRequest $request): JsonResponse
    {
        $data = $request->validated();
        $normalizedEmail = strtolower($data['email']);

        $emailExists = User::query()
            ->whereRaw('LOWER(email) = ?', [$normalizedEmail])
            ->exists();

        if ($emailExists) {
            throw ValidationException::withMessages([
                'email' => ['Bu e-posta zaten kullaniliyor.'],
            ]);
        }

        $user = User::create([
            'name' => $data['name'],
            'email' => $normalizedEmail,
            'password' => Hash::make($data['password']),
            'role' => $data['role'],
            'city' => $data['city'] ?? null,
            'phone' => $data['phone'] ?? null,
        ]);

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

    public function users(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'role' => ['nullable', Rule::in(['player', 'manager', 'coach', 'scout', 'team', 'admin'])],
            'search' => ['nullable', 'string', 'max:120'],
            'q' => ['nullable', 'string', 'max:120'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:200'],
        ]);

        $role = $validated['role'] ?? null;
        $search = trim((string)($validated['search'] ?? $validated['q'] ?? ''));
        $perPage = (int)($validated['per_page'] ?? 20);

        $query = User::query()
            ->select(['id', 'name', 'email', 'role', 'city', 'phone', 'created_at'])
            ->orderByDesc('created_at');

        if ($role) {
            $query->where('role', $role);
        }

        if ($search !== '') {
            $query->where(function ($inner) use ($search) {
                $inner->where('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        $users = $query->paginate($perPage);

        return response()->json([
            'ok' => true,
            'data' => $users,
        ]);
    }
}
