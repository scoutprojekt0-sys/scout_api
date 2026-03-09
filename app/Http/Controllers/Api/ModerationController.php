<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DataAuditLog;
use App\Models\ModerationQueue;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ModerationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = ModerationQueue::query()
            ->with(['submitter:id,name,email', 'reviewer:id,name,email'])
            ->orderBy('priority', 'desc')
            ->orderBy('submitted_at', 'asc');

        if ($request->has('status')) {
            $query->where('status', $request->status);
        } else {
            $query->where('status', 'pending');
        }

        if ($request->has('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->has('model_type')) {
            $query->where('model_type', $request->model_type);
        }

        $items = $query->paginate($request->per_page ?? 20);

        return response()->json([
            'ok' => true,
            'data' => $items,
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $item = ModerationQueue::with([
            'submitter:id,name,email',
            'reviewer:id,name,email',
            'secondReviewer:id,name,email'
        ])->findOrFail($id);

        return response()->json([
            'ok' => true,
            'data' => $item,
        ]);
    }

    public function approve(Request $request, int $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'notes' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'ok' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $item = ModerationQueue::findOrFail($id);
        $userId = auth()->id();

        if ($item->approve($userId, $request->notes)) {
            // Log the approval
            DataAuditLog::logChange(
                $item->model_type,
                $item->model_id,
                'verified',
                $item->current_values,
                $item->proposed_changes,
                $userId,
                'Approved via moderation queue'
            );

            return response()->json([
                'ok' => true,
                'message' => 'Item approved successfully',
                'data' => $item->fresh(),
            ]);
        }

        return response()->json([
            'ok' => true,
            'message' => 'First approval recorded, awaiting second approval',
            'data' => $item->fresh(),
        ]);
    }

    public function reject(Request $request, int $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'reason' => 'required|string|min:10|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'ok' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $item = ModerationQueue::findOrFail($id);
        $userId = auth()->id();

        $item->reject($userId, $request->reason);

        DataAuditLog::logChange(
            $item->model_type,
            $item->model_id,
            'rejected',
            null,
            null,
            $userId,
            'Rejected: ' . $request->reason
        );

        return response()->json([
            'ok' => true,
            'message' => 'Item rejected successfully',
            'data' => $item->fresh(),
        ]);
    }

    public function flag(Request $request, int $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'reason' => 'required|string|min:10|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'ok' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $item = ModerationQueue::findOrFail($id);
        $userId = auth()->id();

        $item->flag($userId, $request->reason);

        return response()->json([
            'ok' => true,
            'message' => 'Item flagged for review',
            'data' => $item->fresh(),
        ]);
    }

    public function stats(): JsonResponse
    {
        $stats = [
            'pending_count' => ModerationQueue::where('status', 'pending')->count(),
            'by_priority' => [
                'critical' => ModerationQueue::where('status', 'pending')
                    ->where('priority', 'critical')->count(),
                'high' => ModerationQueue::where('status', 'pending')
                    ->where('priority', 'high')->count(),
                'medium' => ModerationQueue::where('status', 'pending')
                    ->where('priority', 'medium')->count(),
                'low' => ModerationQueue::where('status', 'pending')
                    ->where('priority', 'low')->count(),
            ],
            'flagged_count' => ModerationQueue::where('status', 'flagged')->count(),
            'oldest_pending' => ModerationQueue::where('status', 'pending')
                ->orderBy('submitted_at', 'asc')
                ->first(['id', 'submitted_at', 'model_type']),
            'approved_today' => ModerationQueue::where('status', 'approved')
                ->whereDate('reviewed_at', today())->count(),
            'rejected_today' => ModerationQueue::where('status', 'rejected')
                ->whereDate('reviewed_at', today())->count(),
        ];

        return response()->json([
            'ok' => true,
            'data' => $stats,
        ]);
    }
}
