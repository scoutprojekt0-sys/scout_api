<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use App\Models\ContractHistory;
use App\Models\SignatureRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ContractController extends Controller
{
    // Yeni sözleşme oluştur (Avukat)
    // Public ticker: sadece sozlesmesi biten/askida oyuncular
    public function live(Request $request): JsonResponse
    {
        $limit = max(1, min((int) $request->query('limit', 12), 50));
        $statusesRaw = (string) $request->query('statuses', 'expired,suspended');
        $requestedStatuses = collect(explode(',', $statusesRaw))
            ->map(fn ($s) => trim(strtolower($s)))
            ->filter(fn ($s) => in_array($s, ['expired', 'suspended'], true))
            ->values();

        if ($requestedStatuses->isEmpty()) {
            $requestedStatuses = collect(['expired', 'suspended']);
        }

        $today = now()->toDateString();

        $rows = DB::table('users as u')
            ->leftJoin('player_profiles as pp', 'pp.user_id', '=', 'u.id')
            ->where('u.role', 'player')
            ->where(function ($q) use ($today, $requestedStatuses) {
                if ($requestedStatuses->contains('expired')) {
                    $q->orWhere(function ($sub) use ($today) {
                        $sub->whereNotNull('pp.contract_expires')
                            ->whereDate('pp.contract_expires', '<', $today);
                    });
                }

                if ($requestedStatuses->contains('suspended')) {
                    $q->orWhereExists(function ($sub) {
                        $sub->select(DB::raw(1))
                            ->from('contracts as c')
                            ->whereColumn('c.player_user_id', 'u.id')
                            ->whereIn('c.status', ['under_negotiation', 'disputed', 'terminated']);
                    });
                }
            })
            ->select([
                'u.id as player_id',
                'u.name as player_name',
                'pp.position',
                DB::raw('COALESCE(pp.current_team, \'-\') as club_name'),
                'pp.contract_expires',
                DB::raw("EXISTS(
                    SELECT 1
                    FROM contracts c
                    WHERE c.player_user_id = u.id
                      AND c.status IN ('under_negotiation', 'disputed', 'terminated')
                ) as has_suspended_contract"),
            ])
            ->orderByDesc('u.updated_at')
            ->limit($limit)
            ->get();

        $items = $rows->map(function ($row) use ($today, $requestedStatuses) {
            $isExpired = !empty($row->contract_expires) && substr((string) $row->contract_expires, 0, 10) < $today;
            $isSuspended = (int) ($row->has_suspended_contract ?? 0) === 1;

            $status = '';
            if ($isSuspended && $requestedStatuses->contains('suspended')) {
                $status = 'suspended';
            } elseif ($isExpired && $requestedStatuses->contains('expired')) {
                $status = 'expired';
            }

            if ($status === '') {
                return null;
            }

            return [
                'player_id' => (int) $row->player_id,
                'player_name' => (string) ($row->player_name ?? 'Oyuncu'),
                'position' => (string) ($row->position ?? '-'),
                'club_name' => (string) ($row->club_name ?? '-'),
                'status' => $status,
                'contract_expires' => $row->contract_expires,
                'note' => $status === 'suspended'
                    ? 'Sozlesme sureci askida, gorusmeler devam ediyor.'
                    : 'Sozlesmesi sona erdi, gorusmelere acik.',
            ];
        })->filter()->values();

        return response()->json([
            'ok' => true,
            'data' => $items,
            'meta' => [
                'statuses' => $requestedStatuses->all(),
                'count' => $items->count(),
            ],
        ]);
    }

    public function create(Request $request): JsonResponse
    {
        $lawyerId = $request->user()?->lawyer?->id;
        if (!$lawyerId) {
            return response()->json([
                'ok' => false,
                'message' => 'Sözleşme oluşturma yetkisi yalnızca avukatlarda.',
            ], 403);
        }

        $validated = $request->validate([
            'player_user_id' => ['required', 'exists:users,id'],
            'manager_user_id' => ['required', 'exists:users,id'],
            'type' => ['required', 'in:player_team,transfer_agreement,endorsement,image_rights,commercial,other'],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date', 'after:start_date'],
            'total_amount' => ['nullable', 'numeric', 'min:0'],
            'terms_conditions' => ['required', 'string'],
            'clauses' => ['nullable', 'array'],
            'special_conditions' => ['nullable', 'array'],
        ]);

        $contract = Contract::create([
            'lawyer_id' => $lawyerId,
            'contract_number' => 'CNT-' . now()->format('YmdHis'),
            'contract_date' => now(),
            'status' => 'draft',
            ...$validated,
        ]);

        // History kaydı
        ContractHistory::create([
            'contract_id' => $contract->id,
            'action' => 'created',
            'performed_by_role' => 'lawyer',
            'performed_by_user_id' => $request->user()->id,
        ]);

        return response()->json([
            'ok' => true,
            'message' => 'Sözleşme oluşturuldu.',
            'data' => $contract,
        ], 201);
    }

    // Sözleşmeyi taraflara gönder (Avukat)
    public function propose(Request $request, int $contractId): JsonResponse
    {
        $contract = Contract::findOrFail($contractId);
        $user = $request->user();
        $lawyerId = $user?->lawyer?->id;

        if (!$lawyerId || $contract->lawyer_id !== $lawyerId) {
            return response()->json([
                'ok' => false,
                'message' => 'Yetkiniz yok.',
            ], 403);
        }

        // Futbolcu ve menajere imza talepleri gönder
        SignatureRequest::create([
            'contract_id' => $contractId,
            'requested_from' => 'player',
            'user_id' => $contract->player_user_id,
            'lawyer_id' => $contract->lawyer_id,
            'request_message' => 'Sözleşmeyi inceleyip imzalayınız.',
            'requested_at' => now(),
            'deadline' => now()->addDays(7),
        ]);

        SignatureRequest::create([
            'contract_id' => $contractId,
            'requested_from' => 'manager',
            'user_id' => $contract->manager_user_id,
            'lawyer_id' => $contract->lawyer_id,
            'request_message' => 'Sözleşmeyi inceleyip imzalayınız.',
            'requested_at' => now(),
            'deadline' => now()->addDays(7),
        ]);

        $contract->update(['status' => 'proposed']);

        ContractHistory::create([
            'contract_id' => $contractId,
            'action' => 'proposed',
            'performed_by_role' => 'lawyer',
            'performed_by_user_id' => $user->id,
        ]);

        return response()->json([
            'ok' => true,
            'message' => 'Sözleşme taraflara gönderildi.',
        ]);
    }

    // Sözleşmeyi imzala
    public function sign(Request $request, int $signatureRequestId): JsonResponse
    {
        $signRequest = SignatureRequest::findOrFail($signatureRequestId);

        if ($signRequest->user_id !== $request->user()->id) {
            return response()->json([
                'ok' => false,
                'message' => 'Bu imza talebini işlem yapma yetkiniz yok.',
            ], 403);
        }

        $contract = $signRequest->contract;

        // İmzayı kaydet
        $signRequest->update([
            'status' => 'signed',
            'signed_at' => now(),
            'signature_ip' => $request->ip(),
            'signature_device' => $request->header('User-Agent'),
        ]);

        // Kontratın doğru alanını güncelle
        if ($signRequest->requested_from === 'player') {
            $contract->player_signed_at = now();
        } else {
            $contract->manager_signed_at = now();
        }

        // Her iki taraf da imzaladıysa
        if ($contract->player_signed_at && $contract->manager_signed_at) {
            $contract->status = 'awaiting_signature'; // Avukat onayı bekleniyor
        }

        $contract->save();

        ContractHistory::create([
            'contract_id' => $contract->id,
            'action' => 'signed',
            'performed_by_role' => $signRequest->requested_from,
            'performed_by_user_id' => $request->user()->id,
        ]);

        return response()->json([
            'ok' => true,
            'message' => 'Sözleşme imzalandı.',
            'data' => $contract,
        ]);
    }

    // Sözleşmeyi reddet
    public function reject(Request $request, int $signatureRequestId): JsonResponse
    {
        $signRequest = SignatureRequest::findOrFail($signatureRequestId);

        if ($signRequest->user_id !== $request->user()->id) {
            return response()->json([
                'ok' => false,
                'message' => 'Yetkiniz yok.',
            ], 403);
        }

        $validated = $request->validate([
            'reason' => ['required', 'string', 'max:1000'],
        ]);

        $signRequest->update([
            'status' => 'rejected',
            'rejection_reason' => $validated['reason'],
        ]);

        $contract = $signRequest->contract;
        $contract->status = 'under_negotiation';
        $contract->save();

        ContractHistory::create([
            'contract_id' => $contract->id,
            'action' => 'negotiated',
            'performed_by_role' => $signRequest->requested_from,
            'performed_by_user_id' => $request->user()->id,
            'details' => $validated['reason'],
        ]);

        return response()->json([
            'ok' => true,
            'message' => 'Sözleşme reddedildi. Müzakere başladı.',
        ]);
    }

    // Sözleşmeyi getir
    public function show(int $contractId, Request $request): JsonResponse
    {
        $contract = Contract::with([
            'player',
            'manager',
            'lawyer',
            'negotiations',
            'versions',
            'signatureRequests',
            'disputes',
            'reviews',
        ])->findOrFail($contractId);

        // Erişim kontrolü
        if (!$this->canAccessContract($request, $contract)) {
            return response()->json([
                'ok' => false,
                'message' => 'Bu sözleşmeye erişim yetkiniz yok.',
            ], 403);
        }

        return response()->json([
            'ok' => true,
            'data' => [
                'contract' => $contract,
                'progress' => $contract->progress,
                'pending_signatures' => $contract->signatureRequests()
                    ->where('status', 'pending')
                    ->get(),
            ],
        ]);
    }

    // Benim sözleşmelerim
    public function myContracts(Request $request): JsonResponse
    {
        $userId = $request->user()->id;

        $contracts = Contract::where(function($q) use ($userId) {
                $q->where('player_user_id', $userId)
                  ->orWhere('manager_user_id', $userId);
            })
            ->with(['player', 'manager', 'lawyer'])
            ->latest()
            ->paginate(20);

        return response()->json([
            'ok' => true,
            'data' => $contracts,
        ]);
    }

    // Route uyumluluğu için genel liste endpoint'i
    public function index(Request $request): JsonResponse
    {
        return $this->myContracts($request);
    }

    // Route uyumluluğu için genel oluşturma endpoint'i
    public function store(Request $request): JsonResponse
    {
        return $this->create($request);
    }

    // Basit kontrat güncelleme
    public function update(Request $request, int $id): JsonResponse
    {
        $contract = Contract::findOrFail($id);
        if (!$this->canAccessContract($request, $contract)) {
            return response()->json([
                'ok' => false,
                'message' => 'Bu sözleşmeyi güncelleme yetkiniz yok.',
            ], 403);
        }

        $validated = $request->validate([
            'status' => ['nullable', 'in:draft,proposed,under_negotiation,awaiting_signature,signed,active,completed,terminated,disputed'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'total_amount' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
        ]);

        if (!empty($validated)) {
            $contract->update($validated);
        }

        return response()->json([
            'ok' => true,
            'message' => 'Sözleşme güncellendi.',
            'data' => $contract->fresh(),
        ]);
    }

    // Basit kontrat silme (yalnızca taslak/önerilen aşamada)
    public function destroy(Request $request, int $id): JsonResponse
    {
        $contract = Contract::findOrFail($id);
        if (!$this->canAccessContract($request, $contract)) {
            return response()->json([
                'ok' => false,
                'message' => 'Bu sözleşmeyi silme yetkiniz yok.',
            ], 403);
        }

        if (!in_array($contract->status, ['draft', 'proposed', 'under_negotiation'], true)) {
            return response()->json([
                'ok' => false,
                'message' => 'Bu aşamadaki sözleşme silinemez.',
            ], 422);
        }

        $contract->delete();

        return response()->json([
            'ok' => true,
            'message' => 'Sözleşme silindi.',
        ]);
    }

    private function canAccessContract(Request $request, Contract $contract): bool
    {
        $user = $request->user();
        if (!$user) return false;

        $lawyerId = null;
        try {
            $lawyerId = $user->lawyer?->id;
        } catch (\Throwable $e) {
            $lawyerId = null;
        }

        return (int) $user->id === (int) $contract->player_user_id
            || (int) $user->id === (int) $contract->manager_user_id
            || ((int) ($lawyerId ?? 0) > 0 && (int) $lawyerId === (int) $contract->lawyer_id)
            || ($user->role ?? '') === 'admin';
    }
}
