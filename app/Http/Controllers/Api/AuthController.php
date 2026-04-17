<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\UpdateMeRequest;
use App\Mail\EmailVerificationMail;
use App\Models\PlayerProfile;
use App\Models\StaffProfile;
use App\Models\TeamProfile;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
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

        $user = DB::transaction(function () use ($data, $normalizedEmail): User {
            $createdUser = User::create([
                'name' => $data['name'],
                'email' => $normalizedEmail,
                'password' => Hash::make($data['password']),
                'role' => $data['role'],
                'city' => $data['city'] ?? null,
                'phone' => $data['phone'] ?? null,
                'is_verified' => false,
                'email_verification_token' => Str::random(64),
            ]);

            $this->createDefaultProfileForRole($createdUser);

            return $createdUser;
        });

        $verificationLink = $this->buildEmailVerificationLink((string) $user->email_verification_token);

        $this->sendVerificationEmail($user, $verificationLink);

        return response()->json([
            'ok' => true,
            'message' => 'Kayit alindi. E-posta dogrulamasi bekleniyor.',
            'data' => [
                'user' => $user,
                'verification_required' => true,
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

        if (!$this->isUserVerified($user)) {
            throw ValidationException::withMessages([
                'email' => ['Hesap dogrulanmadi. E-postadaki linke tiklayip tekrar giris yapin.'],
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

    public function verifyEmail(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'token' => ['required', 'string', 'min:16'],
        ]);

        /** @var User|null $user */
        $user = User::query()
            ->where('email_verification_token', $validated['token'])
            ->first();

        if (!$user) {
            return response()->json([
                'ok' => false,
                'message' => 'Dogrulama linki gecersiz veya suresi dolmus.',
            ], 422);
        }

        $user->forceFill([
            'email_verified_at' => now(),
            'is_verified' => true,
            'email_verification_token' => null,
        ])->save();

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'ok' => true,
            'message' => 'E-posta dogrulandi. Hesap otomatik onaylandi.',
            'data' => [
                'token' => $token,
                'user' => $user->fresh(),
            ],
        ]);
    }

    public function resendVerification(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
        ]);

        $email = strtolower(trim((string) $validated['email']));
        /** @var User|null $user */
        $user = User::query()->whereRaw('LOWER(email) = ?', [$email])->first();

        if (!$user) {
            return response()->json([
                'ok' => true,
                'message' => 'Eger hesap varsa dogrulama linki tekrar gonderilir.',
            ]);
        }

        if ($this->isUserVerified($user)) {
            return response()->json([
                'ok' => true,
                'message' => 'Bu hesap zaten dogrulanmis.',
            ]);
        }

        $user->forceFill([
            'email_verification_token' => $user->email_verification_token ?: Str::random(64),
        ])->save();

        $verificationLink = $this->buildEmailVerificationLink((string) $user->email_verification_token);

        $this->sendVerificationEmail($user, $verificationLink);

        return response()->json([
            'ok' => true,
            'message' => 'Dogrulama e-postasi tekrar gonderildi.',
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
            ->select(['id', 'name', 'email', 'role', 'city', 'phone', 'is_verified', 'email_verified_at', 'created_at'])
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

    private function createDefaultProfileForRole(User $user): void
    {
        if ($user->role === 'player') {
            PlayerProfile::firstOrCreate(['user_id' => $user->id]);
            return;
        }

        if ($user->role === 'team') {
            TeamProfile::firstOrCreate(
                ['user_id' => $user->id],
                ['team_name' => $user->name]
            );
            return;
        }

        if (in_array($user->role, ['manager', 'coach', 'scout'], true)) {
            StaffProfile::firstOrCreate(
                ['user_id' => $user->id],
                ['role_type' => $user->role]
            );
        }
    }

    private function isUserVerified(User $user): bool
    {
        return (bool) $user->is_verified || !empty($user->email_verified_at);
    }

    private function buildEmailVerificationLink(string $token): string
    {
        $frontendBase = rtrim((string) config('app.frontend_url', config('app.url')), '/');
        if ($frontendBase === '') {
            $frontendBase = 'http://localhost:5500';
        }

        return $frontendBase . '/index.html?verify_email_token=' . urlencode($token);
    }

    private function sendVerificationEmail(User $user, string $verificationLink): void
    {
        try {
            Mail::to($user->email)->send(new EmailVerificationMail($user, $verificationLink));
        } catch (\Throwable $e) {
            Log::error('Verification email could not be sent.', [
                'user_id' => $user->id,
                'email' => $user->email,
                'error' => $e->getMessage(),
            ]);

            throw ValidationException::withMessages([
                'email' => ['Dogrulama e-postasi gonderilemedi. Mail ayarlarinizi kontrol edin.'],
            ]);
        }
    }
}
