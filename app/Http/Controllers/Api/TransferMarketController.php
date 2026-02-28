<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class TransferMarketController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'status' => ['nullable', Rule::in(['open', 'in_talk', 'closed'])],
            'injury_status' => ['nullable', Rule::in(['fit', 'doubtful', 'injured'])],
            'position' => ['nullable', 'string', 'max:40'],
            'city' => ['nullable', 'string', 'max:80'],
            'min_fee' => ['nullable', 'integer', 'min:0'],
            'max_fee' => ['nullable', 'integer', 'min:0'],
            'min_form' => ['nullable', 'integer', 'min:0', 'max:100'],
            'max_form' => ['nullable', 'integer', 'min:0', 'max:100'],
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $perPage = (int) ($validated['per_page'] ?? 20);

        $query = DB::table('transfer_market_listings as market')
            ->join('users as players', 'players.id', '=', 'market.player_user_id')
            ->leftJoin('player_profiles as profile', 'profile.user_id', '=', 'players.id')
            ->select([
                'market.id',
                'market.player_user_id',
                'players.name as player_name',
                'players.city as player_city',
                'profile.current_team',
                'profile.position',
                'profile.birth_year',
                'market.asking_fee_eur',
                'market.salary_min_eur',
                'market.salary_max_eur',
                'market.contract_until',
                'market.form_score',
                'market.minutes_5_matches',
                'market.injury_status',
                'market.market_status',
                'market.note',
                'market.created_at',
                'market.updated_at',
            ]);

        if (!empty($validated['status'])) {
            $query->where('market.market_status', $validated['status']);
        }

        if (!empty($validated['injury_status'])) {
            $query->where('market.injury_status', $validated['injury_status']);
        }

        if (!empty($validated['position'])) {
            $query->where('profile.position', 'like', '%' . $validated['position'] . '%');
        }

        if (!empty($validated['city'])) {
            $query->where('players.city', 'like', '%' . $validated['city'] . '%');
        }

        if (array_key_exists('min_fee', $validated)) {
            $query->where('market.asking_fee_eur', '>=', (int) $validated['min_fee']);
        }

        if (array_key_exists('max_fee', $validated)) {
            $query->where('market.asking_fee_eur', '<=', (int) $validated['max_fee']);
        }

        if (array_key_exists('min_form', $validated)) {
            $query->where('market.form_score', '>=', (int) $validated['min_form']);
        }

        if (array_key_exists('max_form', $validated)) {
            $query->where('market.form_score', '<=', (int) $validated['max_form']);
        }

        $listings = $query
            ->orderByRaw("case market.market_status when 'open' then 1 when 'in_talk' then 2 else 3 end")
            ->orderByDesc('market.updated_at')
            ->paginate($perPage);

        $currentYear = (int) now()->format('Y');
        $listings->getCollection()->transform(function ($row) use ($currentYear) {
            $row->age = $row->birth_year ? max(0, $currentYear - (int) $row->birth_year) : null;
            return $row;
        });

        return response()->json([
            'ok' => true,
            'filters' => [
                'status' => $validated['status'] ?? null,
                'injury_status' => $validated['injury_status'] ?? null,
                'position' => $validated['position'] ?? null,
                'city' => $validated['city'] ?? null,
                'min_fee' => $validated['min_fee'] ?? null,
                'max_fee' => $validated['max_fee'] ?? null,
                'min_form' => $validated['min_form'] ?? null,
                'max_form' => $validated['max_form'] ?? null,
            ],
            'data' => $listings,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $authUser = $request->user();
        if ($authUser->role !== 'player') {
            return response()->json([
                'ok' => false,
                'message' => 'Sadece oyuncu rolu transfer borsasina kayit acabilir.',
            ], Response::HTTP_FORBIDDEN);
        }

        $validated = $request->validate([
            'asking_fee_eur' => ['nullable', 'integer', 'min:0', 'max:9999999999'],
            'salary_min_eur' => ['nullable', 'integer', 'min:0', 'max:9999999'],
            'salary_max_eur' => ['nullable', 'integer', 'min:0', 'max:9999999'],
            'contract_until' => ['nullable', 'date'],
            'form_score' => ['nullable', 'integer', 'min:0', 'max:100'],
            'minutes_5_matches' => ['nullable', 'integer', 'min:0', 'max:900'],
            'injury_status' => ['nullable', Rule::in(['fit', 'doubtful', 'injured'])],
            'market_status' => ['nullable', Rule::in(['open', 'in_talk', 'closed'])],
            'note' => ['nullable', 'string', 'max:3000'],
        ]);

        if (
            isset($validated['salary_min_eur'], $validated['salary_max_eur'])
            && (int) $validated['salary_min_eur'] > (int) $validated['salary_max_eur']
        ) {
            return response()->json([
                'ok' => false,
                'message' => 'Maas minimum degeri maksimumdan buyuk olamaz.',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $existing = DB::table('transfer_market_listings')
            ->where('player_user_id', (int) $authUser->id)
            ->first();

        if ($existing) {
            return response()->json([
                'ok' => false,
                'message' => 'Bu oyuncu icin transfer kaydi zaten var. Guncelleme endpointini kullanin.',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $id = DB::table('transfer_market_listings')->insertGetId([
            'player_user_id' => (int) $authUser->id,
            'asking_fee_eur' => $validated['asking_fee_eur'] ?? null,
            'salary_min_eur' => $validated['salary_min_eur'] ?? null,
            'salary_max_eur' => $validated['salary_max_eur'] ?? null,
            'contract_until' => $validated['contract_until'] ?? null,
            'form_score' => $validated['form_score'] ?? null,
            'minutes_5_matches' => $validated['minutes_5_matches'] ?? null,
            'injury_status' => $validated['injury_status'] ?? 'fit',
            'market_status' => $validated['market_status'] ?? 'open',
            'note' => $validated['note'] ?? null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $created = DB::table('transfer_market_listings')->where('id', $id)->first();

        return response()->json([
            'ok' => true,
            'message' => 'Transfer borsasi kaydi olusturuldu.',
            'data' => $created,
        ], Response::HTTP_CREATED);
    }

    public function show(int $id): JsonResponse
    {
        $listing = DB::table('transfer_market_listings as market')
            ->join('users as players', 'players.id', '=', 'market.player_user_id')
            ->leftJoin('player_profiles as profile', 'profile.user_id', '=', 'players.id')
            ->where('market.id', $id)
            ->select([
                'market.id',
                'market.player_user_id',
                'players.name as player_name',
                'players.city as player_city',
                'profile.current_team',
                'profile.position',
                'profile.birth_year',
                'market.asking_fee_eur',
                'market.salary_min_eur',
                'market.salary_max_eur',
                'market.contract_until',
                'market.form_score',
                'market.minutes_5_matches',
                'market.injury_status',
                'market.market_status',
                'market.note',
                'market.created_at',
                'market.updated_at',
            ])
            ->first();

        if (!$listing) {
            return response()->json([
                'ok' => false,
                'message' => 'Transfer borsasi kaydi bulunamadi.',
            ], Response::HTTP_NOT_FOUND);
        }

        $listing->age = $listing->birth_year ? max(0, (int) now()->format('Y') - (int) $listing->birth_year) : null;

        return response()->json([
            'ok' => true,
            'data' => $listing,
        ]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $listing = DB::table('transfer_market_listings')->where('id', $id)->first();
        if (!$listing) {
            return response()->json([
                'ok' => false,
                'message' => 'Transfer borsasi kaydi bulunamadi.',
            ], Response::HTTP_NOT_FOUND);
        }

        $authUser = $request->user();
        if ((int) $authUser->id !== (int) $listing->player_user_id) {
            return response()->json([
                'ok' => false,
                'message' => 'Bu kaydi guncelleme yetkiniz yok.',
            ], Response::HTTP_FORBIDDEN);
        }

        $validated = $request->validate([
            'asking_fee_eur' => ['sometimes', 'nullable', 'integer', 'min:0', 'max:9999999999'],
            'salary_min_eur' => ['sometimes', 'nullable', 'integer', 'min:0', 'max:9999999'],
            'salary_max_eur' => ['sometimes', 'nullable', 'integer', 'min:0', 'max:9999999'],
            'contract_until' => ['sometimes', 'nullable', 'date'],
            'form_score' => ['sometimes', 'nullable', 'integer', 'min:0', 'max:100'],
            'minutes_5_matches' => ['sometimes', 'nullable', 'integer', 'min:0', 'max:900'],
            'injury_status' => ['sometimes', Rule::in(['fit', 'doubtful', 'injured'])],
            'market_status' => ['sometimes', Rule::in(['open', 'in_talk', 'closed'])],
            'note' => ['sometimes', 'nullable', 'string', 'max:3000'],
        ]);

        $nextMinSalary = array_key_exists('salary_min_eur', $validated)
            ? $validated['salary_min_eur']
            : $listing->salary_min_eur;
        $nextMaxSalary = array_key_exists('salary_max_eur', $validated)
            ? $validated['salary_max_eur']
            : $listing->salary_max_eur;

        if (
            $nextMinSalary !== null
            && $nextMaxSalary !== null
            && (int) $nextMinSalary > (int) $nextMaxSalary
        ) {
            return response()->json([
                'ok' => false,
                'message' => 'Maas minimum degeri maksimumdan buyuk olamaz.',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        DB::table('transfer_market_listings')
            ->where('id', $id)
            ->update([
                'asking_fee_eur' => array_key_exists('asking_fee_eur', $validated) ? $validated['asking_fee_eur'] : $listing->asking_fee_eur,
                'salary_min_eur' => $nextMinSalary,
                'salary_max_eur' => $nextMaxSalary,
                'contract_until' => array_key_exists('contract_until', $validated) ? $validated['contract_until'] : $listing->contract_until,
                'form_score' => array_key_exists('form_score', $validated) ? $validated['form_score'] : $listing->form_score,
                'minutes_5_matches' => array_key_exists('minutes_5_matches', $validated) ? $validated['minutes_5_matches'] : $listing->minutes_5_matches,
                'injury_status' => $validated['injury_status'] ?? $listing->injury_status,
                'market_status' => $validated['market_status'] ?? $listing->market_status,
                'note' => array_key_exists('note', $validated) ? $validated['note'] : $listing->note,
                'updated_at' => now(),
            ]);

        $updated = DB::table('transfer_market_listings')->where('id', $id)->first();

        return response()->json([
            'ok' => true,
            'message' => 'Transfer borsasi kaydi guncellendi.',
            'data' => $updated,
        ]);
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $listing = DB::table('transfer_market_listings')->where('id', $id)->first();
        if (!$listing) {
            return response()->json([
                'ok' => false,
                'message' => 'Transfer borsasi kaydi bulunamadi.',
            ], Response::HTTP_NOT_FOUND);
        }

        $authUser = $request->user();
        if ((int) $authUser->id !== (int) $listing->player_user_id) {
            return response()->json([
                'ok' => false,
                'message' => 'Bu kaydi silme yetkiniz yok.',
            ], Response::HTTP_FORBIDDEN);
        }

        DB::table('transfer_market_listings')->where('id', $id)->delete();

        return response()->json([
            'ok' => true,
            'message' => 'Transfer borsasi kaydi silindi.',
        ]);
    }
}
