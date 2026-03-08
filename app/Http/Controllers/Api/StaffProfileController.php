<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CoachProfileCard;
use App\Models\ManagerProfileCard;
use App\Models\StaffProfile;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StaffProfileController extends Controller
{
    public function me(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();
        return response()->json([
            'ok' => true,
            'data' => $this->buildProfilePayload($user),
        ]);
    }

    public function updateMe(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        if (!in_array($user->role, ['manager', 'scout', 'coach'], true)) {
            return response()->json([
                'ok' => false,
                'message' => 'Bu profil tipi desteklenmiyor.',
            ], 422);
        }

        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'min:2', 'max:120'],
            'city' => ['sometimes', 'nullable', 'string', 'max:80'],
            'phone' => ['sometimes', 'nullable', 'string', 'max:30'],
            'organization' => ['sometimes', 'nullable', 'string', 'max:140'],
            'experience_years' => ['sometimes', 'nullable', 'integer', 'min:0', 'max:80'],
            'bio' => ['sometimes', 'nullable', 'string', 'max:5000'],
            'country' => ['sometimes', 'nullable', 'string', 'max:80'],
            'branch' => ['sometimes', 'nullable', 'string', 'max:60'],
            'profile_photo_url' => ['sometimes', 'nullable', 'string', 'max:4000'],
        ]);

        if (array_key_exists('name', $validated)) {
            $user->name = $validated['name'];
        }
        if (array_key_exists('city', $validated)) {
            $user->city = $validated['city'];
        }
        if (array_key_exists('phone', $validated)) {
            $user->phone = $validated['phone'];
        }
        $user->save();

        $staffProfile = StaffProfile::firstOrNew(['user_id' => $user->id]);
        $staffProfile->role_type = $staffProfile->role_type ?: $user->role;
        if (array_key_exists('organization', $validated)) {
            $staffProfile->organization = $validated['organization'];
        }
        if (array_key_exists('experience_years', $validated)) {
            $staffProfile->experience_years = $validated['experience_years'];
        }

        $existingBio = $this->decodeMeta((string) ($staffProfile->bio ?? ''));
        $meta = array_merge($existingBio, [
            'bio' => array_key_exists('bio', $validated) ? $validated['bio'] : ($existingBio['bio'] ?? ''),
            'country' => array_key_exists('country', $validated) ? $validated['country'] : ($existingBio['country'] ?? ''),
            'branch' => array_key_exists('branch', $validated) ? $validated['branch'] : ($existingBio['branch'] ?? ''),
            'profile_photo_url' => array_key_exists('profile_photo_url', $validated) ? $validated['profile_photo_url'] : ($existingBio['profile_photo_url'] ?? ''),
        ]);
        $staffProfile->bio = json_encode($meta, JSON_UNESCAPED_UNICODE);
        $staffProfile->save();

        $this->syncProfileCard($user, $staffProfile, $meta);

        return response()->json([
            'ok' => true,
            'message' => 'Profil kaydedildi.',
            'data' => $this->buildProfilePayload($user->fresh()),
        ]);
    }

    public function publicProfile(int $userId): JsonResponse
    {
        $user = User::query()->where('id', $userId)->whereIn('role', ['manager', 'scout', 'coach'])->first();
        if (!$user) {
            return response()->json([
                'ok' => false,
                'message' => 'Profil bulunamadi.',
            ], 404);
        }

        return response()->json([
            'ok' => true,
            'data' => $this->buildProfilePayload($user),
        ]);
    }

    private function buildProfilePayload(User $user): array
    {
        $staffProfile = StaffProfile::query()->where('user_id', $user->id)->first();
        $meta = $this->decodeMeta((string) ($staffProfile->bio ?? ''));

        $roleType = $staffProfile->role_type ?: $user->role;
        $branch = (string) ($meta['branch'] ?? '');
        $country = (string) ($meta['country'] ?? '');
        $photo = (string) ($meta['profile_photo_url'] ?? '');

        if ($roleType === 'manager') {
            $card = ManagerProfileCard::query()->where('user_id', $user->id)->first();
            $branch = $branch !== '' ? $branch : (string) ($card->specialization ?? '');
            $photo = $photo !== '' ? $photo : (string) ($card->profile_photo_url ?? '');
            $social = is_array($card?->social_links) ? $card->social_links : [];
            $country = $country !== '' ? $country : (string) ($social['country'] ?? '');
        } else {
            $card = CoachProfileCard::query()->where('user_id', $user->id)->first();
            $branch = $branch !== '' ? $branch : (string) ($card->primary_sport ?? '');
            $photo = $photo !== '' ? $photo : (string) ($card->profile_photo_url ?? '');
            $social = is_array($card?->social_links) ? $card->social_links : [];
            $country = $country !== '' ? $country : (string) ($social['country'] ?? '');
        }

        return [
            'user_id' => $user->id,
            'role' => $roleType,
            'name' => $user->name,
            'city' => $user->city,
            'phone' => $user->phone,
            'organization' => $staffProfile->organization ?? '',
            'experience_years' => $staffProfile->experience_years,
            'bio' => (string) ($meta['bio'] ?? ''),
            'country' => $country,
            'branch' => $branch,
            'profile_photo_url' => $photo,
        ];
    }

    private function syncProfileCard(User $user, StaffProfile $staffProfile, array $meta): void
    {
        $photo = (string) ($meta['profile_photo_url'] ?? '');
        $branch = (string) ($meta['branch'] ?? '');
        $country = (string) ($meta['country'] ?? '');

        if ($user->role === 'manager') {
            $card = ManagerProfileCard::firstOrNew(['user_id' => $user->id]);
            $card->full_name = $user->name;
            $card->specialization = $branch ?: $card->specialization;
            $card->years_experience = $staffProfile->experience_years;
            if ($photo !== '') {
                $card->profile_photo_url = $photo;
            }
            $social = is_array($card->social_links) ? $card->social_links : [];
            if ($country !== '') {
                $social['country'] = $country;
            }
            $card->social_links = $social;
            $card->save();
            return;
        }

        $card = CoachProfileCard::firstOrNew(['user_id' => $user->id]);
        $card->full_name = $user->name;
        $card->primary_sport = $branch ?: $card->primary_sport;
        $card->years_experience = $staffProfile->experience_years;
        if ($photo !== '') {
            $card->profile_photo_url = $photo;
        }
        $social = is_array($card->social_links) ? $card->social_links : [];
        if ($country !== '') {
            $social['country'] = $country;
        }
        $card->social_links = $social;
        $card->save();
    }

    private function decodeMeta(string $raw): array
    {
        if ($raw === '') {
            return [];
        }

        $decoded = json_decode($raw, true);
        if (is_array($decoded)) {
            return $decoded;
        }

        return [
            'bio' => $raw,
        ];
    }
}
