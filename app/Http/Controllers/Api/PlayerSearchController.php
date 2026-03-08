<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PlayerSearch;
use App\Models\PlayerSearchResult;
use App\Models\PlayerProfileCard;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PlayerSearchController extends Controller
{
    // Oyuncu Araması Yap
    public function search(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'sport' => ['nullable', 'in:football,basketball,volleyball'],
            'position' => ['nullable', 'string', 'max:100'],
            'gender' => ['nullable', 'in:male,female,mixed'],
            'min_age' => ['nullable', 'integer', 'min:16', 'max:45'],
            'max_age' => ['nullable', 'integer', 'min:16', 'max:45'],
            'min_rating' => ['nullable', 'numeric', 'min:1', 'max:5'],
            'skill_levels' => ['nullable', 'array'],
            'locations' => ['nullable', 'array'],
            'save_search' => ['nullable', 'boolean'],
        ]);

        // Aramayı kaydet
        $search = PlayerSearch::create([
            'manager_id' => $request->user()->id,
            'sport' => $validated['sport'] ?? null,
            'position' => $validated['position'] ?? null,
            'gender' => $validated['gender'] ?? null,
            'min_age' => $validated['min_age'] ?? null,
            'max_age' => $validated['max_age'] ?? null,
            'skill_levels' => $validated['skill_levels'] ?? null,
            'locations' => $validated['locations'] ?? null,
            'min_rating' => $validated['min_rating'] ?? null,
            'is_saved' => $validated['save_search'] ?? false,
        ]);

        // Aramanın sonuçlarını hesapla
        $query = PlayerProfileCard::query();

        if ($validated['sport'] ?? null) {
            $query->where('sport', $validated['sport']);
        }

        if ($validated['position'] ?? null) {
            $query->where('position', $validated['position']);
        }

        if ($validated['min_age'] ?? null) {
            $query->where('age', '>=', $validated['min_age']);
        }

        if ($validated['max_age'] ?? null) {
            $query->where('age', '<=', $validated['max_age']);
        }

        if ($validated['min_rating'] ?? null) {
            $query->where('overall_rating', '>=', $validated['min_rating']);
        }

        $players = $query->get();

        // Eşleşme puanlarını hesapla
        foreach ($players as $player) {
            $matchScore = $this->calculateMatchScore($search, $player);

            PlayerSearchResult::create([
                'search_id' => $search->id,
                'player_id' => $player->user_id,
                'match_score' => $matchScore,
                'match_details' => $this->getMatchDetails($search, $player),
            ]);
        }

        $results = PlayerSearchResult::where('search_id', $search->id)
            ->orderByDesc('match_score')
            ->with('player')
            ->paginate(20);

        return response()->json([
            'ok' => true,
            'search_id' => $search->id,
            'total_results' => $results->total(),
            'data' => $results,
        ]);
    }

    // Kaydedilmiş Aramaları Getir
    public function getSavedSearches(Request $request): JsonResponse
    {
        $searches = PlayerSearch::where('manager_id', $request->user()->id)
            ->where('is_saved', true)
            ->with('results')
            ->latest()
            ->paginate(10);

        return response()->json([
            'ok' => true,
            'data' => $searches,
        ]);
    }

    // Aramanın Sonuçlarını Getir
    public function getSearchResults(int $searchId, Request $request): JsonResponse
    {
        $search = PlayerSearch::findOrFail($searchId);

        if ($search->manager_id !== $request->user()->id) {
            return response()->json([
                'ok' => false,
                'message' => 'Yetkiniz yok.',
            ], 403);
        }

        $results = PlayerSearchResult::where('search_id', $searchId)
            ->orderByDesc('match_score')
            ->with('player')
            ->paginate(20);

        return response()->json([
            'ok' => true,
            'data' => $results,
        ]);
    }

    // Eşleşme Puanını Hesapla
    private function calculateMatchScore(PlayerSearch $search, PlayerProfileCard $player): float
    {
        $score = 0;

        // Spor eşleşme (25 puan)
        if ($search->sport && $player->sport === $search->sport) {
            $score += 25;
        }

        // Pozisyon eşleşme (20 puan)
        if ($search->position && $player->position === $search->position) {
            $score += 20;
        }

        // Yaş eşleşme (20 puan)
        if ($search->min_age && $search->max_age) {
            if ($player->age >= $search->min_age && $player->age <= $search->max_age) {
                $score += 20;
            }
        }

        // Rating eşleşme (20 puan)
        if ($search->min_rating && $player->overall_rating >= $search->min_rating) {
            $score += 20;
        }

        // Cinsiyet eşleşme (15 puan)
        if ($search->gender && $player->gender === $search->gender) {
            $score += 15;
        }

        return min($score, 100);
    }

    // Eşleşme Detayları
    private function getMatchDetails(PlayerSearch $search, PlayerProfileCard $player): array
    {
        $details = [];

        if ($search->sport && $player->sport === $search->sport) {
            $details[] = 'Spor eşleşiyor';
        }

        if ($search->position && $player->position === $search->position) {
            $details[] = 'Pozisyon eşleşiyor';
        }

        if ($search->min_age && $search->max_age && $player->age >= $search->min_age && $player->age <= $search->max_age) {
            $details[] = 'Yaş aralığında';
        }

        if ($search->min_rating && $player->overall_rating >= $search->min_rating) {
            $details[] = 'Rating gerekli seviyede';
        }

        return $details;
    }
}
