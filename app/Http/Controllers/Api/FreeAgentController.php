<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FreeAgentListing;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FreeAgentController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = FreeAgentListing::query()
            ->where('status', 'active')
            ->with(['player.playerProfile']);

        // Filtreleme
        if ($request->has('city')) {
            $query->where('city', $request->input('city'));
        }

        if ($request->has('district')) {
            $query->where('district', $request->input('district'));
        }

        if ($request->has('position')) {
            $position = $request->input('position');
            $query->whereJsonContains('preferred_positions', $position);
        }

        if ($request->has('skill_level')) {
            $query->where('skill_level', $request->input('skill_level'));
        }

        if ($request->has('availability')) {
            $query->where('availability', $request->input('availability'));
        }

        if ($request->has('max_fee')) {
            $query->where('max_monthly_fee', '<=', $request->input('max_fee'));
        }

        if ($request->has('available_day')) {
            $day = $request->input('available_day');
            $query->whereJsonContains('available_days', $day);
        }

        $listings = $query->latest()->paginate(20);

        return response()->json([
            'ok' => true,
            'data' => $listings,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        // Aktif ilan var mı kontrol et
        $existing = FreeAgentListing::where('player_user_id', $request->user()->id)
            ->where('status', 'active')
            ->exists();

        if ($existing) {
            return response()->json([
                'ok' => false,
                'message' => 'Zaten aktif bir ilanınız var. Önce onu kapatın.',
            ], 400);
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:160'],
            'preferred_positions' => ['required', 'array', 'min:1'],
            'city' => ['required', 'string', 'max:80'],
            'district' => ['nullable', 'string', 'max:80'],
            'availability' => ['required', 'in:immediately,next_season,flexible'],
            'available_days' => ['nullable', 'array'],
            'available_time' => ['nullable', 'string', 'max:50'],
            'skill_level' => ['required', 'in:beginner,intermediate,advanced'],
            'max_monthly_fee' => ['nullable', 'numeric', 'min:0'],
            'has_equipment' => ['boolean'],
            'has_transportation' => ['boolean'],
            'about' => ['nullable', 'string', 'max:1000'],
            'experience' => ['nullable', 'string', 'max:2000'],
        ]);

        $listing = FreeAgentListing::create([
            'player_user_id' => $request->user()->id,
            ...$validated,
        ]);

        return response()->json([
            'ok' => true,
            'message' => 'Serbest oyuncu ilanı oluşturuldu.',
            'data' => $listing,
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $listing = FreeAgentListing::with(['player.playerProfile', 'player.media'])
            ->findOrFail($id);

        return response()->json([
            'ok' => true,
            'data' => $listing,
        ]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $listing = FreeAgentListing::findOrFail($id);

        if ($listing->player_user_id !== $request->user()->id) {
            return response()->json([
                'ok' => false,
                'message' => 'Bu ilanı düzenleme yetkiniz yok.',
            ], 403);
        }

        $validated = $request->validate([
            'title' => ['sometimes', 'string', 'max:160'],
            'preferred_positions' => ['sometimes', 'array', 'min:1'],
            'availability' => ['sometimes', 'in:immediately,next_season,flexible'],
            'available_days' => ['nullable', 'array'],
            'available_time' => ['nullable', 'string', 'max:50'],
            'skill_level' => ['sometimes', 'in:beginner,intermediate,advanced'],
            'max_monthly_fee' => ['nullable', 'numeric', 'min:0'],
            'has_equipment' => ['boolean'],
            'has_transportation' => ['boolean'],
            'about' => ['nullable', 'string', 'max:1000'],
            'experience' => ['nullable', 'string', 'max:2000'],
            'status' => ['sometimes', 'in:active,found_team,inactive'],
        ]);

        $listing->update($validated);

        return response()->json([
            'ok' => true,
            'message' => 'İlan güncellendi.',
            'data' => $listing,
        ]);
    }

    public function myListing(Request $request): JsonResponse
    {
        $listing = FreeAgentListing::where('player_user_id', $request->user()->id)
            ->latest()
            ->first();

        return response()->json([
            'ok' => true,
            'data' => $listing,
        ]);
    }
}
