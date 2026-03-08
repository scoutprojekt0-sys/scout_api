<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SportsType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SportsController extends Controller
{
    public function listSports(): JsonResponse
    {
        $sports = SportsType::all();

        $formatted = $sports->map(function($sport) {
            return [
                'id' => $sport->id,
                'name' => $sport->name,
                'display_name' => $sport->display_name,
                'icon_url' => $sport->icon_url,
                'description' => $sport->description,
            ];
        });

        return response()->json([
            'ok' => true,
            'data' => $formatted,
        ]);
    }

    public function setSportPreference(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'preferred_sport' => ['required', 'in:football,basketball,volleyball'],
            'preferred_gender_to_play_with' => ['required', 'in:male,female,mixed,no_preference'],
            'comfortable_mixed_team' => ['boolean'],
        ]);

        $preference = \App\Models\GenderPreference::updateOrCreate(
            ['user_id' => $request->user()->id],
            $validated
        );

        return response()->json([
            'ok' => true,
            'message' => 'Spor tercihiniz kaydedildi.',
            'data' => $preference,
        ]);
    }

    public function getSportPreference(Request $request): JsonResponse
    {
        $preference = \App\Models\GenderPreference::where('user_id', $request->user()->id)->first();

        if (!$preference) {
            return response()->json([
                'ok' => false,
                'message' => 'Spor tercihi belirtilmemiş.',
            ], 404);
        }

        return response()->json([
            'ok' => true,
            'data' => $preference,
        ]);
    }

    public function filterBySource(Request $request): JsonResponse
    {
        $sport = $request->input('sport'); // football, basketball, volleyball
        $gender = $request->input('gender'); // male, female, mixed, all

        $validated = $request->validate([
            'sport' => ['required', 'in:football,basketball,volleyball'],
            'gender' => ['required', 'in:male,female,mixed,all'],
        ]);

        // Dinamik olarak takımları filtrele
        $teams = \App\Models\AmateurTeam::query()
            ->where('sport', $validated['sport'])
            ->when($validated['gender'] !== 'all', function($q) use ($validated) {
                if ($validated['gender'] === 'mixed') {
                    // Karma takımları göster
                    $q->where('team_gender', 'mixed');
                } else {
                    // Bay/Bayan takımlarını göster
                    $q->where('team_gender', $validated['gender']);
                }
            })
            ->with('manager')
            ->latest()
            ->paginate(20);

        return response()->json([
            'ok' => true,
            'filter' => $validated,
            'data' => $teams,
        ]);
    }
}
