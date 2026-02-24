<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Media\StoreMediaRequest;
use App\Models\Media;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class MediaController extends Controller
{
    public function store(StoreMediaRequest $request): JsonResponse
    {
        $file = $request->file('file');
        $mime = (string) $file->getMimeType();
        $type = str_starts_with($mime, 'image/') ? 'image' : 'video';

        $path = $file->store('media/'.$request->user()->id, 'public');

        $media = Media::query()->create([
            'user_id' => (int) $request->user()->id,
            'type' => $type,
            'url' => Storage::disk('public')->url($path),
            'thumb_url' => null,
            'title' => $request->validated('title'),
        ]);

        return response()->json([
            'ok' => true,
            'message' => 'Media yuklendi.',
            'data' => $media,
        ], Response::HTTP_CREATED);
    }

    public function indexByUser(int $id): JsonResponse
    {
        $media = Media::query()
            ->where('user_id', $id)
            ->orderByDesc('created_at')
            ->paginate(20);

        return response()->json([
            'ok' => true,
            'data' => $media,
        ]);
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $media = Media::query()->find($id);

        if (!$media) {
            return response()->json([
                'ok' => false,
                'message' => 'Media bulunamadi.',
            ], Response::HTTP_NOT_FOUND);
        }

        $this->authorize('delete', $media);

        $path = preg_replace('#^/storage/#', '', parse_url($media->url, PHP_URL_PATH) ?? '');
        if (is_string($path) && $path !== '') {
            Storage::disk('public')->delete($path);
        }

        $media->delete();

        return response()->json([
            'ok' => true,
            'message' => 'Media silindi.',
        ]);
    }
}
