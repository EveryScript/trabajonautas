<?php

namespace App\Services\Bot;

use Illuminate\Support\Str;

class ETalentScraperService extends EvaluarScraperService
{
    public function getFeeds(string $url): array
    {
        $url = $this->assertSafeJobBoardUrl($url, resolveDns: false);
        $parts = parse_url($url);
        $query = [];
        parse_str((string) ($parts['query'] ?? ''), $query);

        $keywords = Str::of((string) ($query['search_keywords'] ?? ''))
            ->replaceMatches('/\s+/', ' ')
            ->trim()
            ->toString();

        if ($keywords === '') {
            throw new \InvalidArgumentException(
                'La URL de E-Talent debe incluir el parámetro search_keywords.',
            );
        }

        $scheme = strtolower((string) ($parts['scheme'] ?? 'https'));
        $host = strtolower(rtrim((string) ($parts['host'] ?? ''), '.'));
        $perPage = max(10, min(100, (int) config('services.etalent.feed_per_page', 100)));
        $feedQuery = http_build_query([
            'feed' => 'job_feed',
            'search_keywords' => $keywords,
            'posts_per_page' => $perPage,
        ], '', '&', PHP_QUERY_RFC3986);

        return ["{$scheme}://{$host}/?{$feedQuery}"];
    }
}
