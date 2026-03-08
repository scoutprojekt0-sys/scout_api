<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SuccessStory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SuccessStoryController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $limit = max(1, min((int) $request->integer('limit', 10), 50));
        $items = SuccessStory::query()
            ->where('status', 'approved')
            ->latest('approved_at')
            ->latest('id')
            ->limit($limit)
            ->get([
                'id',
                'full_name',
                'sport',
                'old_club',
                'new_club',
                'story_text',
                'image_url',
                'approved_at',
                'created_at',
            ]);

        return response()->json([
            'ok' => true,
            'data' => $items,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:150'],
            'sport' => ['required', 'string', 'max:50'],
            'old_club' => ['nullable', 'string', 'max:150'],
            'new_club' => ['nullable', 'string', 'max:150'],
            'story_text' => ['required', 'string', 'max:2500'],
            'image_url' => ['nullable', 'url', 'max:500'],
        ]);

        $item = SuccessStory::query()->create([
            'user_id' => (int) $request->user()->id,
            'full_name' => $validated['full_name'],
            'sport' => $validated['sport'],
            'old_club' => $validated['old_club'] ?? null,
            'new_club' => $validated['new_club'] ?? null,
            'story_text' => $validated['story_text'],
            'image_url' => $validated['image_url'] ?? null,
            'status' => 'pending',
        ]);

        return response()->json([
            'ok' => true,
            'message' => 'Basari hikayen admin onayina gonderildi.',
            'data' => $item,
        ], 201);
    }

    public function adminIndex(Request $request): JsonResponse
    {
        $status = (string) $request->query('status', 'pending');
        $allowed = ['pending', 'approved', 'rejected', 'all'];
        if (!in_array($status, $allowed, true)) {
            $status = 'pending';
        }

        $query = SuccessStory::query()->latest();
        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $items = $query->limit(200)->get();

        return response()->json([
            'ok' => true,
            'data' => $items,
        ]);
    }

    public function adminModerate(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:approved,rejected'],
            'admin_note' => ['nullable', 'string', 'max:1000'],
        ]);

        $story = SuccessStory::query()->findOrFail($id);
        $story->status = $validated['status'];
        $story->admin_note = $validated['admin_note'] ?? null;
        $story->approved_by = (int) $request->user()->id;
        $story->approved_at = now();
        $story->save();

        return response()->json([
            'ok' => true,
            'message' => 'Basari hikayesi durumu guncellendi.',
            'data' => $story,
        ]);
    }
}
