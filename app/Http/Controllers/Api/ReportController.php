<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'reported_user_id' => ['nullable', 'exists:users,id'],
            'reported_entity_type' => ['nullable', 'string', 'max:50'],
            'reported_entity_id' => ['nullable', 'integer'],
            'reason' => ['required', 'in:spam,inappropriate,fake_profile,harassment,other'],
            'description' => ['nullable', 'string', 'max:2000'],
        ]);

        $report = Report::create([
            'reporter_user_id' => $request->user()->id,
            ...$validated,
            'status' => 'pending',
        ]);

        return response()->json([
            'ok' => true,
            'message' => 'Şikayet gönderildi. İncelenecektir.',
            'data' => $report,
        ], 201);
    }

    public function myReports(Request $request): JsonResponse
    {
        $reports = Report::query()
            ->where('reporter_user_id', $request->user()->id)
            ->with(['reportedUser'])
            ->latest()
            ->paginate(20);

        return response()->json([
            'ok' => true,
            'data' => $reports,
        ]);
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $report = Report::where('id', $id)
            ->where('reporter_user_id', $request->user()->id)
            ->with(['reportedUser'])
            ->firstOrFail();

        return response()->json([
            'ok' => true,
            'data' => $report,
        ]);
    }
}
