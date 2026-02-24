<?php

namespace App\Services;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;

class ExternalNewsFeedService
{
    public function fetch(int $limit = 5): array
    {
        $url = (string) env('NEWS_FEED_URL', 'https://feeds.bbci.co.uk/sport/football/rss.xml');
        $timeout = (int) env('NEWS_FEED_TIMEOUT', 6);
        $source = (string) env('NEWS_FEED_SOURCE', 'Football Feed');

        if ($url === '') {
            return [];
        }

        try {
            $response = Http::timeout($timeout)->get($url);
            if (!$response->ok()) {
                return [];
            }

            $xml = @simplexml_load_string($response->body());
            if ($xml === false) {
                return [];
            }

            $items = [];

            // RSS
            if (isset($xml->channel->item)) {
                foreach ($xml->channel->item as $item) {
                    if (count($items) >= $limit) {
                        break;
                    }

                    $title = trim((string) ($item->title ?? ''));
                    if ($title === '') {
                        continue;
                    }

                    $publishedAt = trim((string) ($item->pubDate ?? ''));
                    $link = trim((string) ($item->link ?? ''));

                    $items[] = [
                        'id' => crc32($title.$publishedAt.$link),
                        'title' => $title,
                        'source' => $source,
                        'published_at' => $this->normalizeDate($publishedAt),
                        'url' => $link,
                    ];
                }

                return $items;
            }

            // Atom
            if (isset($xml->entry)) {
                foreach ($xml->entry as $entry) {
                    if (count($items) >= $limit) {
                        break;
                    }

                    $title = trim((string) ($entry->title ?? ''));
                    if ($title === '') {
                        continue;
                    }

                    $publishedAt = trim((string) ($entry->updated ?? $entry->published ?? ''));
                    $link = '';
                    if (isset($entry->link)) {
                        $attrs = $entry->link->attributes();
                        $link = trim((string) ($attrs['href'] ?? ''));
                    }

                    $items[] = [
                        'id' => crc32($title.$publishedAt.$link),
                        'title' => $title,
                        'source' => $source,
                        'published_at' => $this->normalizeDate($publishedAt),
                        'url' => $link,
                    ];
                }

                return $items;
            }

            return [];
        } catch (\Throwable) {
            return [];
        }
    }

    private function normalizeDate(string $raw): string
    {
        if ($raw === '') {
            return now()->toISOString();
        }

        try {
            return Carbon::parse($raw)->toISOString();
        } catch (\Throwable) {
            return now()->toISOString();
        }
    }
}
