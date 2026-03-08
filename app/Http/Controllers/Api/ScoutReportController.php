<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ScoutReport\StoreScoutReportRequest;
use App\Models\ScoutReport;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ScoutReportController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = ScoutReport::query()->with(['scout', 'player']);

        // Kendi raporlarım
        if ($request->boolean('my_reports')) {
            $query->where('scout_user_id', $request->user()->id);
        }

        // Belirli bir oyuncu için raporlar
        if ($request->has('player_user_id')) {
            $query->where('player_user_id', $request->input('player_user_id'));

            // Özel raporları sadece yazarı görebilir
            if ($request->user()->id !== $request->input('scout_user_id')) {
                $query->where('is_private', false);
            }
        }

        $reports = $query->latest()->paginate(20);

        return response()->json([
            'ok' => true,
            'data' => $reports,
        ]);
    }

    public function store(StoreScoutReportRequest $request): JsonResponse
    {
        $report = ScoutReport::create([
            'scout_user_id' => $request->user()->id,
            ...$request->validated(),
        ]);

        $report->load(['scout', 'player']);

        return response()->json([
            'ok' => true,
            'message' => 'Scout raporu oluşturuldu.',
            'data' => $report,
        ], 201);
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $report = ScoutReport::with(['scout', 'player'])->findOrFail($id);

        // Özel raporları sadece yazarı görebilir
        if ($report->is_private && $report->scout_user_id !== $request->user()->id) {
            return response()->json([
                'ok' => false,
                'message' => 'Bu raporu görüntüleme yetkiniz yok.',
            ], 403);
        }

        return response()->json([
            'ok' => true,
            'data' => $report,
        ]);
    }

    public function update(StoreScoutReportRequest $request, int $id): JsonResponse
    {
        $report = ScoutReport::findOrFail($id);

        if ($report->scout_user_id !== $request->user()->id) {
            return response()->json([
                'ok' => false,
                'message' => 'Bu raporu düzenleme yetkiniz yok.',
            ], 403);
        }

        $report->update($request->validated());
        $report->load(['scout', 'player']);

        return response()->json([
            'ok' => true,
            'message' => 'Scout raporu güncellendi.',
            'data' => $report,
        ]);
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $report = ScoutReport::findOrFail($id);

        if ($report->scout_user_id !== $request->user()->id) {
            return response()->json([
                'ok' => false,
                'message' => 'Bu raporu silme yetkiniz yok.',
            ], 403);
        }

        $report->delete();

        return response()->json([
            'ok' => true,
            'message' => 'Scout raporu silindi.',
        ]);
    }
}
