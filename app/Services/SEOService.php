<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class SEOService
{
    /**
     * Generate SEO meta tags for player profile
     */
    public function generatePlayerMeta($player): array
    {
        $position = $player->position ?? 'Player';
        $team = $player->team->name ?? 'Free Agent';
        $sport = ucfirst($player->sport_type ?? 'Football');

        return [
            'title' => "{$player->name} - {$position} | NextScout {$sport} Profile",
            'description' => "Discover {$player->name}, professional {$position} playing for {$team}. View stats, videos, and scout reports on NextScout.",
            'keywords' => "{$player->name}, {$position}, {$team}, {$sport}, scout, transfer, player profile",
            'canonical_url' => url("/players/{$player->id}"),
            'og_title' => "{$player->name} - Professional {$position}",
            'og_description' => "Height: {$player->height}cm | Age: {$player->age} | {$team}",
            'og_image' => $player->avatar_url ?? url('/images/default-player.jpg'),
            'og_type' => 'profile',
            'twitter_card' => 'summary_large_image',
            'schema_markup' => $this->generatePlayerSchema($player),
        ];
    }

    /**
     * Generate Schema.org markup for player
     */
    public function generatePlayerSchema($player): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'Person',
            'name' => $player->name,
            'jobTitle' => $player->position ?? 'Professional Player',
            'height' => [
                '@type' => 'QuantitativeValue',
                'value' => $player->height,
                'unitCode' => 'CMT',
            ],
            'weight' => [
                '@type' => 'QuantitativeValue',
                'value' => $player->weight,
                'unitCode' => 'KGM',
            ],
            'birthDate' => $player->birth_date,
            'nationality' => $player->nationality,
            'memberOf' => [
                '@type' => 'SportsTeam',
                'name' => $player->team->name ?? null,
            ],
            'award' => $player->achievements ?? [],
            'image' => $player->avatar_url,
            'url' => url("/players/{$player->id}"),
        ];
    }

    /**
     * Generate SEO meta tags for team
     */
    public function generateTeamMeta($team): array
    {
        return [
            'title' => "{$team->name} - Official Team Profile | NextScout",
            'description' => "Explore {$team->name} roster, statistics, and scout reports. Founded {$team->founded_year}. {$team->league->name ?? ''}",
            'keywords' => "{$team->name}, football team, squad, roster, scout",
            'canonical_url' => url("/teams/{$team->id}"),
            'og_title' => $team->name,
            'og_description' => $team->description,
            'og_image' => $team->logo_url,
            'og_type' => 'website',
            'schema_markup' => $this->generateTeamSchema($team),
        ];
    }

    /**
     * Generate Schema.org markup for team
     */
    public function generateTeamSchema($team): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'SportsTeam',
            'name' => $team->name,
            'sport' => $team->sport_type ?? 'Football',
            'foundingDate' => $team->founded_year,
            'location' => [
                '@type' => 'Place',
                'address' => [
                    '@type' => 'PostalAddress',
                    'addressCountry' => $team->country,
                    'addressLocality' => $team->city,
                ],
            ],
            'logo' => $team->logo_url,
            'url' => url("/teams/{$team->id}"),
            'memberOf' => [
                '@type' => 'SportsOrganization',
                'name' => $team->league->name ?? null,
            ],
        ];
    }

    /**
     * Generate SEO meta tags for match
     */
    public function generateMatchMeta($match): array
    {
        $homeTeam = $match->homeTeam->name;
        $awayTeam = $match->awayTeam->name;
        $date = $match->match_date->format('M d, Y');

        return [
            'title' => "{$homeTeam} vs {$awayTeam} - {$date} | NextScout",
            'description' => "Watch live updates, stats and highlights for {$homeTeam} vs {$awayTeam} on {$date}.",
            'keywords' => "{$homeTeam}, {$awayTeam}, live match, football, score",
            'canonical_url' => url("/matches/{$match->id}"),
            'og_title' => "{$homeTeam} vs {$awayTeam}",
            'og_description' => "Live match coverage on NextScout",
            'og_image' => url('/images/match-preview.jpg'),
            'og_type' => 'article',
            'schema_markup' => $this->generateMatchSchema($match),
        ];
    }

    /**
     * Generate Schema.org markup for match
     */
    public function generateMatchSchema($match): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'SportsEvent',
            'name' => "{$match->homeTeam->name} vs {$match->awayTeam->name}",
            'startDate' => $match->match_date->toIso8601String(),
            'location' => [
                '@type' => 'Place',
                'name' => $match->stadium ?? $match->homeTeam->stadium,
            ],
            'competitor' => [
                [
                    '@type' => 'SportsTeam',
                    'name' => $match->homeTeam->name,
                ],
                [
                    '@type' => 'SportsTeam',
                    'name' => $match->awayTeam->name,
                ],
            ],
            'sport' => $match->sport_type ?? 'Football',
        ];
    }

    /**
     * Save SEO meta to database
     */
    public function saveMeta(string $pageType, int $pageId, array $meta)
    {
        DB::table('seo_meta')->updateOrInsert(
            ['page_type' => $pageType, 'page_id' => $pageId],
            array_merge($meta, [
                'schema_markup' => json_encode($meta['schema_markup'] ?? []),
                'updated_at' => now(),
                'created_at' => DB::raw('COALESCE(created_at, NOW())'),
            ])
        );
    }

    /**
     * Get SEO meta from database
     */
    public function getMeta(string $pageType, int $pageId): ?array
    {
        $meta = DB::table('seo_meta')
            ->where('page_type', $pageType)
            ->where('page_id', $pageId)
            ->first();

        if (!$meta) {
            return null;
        }

        return [
            'title' => $meta->title,
            'description' => $meta->description,
            'keywords' => $meta->keywords,
            'canonical_url' => $meta->canonical_url,
            'og_title' => $meta->og_title,
            'og_description' => $meta->og_description,
            'og_image' => $meta->og_image,
            'og_type' => $meta->og_type,
            'twitter_card' => $meta->twitter_card,
            'twitter_title' => $meta->twitter_title,
            'twitter_description' => $meta->twitter_description,
            'twitter_image' => $meta->twitter_image,
            'schema_markup' => json_decode($meta->schema_markup, true),
        ];
    }

    /**
     * Generate sitemap XML
     */
    public function generateSitemap(): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

        // Add homepage
        $xml .= $this->addSitemapUrl(url('/'), '1.0', 'daily');

        // Add player profiles
        $players = DB::table('players')->where('is_active', true)->get(['id', 'updated_at']);
        foreach ($players as $player) {
            $xml .= $this->addSitemapUrl(
                url("/players/{$player->id}"),
                '0.8',
                'weekly',
                $player->updated_at
            );
        }

        // Add team pages
        $teams = DB::table('teams')->where('is_active', true)->get(['id', 'updated_at']);
        foreach ($teams as $team) {
            $xml .= $this->addSitemapUrl(
                url("/teams/{$team->id}"),
                '0.7',
                'weekly',
                $team->updated_at
            );
        }

        // Add match pages (recent ones)
        $matches = DB::table('matches')
            ->where('match_date', '>=', now()->subMonths(3))
            ->get(['id', 'updated_at']);
        foreach ($matches as $match) {
            $xml .= $this->addSitemapUrl(
                url("/matches/{$match->id}"),
                '0.6',
                'never',
                $match->updated_at
            );
        }

        $xml .= '</urlset>';

        return $xml;
    }

    /**
     * Add URL to sitemap
     */
    protected function addSitemapUrl(string $url, string $priority, string $changefreq, $lastmod = null): string
    {
        $xml = '<url>';
        $xml .= "<loc>{$url}</loc>";
        if ($lastmod) {
            $xml .= '<lastmod>' . date('Y-m-d', strtotime($lastmod)) . '</lastmod>';
        }
        $xml .= "<changefreq>{$changefreq}</changefreq>";
        $xml .= "<priority>{$priority}</priority>";
        $xml .= '</url>';

        return $xml;
    }

    /**
     * Generate robots.txt
     */
    public function generateRobotsTxt(): string
    {
        return "User-agent: *\n" .
               "Allow: /\n" .
               "Disallow: /admin/\n" .
               "Disallow: /api/\n" .
               "Disallow: /private/\n" .
               "\n" .
               "Sitemap: " . url('/sitemap.xml') . "\n";
    }
}
