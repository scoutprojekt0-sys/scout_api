<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessVideoJob;
use App\Models\Video;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class VideoController extends Controller
{
    /**
     * Upload video
     */
    public function upload(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:200',
            'description' => 'nullable|string|max:2000',
            'video' => 'required|file|mimes:mp4,mov,avi,wmv|max:102400', // 100MB
            'visibility' => 'in:public,private,unlisted',
        ]);

        $user = $request->user();
        $file = $request->file('video');

        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('videos/' . $user->id, $filename, 'public');

        $video = Video::create([
            'user_id' => $user->id,
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'filename' => $filename,
            'original_path' => $path,
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'status' => 'uploading',
            'visibility' => $validated['visibility'] ?? 'public',
        ]);

        ProcessVideoJob::dispatch((int) $video->id);

        return response()->json([
            'ok' => true,
            'message' => 'Video yüklendi',
            'data' => $video,
        ], 201);
    }

    /**
     * Get videos feed
     */
    public function index(Request $request): JsonResponse
    {
        $query = Video::with('user')
            ->where('status', 'ready')
            ->where('visibility', 'public')
            ->orderByDesc('created_at');

        $videos = $query->paginate(20);

        return response()->json([
            'ok' => true,
            'data' => $videos,
        ]);
    }

    /**
     * Get single video
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $video = Video::with('user')->findOrFail($id);

        // Increment views
        $video->incrementViews();

        return response()->json([
            'ok' => true,
            'data' => $video,
        ]);
    }

    /**
     * Delete video
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $video = Video::where('user_id', $request->user()->id)
            ->findOrFail($id);

        // Delete file from storage
        if (Storage::disk('public')->exists($video->original_path)) {
            Storage::disk('public')->delete($video->original_path);
        }

        $video->delete();

        return response()->json([
            'ok' => true,
            'message' => 'Video silindi',
        ]);
    }
}
