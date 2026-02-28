<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RadarController extends Controller
{
    public function matches(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'need_id' => ['nullable', 'integer', 'min:1'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:50'],
        ]);

        $limit = (int) ($validated['limit'] ?? 10);

        $needQuery = DB::table('club_needs')->where('status', 'open');
        if (!empty($validated['need_id'])) {
            $needQuery->where('id', (int) $validated['need_id']);
        }
        $need = $needQuery->orderByDesc('updated_at')->first();

        if (!$need) {
            return response()->json([
                'ok' => true,
                'need' => null,
                'data' => [],
                'message' => 'Acik kulup ihtiyaci bulunamadi.',
            ]);
        }

        $rows = DB::table('transfer_market_listings as market')
            ->join('users as players', 'players.id', '=', 'market.player_user_id')
            ->leftJoin('player_profiles as profile', 'profile.user_id', '=', 'players.id')
            ->where('market.market_status', 'open')
            ->select([
                'market.id as listing_id',
                'market.player_user_id',
                'players.name as player_name',
                'players.city as player_city',
                'profile.position',
                'profile.birth_year',
                'profile.current_team',
                'market.asking_fee_eur',
                'market.form_score',
                'market.minutes_5_matches',
                'market.injury_status',
            ])
            ->limit(200)
            ->get();

        $currentYear = (int) now()->format('Y');
        $matches = $rows->map(function ($row) use ($need, $currentYear) {
            $age = $row->birth_year ? max(0, $currentYear - (int) $row->birth_year) : null;

            $positionScore = 0;
            if (!empty($row->position) && !empty($need->position)) {
                $playerPos = strtolower((string) $row->position);
                $needPos = strtolower((string) $need->position);
                $positionScore = str_contains($playerPos, $needPos) || str_contains($needPos, $playerPos) ? 100 : 0;
            }

            $ageScore = 60;
            if ($age !== null && $need->age_min !== null && $need->age_max !== null) {
                if ($age >= (int) $need->age_min && $age <= (int) $need->age_max) {
                    $ageScore = 100;
                } else {
                    $distance = $age < (int) $need->age_min
                        ? ((int) $need->age_min - $age)
                        : ($age - (int) $need->age_max);
                    $ageScore = max(0, 100 - ($distance * 8));
                }
            }

            $form = $row->form_score !== null ? (int) $row->form_score : 50;
            $minutesRatio = min(1, max(0, (int) ($row->minutes_5_matches ?? 0)) / 450);
            $formMinutesScore = (int) round(($form * 0.6) + (($minutesRatio * 100) * 0.4));

            $budgetScore = 60;
            if ($need->budget_max_eur !== null && $row->asking_fee_eur !== null) {
                $budget = (float) $need->budget_max_eur;
                $asking = (float) $row->asking_fee_eur;
                if ($budget <= 0) {
                    $budgetScore = 0;
                } elseif ($asking <= $budget) {
                    $budgetScore = 100;
                } else {
                    $overPercent = (($asking - $budget) / $budget) * 100;
                    $budgetScore = (int) max(0, 100 - ($overPercent * 0.5));
                }
            }

            $total = (int) round(
                ($positionScore * 0.40) +
                ($ageScore * 0.20) +
                ($formMinutesScore * 0.25) +
                ($budgetScore * 0.15)
            );

            $reasons = [];
            if ($positionScore >= 100) {
                $reasons[] = 'Pozisyon ihtiyac ile uyumlu';
            }
            if ($ageScore >= 90) {
                $reasons[] = 'Yas profili hedef aralikta';
            }
            if ($budgetScore >= 90) {
                $reasons[] = 'Bonservis butceye uygun';
            }
            if ($formMinutesScore >= 70) {
                $reasons[] = 'Form ve dakika trendi guclu';
            }
            if (count($reasons) === 0) {
                $reasons[] = 'Kriterlerin bir kismina yakin';
            }

            return [
                'listing_id' => (int) $row->listing_id,
                'player_user_id' => (int) $row->player_user_id,
                'player_name' => $row->player_name,
                'player_city' => $row->player_city,
                'position' => $row->position,
                'current_team' => $row->current_team,
                'age' => $age,
                'asking_fee_eur' => $row->asking_fee_eur,
                'form_score' => $row->form_score,
                'minutes_5_matches' => $row->minutes_5_matches,
                'injury_status' => $row->injury_status,
                'match_score' => $total,
                'reasons' => array_slice($reasons, 0, 3),
            ];
        })->sortByDesc('match_score')->take($limit)->values();

        return response()->json([
            'ok' => true,
            'need' => [
                'id' => (int) $need->id,
                'team_user_id' => (int) $need->team_user_id,
                'title' => $need->title,
                'position' => $need->position,
                'age_min' => $need->age_min,
                'age_max' => $need->age_max,
                'budget_max_eur' => $need->budget_max_eur,
                'city' => $need->city,
                'urgency' => $need->urgency,
            ],
            'data' => $matches,
        ]);
    }
}
