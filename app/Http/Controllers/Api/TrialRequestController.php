<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TrialRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TrialRequestController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'team_id' => ['required', 'exists:amateur_teams,id'],
            'request_type' => ['required', 'in:trial_match,training,friendly_match'],
            'message' => ['nullable', 'string', 'max:1000'],
            'preferred_date' => ['nullable', 'date', 'after:today'],
            'preferred_time' => ['nullable', 'string', 'max:50'],
        ]);

        // Aynı takıma bekleyen bir talep var mı kontrol et
        $existing = TrialRequest::where('player_user_id', $request->user()->id)
            ->where('team_id', $validated['team_id'])
            ->where('status', 'pending')
            ->exists();

        if ($existing) {
            return response()->json([
                'ok' => false,
                'message' => 'Bu takıma zaten bekleyen bir talebiniz var.',
            ], 400);
        }

        $trialRequest = TrialRequest::create([
            'player_user_id' => $request->user()->id,
            ...$validated,
        ]);

        $trialRequest->load(['team', 'player']);

        return response()->json([
            'ok' => true,
            'message' => 'Deneme talebi gönderildi.',
            'data' => $trialRequest,
        ], 201);
    }

    public function myRequests(Request $request): JsonResponse
    {
        $requests = TrialRequest::query()
            ->where('player_user_id', $request->user()->id)
            ->with('team')
            ->latest()
            ->paginate(20);

        return response()->json([
            'ok' => true,
            'data' => $requests,
        ]);
    }

    public function teamRequests(Request $request, int $teamId): JsonResponse
    {
        // Takım sahibi mi kontrol et
        $team = \App\Models\AmateurTeam::findOrFail($teamId);

        if ($team->user_id !== $request->user()->id) {
            return response()->json([
                'ok' => false,
                'message' => 'Bu takımın taleplerine erişim yetkiniz yok.',
            ], 403);
        }

        $requests = TrialRequest::query()
            ->where('team_id', $teamId)
            ->with(['player.playerProfile'])
            ->latest()
            ->paginate(20);

        return response()->json([
            'ok' => true,
            'data' => $requests,
        ]);
    }

    public function respond(Request $request, int $id): JsonResponse
    {
        $trialRequest = TrialRequest::findOrFail($id);
        $team = $trialRequest->team;

        if ($team->user_id !== $request->user()->id) {
            return response()->json([
                'ok' => false,
                'message' => 'Bu talebe cevap verme yetkiniz yok.',
            ], 403);
        }

        $validated = $request->validate([
            'status' => ['required', 'in:accepted,rejected'],
            'team_response' => ['nullable', 'string', 'max:1000'],
            'scheduled_date' => ['nullable', 'date'],
        ]);

        $trialRequest->update($validated);

        return response()->json([
            'ok' => true,
            'message' => 'Talep güncellendi.',
            'data' => $trialRequest,
        ]);
    }

    public function addFeedback(Request $request, int $id): JsonResponse
    {
        $trialRequest = TrialRequest::findOrFail($id);
        $team = $trialRequest->team;

        if ($team->user_id !== $request->user()->id) {
            return response()->json([
                'ok' => false,
                'message' => 'Geri bildirim verme yetkiniz yok.',
            ], 403);
        }

        $validated = $request->validate([
            'feedback' => ['required', 'string', 'max:2000'],
            'performance_rating' => ['nullable', 'integer', 'min:1', 'max:10'],
            'offered_position' => ['boolean'],
        ]);

        $trialRequest->update([
            ...$validated,
            'status' => 'completed',
        ]);

        return response()->json([
            'ok' => true,
            'message' => 'Geri bildirim kaydedildi.',
            'data' => $trialRequest,
        ]);
    }
}
