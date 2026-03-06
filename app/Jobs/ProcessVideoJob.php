<?php

namespace App\Jobs;

use App\Models\Video;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessVideoJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(private readonly int $videoId)
    {
    }

    public function handle(): void
    {
        $video = Video::query()->find($this->videoId);
        if (!$video) {
            return;
        }

        $video->update(['status' => 'processing']);

        // Placeholder pipeline:
        // In production this should call FFmpeg/transcoder service and upload CDN variants.
        $video->update([
            'status' => 'ready',
            'transcoded_urls' => [
                'source' => $video->original_path,
            ],
            'metadata' => array_merge((array) $video->metadata, [
                'processed_at' => now()->toIso8601String(),
            ]),
        ]);
    }
}
