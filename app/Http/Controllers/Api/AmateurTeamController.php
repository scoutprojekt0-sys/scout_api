<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AmateurTeam;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AmateurTeamController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = AmateurTeam::query()->with('manager');

        // Filtreleme
        if ($request->has('city')) {
            $query->where('city', $request->input('city'));
        }

        if ($request->has('district')) {
            $query->where('district', $request->input('district'));
        }

        if ($request->has('team_type')) {
            $query->where('team_type', $request->input('team_type'));
        }

        if ($request->boolean('accepting_players')) {
            $query->where('accepts_new_players', true)
                  ->where('needed_players', '>', 0);
        }

        if ($request->has('max_monthly_fee')) {
            $query->where('monthly_fee', '<=', $request->input('max_monthly_fee'));
        }

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('team_name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('neighborhood', 'like', "%{$search}%");
            });
        }

        $teams = $query->latest()->paginate(20);

        return response()->json([
            'ok' => true,
            'data' => $teams,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'team_name' => ['required', 'string', 'max:140'],
            'team_type' => ['required', 'in:club,neighborhood,workplace,university,school,friends'],
            'city' => ['required', 'string', 'max:80'],
            'district' => ['nullable', 'string', 'max:80'],
            'neighborhood' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:2000'],
            'home_field' => ['nullable', 'string', 'max:120'],
            'field_type' => ['nullable', 'in:grass,artificial,halısaha,concrete'],
            'practice_days' => ['nullable', 'string', 'max:100'],
            'practice_time' => ['nullable', 'string', 'max:50'],
            'current_players' => ['integer', 'min:0'],
            'needed_players' => ['integer', 'min:0'],
            'needed_positions' => ['nullable', 'array'],
            'monthly_fee' => ['nullable', 'numeric', 'min:0'],
            'accepts_new_players' => ['boolean'],
            'contact_phone' => ['nullable', 'string', 'max:30'],
            'whatsapp_group' => ['nullable', 'string', 'max:255'],
        ]);

        $team = AmateurTeam::create([
            'user_id' => $request->user()->id,
            ...$validated,
        ]);

        return response()->json([
            'ok' => true,
            'message' => 'Takım oluşturuldu.',
            'data' => $team,
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $team = AmateurTeam::with(['manager', 'playerSearches' => function($q) {
            $q->where('status', 'active');
        }])->findOrFail($id);

        return response()->json([
            'ok' => true,
            'data' => $team,
        ]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $team = AmateurTeam::findOrFail($id);

        if ($team->user_id !== $request->user()->id) {
            return response()->json([
                'ok' => false,
                'message' => 'Bu takımı düzenleme yetkiniz yok.',
            ], 403);
        }

        $validated = $request->validate([
            'team_name' => ['sometimes', 'string', 'max:140'],
            'description' => ['nullable', 'string', 'max:2000'],
            'home_field' => ['nullable', 'string', 'max:120'],
            'field_type' => ['nullable', 'in:grass,artificial,halısaha,concrete'],
            'practice_days' => ['nullable', 'string', 'max:100'],
            'practice_time' => ['nullable', 'string', 'max:50'],
            'current_players' => ['integer', 'min:0'],
            'needed_players' => ['integer', 'min:0'],
            'needed_positions' => ['nullable', 'array'],
            'monthly_fee' => ['nullable', 'numeric', 'min:0'],
            'accepts_new_players' => ['boolean'],
            'contact_phone' => ['nullable', 'string', 'max:30'],
            'whatsapp_group' => ['nullable', 'string', 'max:255'],
        ]);

        $team->update($validated);

        return response()->json([
            'ok' => true,
            'message' => 'Takım güncellendi.',
            'data' => $team,
        ]);
    }

    public function nearbyTeams(Request $request): JsonResponse
    {
        $city = $request->input('city');
        $district = $request->input('district');

        $query = AmateurTeam::query()
            ->where('accepts_new_players', true)
            ->where('needed_players', '>', 0);

        if ($city) {
            $query->where('city', $city);
        }

        if ($district) {
            $query->where('district', $district);
        }

        $teams = $query->latest()->limit(20)->get();

        return response()->json([
            'ok' => true,
            'data' => $teams,
        ]);
    }
}
