<?php

namespace App\Services;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;

class ExternalNewsFeedService
{
    public function fetchTurkey(int $limit = 8): array
    {
        $timeout = (int) env('NEWS_FEED_TIMEOUT', 7);
        $feeds = [
            ['url' => 'https://www.ntvspor.net/rss', 'source' => 'NTV Spor'],
            ['url' => 'https://www.fanatik.com.tr/rss', 'source' => 'Fanatik'],
            ['url' => 'https://www.fotomac.com.tr/rss/anasayfa.xml', 'source' => 'Fotomac'],
        ];

        return $this->collectFromFeeds($feeds, $timeout, $limit);
    }

    public function fetch(int $limit = 8): array
    {
        $timeout = (int) env('NEWS_FEED_TIMEOUT', 7);
        $feeds = $this->resolveFeedUrls();

        return $this->collectFromFeeds($feeds, $timeout, $limit);
    }

    private function collectFromFeeds(array $feeds, int $timeout, int $limit): array
    {
        if (empty($feeds)) {
            return [];
        }

        $items = [];
        $seenTitles = [];
        $maxAgeHours = (int) env('NEWS_FEED_MAX_AGE_HOURS', 24);
        $minTimestamp = now()->subHours(max(1, $maxAgeHours))->timestamp;

        foreach ($feeds as $feed) {
            $url = (string) ($feed['url'] ?? '');
            $source = (string) ($feed['source'] ?? 'Turkiye Spor');
            if ($url === '') {
                continue;
            }

            $batch = $this->fetchSingleFeed($url, $source, $timeout, $limit);
            foreach ($batch as $row) {
                $titleKey = mb_strtolower(trim((string) ($row['title'] ?? '')));
                if ($titleKey === '' || isset($seenTitles[$titleKey])) {
                    continue;
                }
                $publishedTs = strtotime((string) ($row['published_at'] ?? ''));
                if ($publishedTs !== false && $publishedTs < $minTimestamp) {
                    continue;
                }
                $seenTitles[$titleKey] = true;
                $items[] = $row;
            }
        }

        usort($items, static function (array $a, array $b): int {
            return strcmp((string) ($b['published_at'] ?? ''), (string) ($a['published_at'] ?? ''));
        });

        return array_slice($items, 0, $limit);
    }

    private function resolveFeedUrls(): array
    {
        $custom = trim((string) env('NEWS_FEED_URLS', ''));
        if ($custom !== '') {
            $urls = array_values(array_filter(array_map('trim', explode(',', $custom))));
            return array_map(static fn (string $url): array => [
                'url' => $url,
                'source' => 'Turkiye Spor',
            ], $urls);
        }

        $single = trim((string) env('NEWS_FEED_URL', ''));
        if ($single !== '') {
            return [[
                'url' => $single,
                'source' => (string) env('NEWS_FEED_SOURCE', 'Turkiye Spor'),
            ]];
        }

        // Varsayilan Turkiye kaynaklari
        return [
            ['url' => 'https://www.ntvspor.net/rss', 'source' => 'NTV Spor'],
            ['url' => 'https://www.fanatik.com.tr/rss', 'source' => 'Fanatik'],
            ['url' => 'https://www.fotomac.com.tr/rss/anasayfa.xml', 'source' => 'Fotomac'],
        ];
    }

    private function fetchSingleFeed(string $url, string $source, int $timeout, int $limit): array
    {
        try {
            $requestUrl = $url;
            $separator = str_contains($requestUrl, '?') ? '&' : '?';
            $requestUrl .= $separator . '_t=' . time();

            $response = Http::timeout($timeout)
                ->withHeaders([
                    'Cache-Control' => 'no-cache',
                    'Pragma' => 'no-cache',
                    'User-Agent' => 'NextScoutNewsBot/1.0',
                ])
                ->get($requestUrl);
            if (!$response->ok()) {
                return [];
            }

            $xml = @simplexml_load_string($response->body());
            if ($xml === false) {
                return [];
            }

            $items = [];

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
                    $image = $this->extractImageFromRssItem($item);

                    $items[] = [
                        'id' => crc32($title . $publishedAt . $link),
                        'title' => $title,
                        'source' => $source,
                        'published_at' => $this->normalizeDate($publishedAt),
                        'url' => $link,
                        'image_url' => $image,
                    ];
                }
                return $items;
            }

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
                        'id' => crc32($title . $publishedAt . $link),
                        'title' => $title,
                        'source' => $source,
                        'published_at' => $this->normalizeDate($publishedAt),
                        'url' => $link,
                        'image_url' => '',
                    ];
                }
                return $items;
            }

            return [];
        } catch (\Throwable) {
            return [];
        }
    }

    private function extractImageFromRssItem(\SimpleXMLElement $item): string
    {
        if (isset($item->enclosure)) {
            $attrs = $item->enclosure->attributes();
            $url = trim((string) ($attrs['url'] ?? ''));
            if ($url !== '') {
                return $url;
            }
        }

        $namespaces = $item->getNamespaces(true);
        if (isset($namespaces['media'])) {
            $media = $item->children($namespaces['media']);
            if (isset($media->thumbnail)) {
                $attrs = $media->thumbnail->attributes();
                $url = trim((string) ($attrs['url'] ?? ''));
                if ($url !== '') {
                    return $url;
                }
            }
            if (isset($media->content)) {
                $attrs = $media->content->attributes();
                $url = trim((string) ($attrs['url'] ?? ''));
                if ($url !== '') {
                    return $url;
                }
            }
        }

        return '';
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
