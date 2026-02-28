<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class ClubNeedController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'status' => ['nullable', Rule::in(['open', 'closed'])],
            'position' => ['nullable', 'string', 'max:40'],
            'city' => ['nullable', 'string', 'max:80'],
            'team_user_id' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $perPage = (int) ($validated['per_page'] ?? 20);

        $query = DB::table('club_needs')
            ->leftJoin('users as teams', 'teams.id', '=', 'club_needs.team_user_id')
            ->select([
                'club_needs.id',
                'club_needs.team_user_id',
                'teams.name as team_name',
                'teams.city as team_city',
                'club_needs.title',
                'club_needs.position',
                'club_needs.age_min',
                'club_needs.age_max',
                'club_needs.budget_max_eur',
                'club_needs.city',
                'club_needs.urgency',
                'club_needs.status',
                'club_needs.note',
                'club_needs.created_at',
                'club_needs.updated_at',
            ]);

        if (!empty($validated['status'])) {
            $query->where('club_needs.status', $validated['status']);
        }
        if (!empty($validated['position'])) {
            $query->where('club_needs.position', 'like', '%' . $validated['position'] . '%');
        }
        if (!empty($validated['city'])) {
            $query->where('club_needs.city', 'like', '%' . $validated['city'] . '%');
        }
        if (!empty($validated['team_user_id'])) {
            $query->where('club_needs.team_user_id', (int) $validated['team_user_id']);
        }

        $data = $query->orderByDesc('club_needs.updated_at')->paginate($perPage);

        return response()->json([
            'ok' => true,
            'data' => $data,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $authUser = $request->user();
        if ($authUser->role !== 'team') {
            return response()->json([
                'ok' => false,
                'message' => 'Sadece takim rolu ihtiyac kaydi olusturabilir.',
            ], Response::HTTP_FORBIDDEN);
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'min:3', 'max:160'],
            'position' => ['required', 'string', 'min:2', 'max:40'],
            'age_min' => ['nullable', 'integer', 'min:10', 'max:60'],
            'age_max' => ['nullable', 'integer', 'min:10', 'max:60'],
            'budget_max_eur' => ['nullable', 'integer', 'min:0', 'max:9999999999'],
            'city' => ['nullable', 'string', 'max:80'],
            'urgency' => ['nullable', 'integer', 'min:1', 'max:100'],
            'status' => ['nullable', Rule::in(['open', 'closed'])],
            'note' => ['nullable', 'string', 'max:3000'],
        ]);

        if (
            isset($validated['age_min'], $validated['age_max'])
            && (int) $validated['age_min'] > (int) $validated['age_max']
        ) {
            return response()->json([
                'ok' => false,
                'message' => 'Yas min degeri max degerden buyuk olamaz.',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $id = DB::table('club_needs')->insertGetId([
            'team_user_id' => (int) $authUser->id,
            'title' => $validated['title'],
            'position' => $validated['position'],
            'age_min' => $validated['age_min'] ?? null,
            'age_max' => $validated['age_max'] ?? null,
            'budget_max_eur' => $validated['budget_max_eur'] ?? null,
            'city' => $validated['city'] ?? null,
            'urgency' => $validated['urgency'] ?? 50,
            'status' => $validated['status'] ?? 'open',
            'note' => $validated['note'] ?? null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $created = DB::table('club_needs')->where('id', $id)->first();

        return response()->json([
            'ok' => true,
            'message' => 'Kulup ihtiyac kaydi olusturuldu.',
            'data' => $created,
        ], Response::HTTP_CREATED);
    }

    public function show(int $id): JsonResponse
    {
        $data = DB::table('club_needs')
            ->leftJoin('users as teams', 'teams.id', '=', 'club_needs.team_user_id')
            ->where('club_needs.id', $id)
            ->select([
                'club_needs.*',
                'teams.name as team_name',
                'teams.city as team_city',
            ])->first();

        if (!$data) {
            return response()->json([
                'ok' => false,
                'message' => 'Kulup ihtiyac kaydi bulunamadi.',
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'ok' => true,
            'data' => $data,
        ]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $need = DB::table('club_needs')->where('id', $id)->first();
        if (!$need) {
            return response()->json([
                'ok' => false,
                'message' => 'Kulup ihtiyac kaydi bulunamadi.',
            ], Response::HTTP_NOT_FOUND);
        }

        $authUser = $request->user();
        if ((int) $authUser->id !== (int) $need->team_user_id) {
            return response()->json([
                'ok' => false,
                'message' => 'Bu kaydi guncelleme yetkiniz yok.',
            ], Response::HTTP_FORBIDDEN);
        }

        $validated = $request->validate([
            'title' => ['sometimes', 'string', 'min:3', 'max:160'],
            'position' => ['sometimes', 'string', 'min:2', 'max:40'],
            'age_min' => ['sometimes', 'nullable', 'integer', 'min:10', 'max:60'],
            'age_max' => ['sometimes', 'nullable', 'integer', 'min:10', 'max:60'],
            'budget_max_eur' => ['sometimes', 'nullable', 'integer', 'min:0', 'max:9999999999'],
            'city' => ['sometimes', 'nullable', 'string', 'max:80'],
            'urgency' => ['sometimes', 'integer', 'min:1', 'max:100'],
            'status' => ['sometimes', Rule::in(['open', 'closed'])],
            'note' => ['sometimes', 'nullable', 'string', 'max:3000'],
        ]);

        $nextAgeMin = array_key_exists('age_min', $validated) ? $validated['age_min'] : $need->age_min;
        $nextAgeMax = array_key_exists('age_max', $validated) ? $validated['age_max'] : $need->age_max;
        if ($nextAgeMin !== null && $nextAgeMax !== null && (int) $nextAgeMin > (int) $nextAgeMax) {
            return response()->json([
                'ok' => false,
                'message' => 'Yas min degeri max degerden buyuk olamaz.',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        DB::table('club_needs')->where('id', $id)->update([
            'title' => $validated['title'] ?? $need->title,
            'position' => $validated['position'] ?? $need->position,
            'age_min' => $nextAgeMin,
            'age_max' => $nextAgeMax,
            'budget_max_eur' => array_key_exists('budget_max_eur', $validated) ? $validated['budget_max_eur'] : $need->budget_max_eur,
            'city' => array_key_exists('city', $validated) ? $validated['city'] : $need->city,
            'urgency' => $validated['urgency'] ?? $need->urgency,
            'status' => $validated['status'] ?? $need->status,
            'note' => array_key_exists('note', $validated) ? $validated['note'] : $need->note,
            'updated_at' => now(),
        ]);

        $updated = DB::table('club_needs')->where('id', $id)->first();

        return response()->json([
            'ok' => true,
            'message' => 'Kulup ihtiyac kaydi guncellendi.',
            'data' => $updated,
        ]);
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $need = DB::table('club_needs')->where('id', $id)->first();
        if (!$need) {
            return response()->json([
                'ok' => false,
                'message' => 'Kulup ihtiyac kaydi bulunamadi.',
            ], Response::HTTP_NOT_FOUND);
        }

        $authUser = $request->user();
        if ((int) $authUser->id !== (int) $need->team_user_id) {
            return response()->json([
                'ok' => false,
                'message' => 'Bu kaydi silme yetkiniz yok.',
            ], Response::HTTP_FORBIDDEN);
        }

        DB::table('club_needs')->where('id', $id)->delete();

        return response()->json([
            'ok' => true,
            'message' => 'Kulup ihtiyac kaydi silindi.',
        ]);
    }
}
