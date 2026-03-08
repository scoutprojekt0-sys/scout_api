<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ContractNegotiation;
use App\Models\ContractDispute;
use App\Models\LawyerReview;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ContractNegotiationController extends Controller
{
    // Müzakere başlat (Avukat)
    public function startNegotiation(Request $request, int $contractId): JsonResponse
    {
        $validated = $request->validate([
            'player_request' => ['nullable', 'string', 'max:2000'],
            'manager_offer' => ['nullable', 'string', 'max:2000'],
        ]);

        $negotiation = ContractNegotiation::create([
            'contract_id' => $contractId,
            'lawyer_id' => $request->user()->lawyer()->id,
            'stage' => 'initial_review',
            'proposed_at' => now(),
            ...$validated,
        ]);

        return response()->json([
            'ok' => true,
            'message' => 'Müzakere başlatıldı.',
            'data' => $negotiation,
        ], 201);
    }

    // Müzakere cevabı (Futbolcu/Menajer)
    public function respondToNegotiation(Request $request, int $negotiationId): JsonResponse
    {
        $validated = $request->validate([
            'response' => ['required', 'string', 'max:2000'],
            'amendments' => ['nullable', 'array'],
        ]);

        $negotiation = ContractNegotiation::findOrFail($negotiationId);
        $user = $request->user();

        if ($negotiation->contract->player_user_id === $user->id) {
            $negotiation->player_request = $validated['response'];
        } elseif ($negotiation->contract->manager_user_id === $user->id) {
            $negotiation->manager_offer = $validated['response'];
        } else {
            return response()->json([
                'ok' => false,
                'message' => 'Yetkiniz yok.',
            ], 403);
        }

        if ($validated['amendments']) {
            $negotiation->amendments = $validated['amendments'];
        }

        $negotiation->reviewed_at = now();
        $negotiation->result = 'revised';
        $negotiation->save();

        return response()->json([
            'ok' => true,
            'message' => 'Cevap kaydedildi.',
            'data' => $negotiation,
        ]);
    }

    // Uyuşmazlık bildir
    public function reportDispute(Request $request, int $contractId): JsonResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'description' => ['required', 'string', 'max:2000'],
            'severity' => ['required', 'in:low,medium,high,critical'],
            'related_clauses' => ['nullable', 'array'],
        ]);

        $dispute = ContractDispute::create([
            'contract_id' => $contractId,
            'raised_by' => $this->getUserRole($request),
            'user_id' => $request->user()->id,
            'status' => 'reported',
            ...$validated,
        ]);

        return response()->json([
            'ok' => true,
            'message' => 'Uyuşmazlık bildirildi. Avukat tarafından incelenecek.',
            'data' => $dispute,
        ], 201);
    }

    // Avukat sözleşmeyi incele ve onayla
    public function reviewAndApprove(Request $request, int $contractId): JsonResponse
    {
        $validated = $request->validate([
            'legal_review' => ['required', 'string'],
            'risk_assessment' => ['nullable', 'array'],
            'recommendations' => ['nullable', 'array'],
            'compliance_score' => ['nullable', 'integer', 'min:1', 'max:100'],
            'review_status' => ['required', 'in:approved,needs_revision,rejected'],
        ]);

        $review = LawyerReview::updateOrCreate(
            ['contract_id' => $contractId],
            [
                'lawyer_id' => $request->user()->lawyer()->id,
                ...$validated,
            ]
        );

        $contract = $review->contract;
        if ($validated['review_status'] === 'approved') {
            $contract->lawyer_approved_at = now();
            $contract->status = 'signed';
        } else {
            $contract->status = 'under_negotiation';
        }
        $contract->save();

        return response()->json([
            'ok' => true,
            'message' => 'Sözleşme incelemesi tamamlandı.',
            'data' => $review,
        ]);
    }

    // Müzakere geçmişini getir
    public function getNegotiationHistory(Request $request, int $contractId): JsonResponse
    {
        $negotiations = ContractNegotiation::where('contract_id', $contractId)
            ->with('lawyer')
            ->latest()
            ->get();

        return response()->json([
            'ok' => true,
            'data' => $negotiations,
        ]);
    }

    private function getUserRole($request): string
    {
        if ($request->user()->lawyer()) {
            return 'lawyer';
        }
        // Futbolcu veya menajer belirtmek için kontrat kontrol et
        return 'player'; // Basit için
    }
}
