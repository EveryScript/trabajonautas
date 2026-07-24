<?php

namespace App\Services\Bot;

use App\Models\Announcement;
use App\Models\BotCompany;
use App\Models\BotVacancyPreview;
use App\Services\ProfessionAssignmentService;
use App\Support\SensitiveDataSanitizer;
use App\Support\TlsVerification;
use Carbon\Carbon;
use Illuminate\Http\Client\Response as HttpResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;

class EvaluarScraperService
{
    public function __construct(
        private GeminiVacancyAnalyzer $analyzer,
        private BotVacancyNormalizer $normalizer,
        private ProfessionAssignmentService $professionAssignments,
    ) {}

    public function scrapeCompany(BotCompany $company, ?string $startDate = null, ?string $endDate = null, ?string $batchId = null): array
    {
        $from = $startDate ? Carbon::parse($startDate)->startOfDay() : now()->subDays(15)->startOfDay();
        $to = $endDate ? Carbon::parse($endDate)->endOfDay() : now()->endOfDay();

        $summary = [
            'company' => $company->name,
            'feed_url' => null,
            'feeds_tested' => [],
            'status' => 'NO_FEED',
            'total_items_feed' => 0,
            'saved' => 0,
            'updated' => 0,
            'shown_in_batch' => 0,
            'skipped_internship' => 0,
            'skipped_out_of_range' => 0,
            'skipped_out_of_range_details' => [],
            'already_published' => 0,
            'already_previewed' => 0,
            'reactivated_deleted' => 0,
            'removed_from_batch' => 0,
            'gemini_enabled' => (bool) config('services.gemini.key'),
            'gemini_model' => config('services.gemini.model', 'gemini-2.5-flash-lite'),
            'gemini_errors' => 0,
            'gemini_calls' => 0,
            'gemini_skipped_existing_preview' => 0,
            'gemini_prompt_tokens' => 0,
            'gemini_candidates_tokens' => 0,
            'gemini_total_tokens' => 0,
            'gemini_thoughts_tokens' => 0,
            'gemini_retries' => 0,
            'gemini_processed' => 0,
            'gemini_errors_by_type' => [],
            'gemini_quota_exceeded' => false,
            'errors' => [],
        ];
        $batchState = [
            'gemini_quota_exceeded' => false,
        ];

        try {
            $feedUrls = $this->getFeeds($company->evaluar_url);
        } catch (\Throwable $exception) {
            $summary['status'] = 'ERROR';
            $safeException = SensitiveDataSanitizer::exception($exception, 300);
            $this->appendSummaryError($summary, $safeException['message']);
            Log::warning('BOT Evaluar no pudo descubrir feeds.', [
                'company_id' => $company->id,
                ...$safeException,
            ]);

            return $summary;
        }

        foreach ($feedUrls as $feedUrl) {
            $feedReport = [
                'url' => SensitiveDataSanitizer::url($feedUrl, 1000),
                'status_code' => null,
                'parsed_xml' => false,
                'items_count' => 0,
                'error' => null,
            ];

            try {
                $response = $this->evaluarGet($feedUrl, [
                    'User-Agent' => 'Mozilla/5.0',
                    'Accept' => 'application/rss+xml, application/xml, text/xml, */*',
                ]);

                $feedReport['status_code'] = $response->status();

                if (!$response->successful()) {
                    $summary['feeds_tested'][] = $feedReport;
                    continue;
                }

                $items = $this->parseItemsFromXml($response->body());
                $feedReport['parsed_xml'] = true;
                $feedReport['items_count'] = count($items);
                $summary['feeds_tested'][] = $feedReport;

                if (!$items) {
                    $summary['status'] = 'NO_ITEMS';
                    continue;
                }

                $summary['feed_url'] = $feedUrl;
                $summary['status'] = 'OK';
                $summary['total_items_feed'] = count($items);

                foreach ($items as $item) {
                    try {
                        $result = $this->processItem($company, $feedUrl, $item, $from, $to, $batchId, $batchState);
                        $resultStatus = is_array($result) ? ($result['status'] ?? null) : $result;

                        if ($resultStatus === 'saved') {
                            $summary['saved']++;
                        } elseif ($resultStatus === 'updated') {
                            $summary['updated']++;
                        } elseif ($resultStatus === 'already_previewed') {
                            $summary['already_previewed']++;
                        } elseif ($resultStatus === 'already_published') {
                            $summary['already_published']++;
                        } elseif ($resultStatus === 'reactivated_deleted') {
                            $summary['reactivated_deleted']++;
                        } elseif ($resultStatus === 'internship') {
                            $summary['skipped_internship']++;
                        } elseif ($resultStatus === 'out_of_range') {
                            $summary['skipped_out_of_range']++;
                            if (is_array($result) && isset($result['detail'])) {
                                $summary['skipped_out_of_range_details'][] = $result['detail'];
                            }
                        }

                        if (is_array($result) && !empty($result['gemini_skipped_existing_preview'])) {
                            $summary['gemini_skipped_existing_preview']++;
                        }

                        if (is_array($result) && !empty($result['gemini_used'])) {
                            $summary['gemini_calls']++;
                            $summary['gemini_prompt_tokens'] += (int) ($result['gemini_prompt_tokens'] ?? 0);
                            $summary['gemini_candidates_tokens'] += (int) ($result['gemini_candidates_tokens'] ?? 0);
                            $summary['gemini_total_tokens'] += (int) ($result['gemini_total_tokens'] ?? 0);
                            $summary['gemini_thoughts_tokens'] += (int) ($result['gemini_thoughts_tokens'] ?? 0);
                            $summary['gemini_retries'] += max(0, (int) ($result['gemini_attempts'] ?? 1) - 1);
                        }

                        if (is_array($result) && empty($result['gemini_skipped_existing_preview']) && array_key_exists('gemini_success', $result)) {
                            $summary['gemini_processed']++;

                            if (empty($result['gemini_success'])) {
                                $summary['gemini_errors']++;
                                $type = $result['gemini_error_type'] ?? 'unknown';
                                $summary['gemini_errors_by_type'][$type] = ($summary['gemini_errors_by_type'][$type] ?? 0) + 1;
                            }

                            if (($result['gemini_error_type'] ?? null) === 'quota_exceeded') {
                                $summary['gemini_quota_exceeded'] = true;
                            }
                        }
                    } catch (\Throwable $exception) {
                        $safeException = SensitiveDataSanitizer::exception($exception, 300);
                        $this->appendSummaryError($summary, $safeException['message']);
                        Log::warning('BOT Evaluar no pudo procesar un item.', [
                            'company_id' => $company->id,
                            'feed_url' => SensitiveDataSanitizer::url($feedUrl, 1000),
                            ...$safeException,
                        ]);
                    }
                }

                break;
            } catch (\Throwable $exception) {
                $safeException = SensitiveDataSanitizer::exception($exception, 300);
                $feedReport['error'] = $safeException['message'];
                $summary['feeds_tested'][] = $feedReport;
                $this->appendSummaryError(
                    $summary,
                    (SensitiveDataSanitizer::url($feedUrl, 1000) ?: 'feed').': '.$safeException['message'],
                );
                $summary['status'] = 'ERROR';
                Log::warning('BOT Evaluar no pudo leer un feed.', [
                    'company_id' => $company->id,
                    'feed_url' => SensitiveDataSanitizer::url($feedUrl, 1000),
                    ...$safeException,
                ]);
            }
        }

        if ($batchId) {
            $summary['shown_in_batch'] = BotVacancyPreview::where('bot_company_id', $company->id)
                ->where('scrape_batch_id', $batchId)
                ->whereNull('removed_from_batch_at')
                ->whereIn('status', ['preview', 'edited', 'error'])
                ->count();
        }

        return $summary;
    }

    public function getFeeds(string $url): array
    {
        $url = $this->assertSafeEvaluarUrl($url, resolveDns: false);
        $parts = parse_url($url);
        $scheme = strtolower((string) $parts['scheme']);
        $host = strtolower(rtrim((string) $parts['host'], '.'));

        $origin = rtrim("{$scheme}://{$host}", '/');
        $path = trim($parts['path'] ?? '', '/');

        $feeds = [
            "{$origin}/job-listings/feed/",
        ];

        if ($path !== '') {
            $feeds[] = "{$origin}/{$path}/feed/";
            $feeds[] = "{$origin}/{$path}/job-listings/feed/";
        }

        return array_values(array_unique($feeds));
    }

    public function feedUrls(string $url): array
    {
        return $this->getFeeds($url);
    }

    private function evaluarGet(string $url, array $headers): HttpResponse
    {
        $url = $this->assertSafeEvaluarUrl($url);
        $maxRedirects = (int) config('services.evaluar.max_redirects', 3);
        $redirectOptions = $maxRedirects > 0
            ? [
                'max' => $maxRedirects,
                'strict' => true,
                'referer' => false,
                'protocols' => ['https'],
                'track_redirects' => false,
                'on_redirect' => function (
                    RequestInterface $request,
                    ResponseInterface $response,
                    UriInterface $uri,
                ): void {
                    $this->assertSafeEvaluarUrl((string) $uri);
                },
            ]
            : false;
        $verify = TlsVerification::option(
            'Evaluar',
            (bool) config('services.evaluar.verify_ssl', true),
            config('services.evaluar.ca_bundle'),
        );

        return Http::connectTimeout((int) config('services.evaluar.connect_timeout', 10))
            ->timeout((int) config('services.evaluar.timeout', 30))
            ->withHeaders($headers)
            ->withOptions([
                'verify' => $verify,
                'allow_redirects' => $redirectOptions,
            ])
            ->get($url);
    }

    private function assertSafeEvaluarUrl(string $url, bool $resolveDns = true): string
    {
        $url = trim($url);

        if (
            $url === ''
            || strlen($url) > 2048
            || preg_match('/[\x00-\x1F\x7F]/', $url)
            || filter_var($url, FILTER_VALIDATE_URL) === false
        ) {
            throw new \InvalidArgumentException('La URL de Evaluar no tiene un formato seguro.');
        }

        $parts = parse_url($url);
        $scheme = strtolower((string) ($parts['scheme'] ?? ''));
        $host = strtolower(rtrim((string) ($parts['host'] ?? ''), '.'));
        $port = isset($parts['port']) ? (int) $parts['port'] : null;

        if ($scheme !== 'https') {
            throw new \InvalidArgumentException('Las URLs de Evaluar deben usar HTTPS.');
        }

        if (
            $host === ''
            || isset($parts['user'])
            || isset($parts['pass'])
            || isset($parts['fragment'])
            || ($port !== null && $port !== 443)
            || filter_var($host, FILTER_VALIDATE_IP) !== false
            || ! preg_match('/^(?=.{1,253}$)(?:[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?\.)+[a-z]{2,63}$/', $host)
        ) {
            throw new \InvalidArgumentException('El host de Evaluar no tiene un formato permitido.');
        }

        $allowed = collect(config('services.evaluar.allowed_host_suffixes', []))
            ->filter(fn (mixed $suffix): bool => is_string($suffix) && trim($suffix) !== '')
            ->map(fn (string $suffix): string => strtolower(trim($suffix, " .\t\n\r\0\x0B")))
            ->contains(fn (string $suffix): bool => $host === $suffix || str_ends_with($host, '.'.$suffix));

        if (! $allowed) {
            throw new \InvalidArgumentException('El host no pertenece a un portal Evaluar permitido.');
        }

        if ($resolveDns && ! app()->environment('testing')) {
            $this->assertPublicDns($host);
        }

        return $url;
    }

    private function assertPublicDns(string $host): void
    {
        $recordTypes = DNS_A | DNS_AAAA;
        $records = @dns_get_record($host, $recordTypes);
        $addresses = [];

        if (is_array($records)) {
            foreach ($records as $record) {
                if (isset($record['ip'])) {
                    $addresses[] = $record['ip'];
                }

                if (isset($record['ipv6'])) {
                    $addresses[] = $record['ipv6'];
                }
            }
        }

        $ipv4Addresses = @gethostbynamel($host);
        if (is_array($ipv4Addresses)) {
            $addresses = [...$addresses, ...$ipv4Addresses];
        }

        $addresses = array_values(array_unique(array_filter($addresses, 'is_string')));

        if ($addresses === []) {
            throw new \RuntimeException('No se pudo resolver el host del portal Evaluar.');
        }

        $publicFlags = FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE;

        foreach ($addresses as $address) {
            if (filter_var($address, FILTER_VALIDATE_IP, $publicFlags) === false) {
                throw new \RuntimeException('El host Evaluar resolvio a una red no permitida.');
            }
        }
    }

    private function parseItemsFromXml(string $xml): array
    {
        $previous = libxml_use_internal_errors(true);
        $dom = new \DOMDocument();
        $loaded = $dom->loadXML($xml, LIBXML_NOCDATA | LIBXML_NONET);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        if (!$loaded) {
            return [];
        }

        $xpath = new \DOMXPath($dom);
        $nodes = $xpath->query('//*[local-name()="item"]');

        if (!$nodes) {
            return [];
        }

        $items = [];
        foreach ($nodes as $node) {
            if ($node instanceof \DOMElement) {
                $items[] = $node;
            }
        }

        return $items;
    }

    private function processItem(
        BotCompany $company,
        string $feedUrl,
        \DOMElement $item,
        Carbon $from,
        Carbon $to,
        ?string $batchId,
        array &$batchState = []
    ): array {
        $title = $this->cleanText($this->childText($item, 'title'));
        $sourceUrl = $this->cleanText($this->childText($item, 'link'));
        $pubDateOriginal = $this->cleanText($this->childText($item, 'pubDate'));

        if (!$title || !$sourceUrl) {
            throw new \RuntimeException('RSS item sin titulo o link.');
        }
        $sourceUrl = $this->assertSafeEvaluarUrl($sourceUrl, resolveDns: false);

        $originalDescription = $this->cleanDescription($this->extractDescriptionFromItem($item));

        if ($this->isInternshipOrPractice($title, $originalDescription, $item->textContent)) {
            $this->removeUnpublishedInternshipPreview($sourceUrl);

            return ['status' => 'internship'];
        }

        $publishedAt = $this->parsePublishedAt($pubDateOriginal);
        if ($publishedAt && !$publishedAt->betweenIncluded($from, $to)) {
            return [
                'status' => 'out_of_range',
                'detail' => [
                    'title' => $title,
                    'pubDate_original' => $pubDateOriginal,
                    'reason' => 'fuera del rango',
                ],
            ];
        }

        $sourceHash = hash('sha256', $sourceUrl);
        $publishedPreview = BotVacancyPreview::where('source_url', $sourceUrl)
            ->where('status', 'published')
            ->first();

        if (
            $publishedPreview
            && $this->existingPublishedAnnouncementExists($sourceUrl, $sourceHash, $publishedPreview->convocatoria_id)
        ) {
            $this->markPublishedPreviewAsSeen($sourceUrl, [
                'feed_url' => $feedUrl,
                'pubDate_original' => $pubDateOriginal,
                'published_at' => $publishedAt?->toIso8601String(),
                'date_parse_error' => $pubDateOriginal !== '' && !$publishedAt,
                'scraped_at' => now()->toIso8601String(),
                'company_url' => $company->evaluar_url,
                'already_published_detected' => true,
            ]);

            return ['status' => 'already_published'];
        }

        if (!$publishedPreview && $this->existingPublishedAnnouncementExists($sourceUrl, $sourceHash)) {
            return ['status' => 'already_published'];
        }

        $contentHash = $this->contentHash($title, $company->name, $originalDescription);
        $existingPreview = $this->existingReusablePreview($company, $sourceUrl);

        if ($existingPreview) {
            return $this->skipExistingPreview(
                preview: $existingPreview,
                company: $company,
                feedUrl: $feedUrl,
                pubDateOriginal: $pubDateOriginal,
                publishedAt: $publishedAt,
                from: $from,
                to: $to,
                batchId: $batchId,
                contentHash: $contentHash,
            );
        }

        $xmlExpiration = $this->expirationFromXml($item);
        $descriptionExpiration = $this->expirationFromDescription($originalDescription);
        $pageExpiration = (!$xmlExpiration && !$descriptionExpiration)
            ? $this->expirationFromPage($sourceUrl)
            : null;

        $gemini = $this->analyzer->analyzeWithMeta($title, $company, $originalDescription, [
            'skip_due_to_quota' => !empty($batchState['gemini_quota_exceeded']),
        ]);

        if (($gemini['error_type'] ?? null) === 'quota_exceeded') {
            $batchState['gemini_quota_exceeded'] = true;
        }

        $this->logGeminiDecision($company, $title, 'called', [
            'success' => (bool) ($gemini['success'] ?? false),
            'error_type' => $gemini['error_type'] ?? null,
            'attempts' => $gemini['gemini_attempts'] ?? null,
            'total_tokens' => $gemini['total_tokens'] ?? null,
        ]);

        $analysis = $gemini['data'];
        $expirationCandidate = $xmlExpiration ?: $descriptionExpiration ?: $pageExpiration;
        $expirationSource = $xmlExpiration
            ? 'xml'
            : ($descriptionExpiration ? 'description' : ($pageExpiration ? 'page' : null));
        $analysisForNormalization = $analysis;

        if ($expirationCandidate) {
            $analysisForNormalization['expiration_date'] = $expirationCandidate;
        }

        $normalizedFields = $this->normalizer->normalize(
            title: $title,
            description: $originalDescription,
            analysis: $analysisForNormalization,
            rawData: [],
        );

        if ($expirationCandidate) {
            $normalizedFields['expiration_source'] = $expirationSource;
            $normalizedFields['expiration_detected_text'] = $expirationCandidate;
        }

        $areaAssignment = $this->professionAssignments->resolve($analysis['area_ids'] ?? []);
        $areaNames = $areaAssignment['area_names'];
        $professionNames = $areaAssignment['profession_names'];
        $previewStatus = !empty($gemini['success']) && $areaAssignment['valid'] ? 'preview' : 'error';

        $description = $this->trabajonautasDescription(
            $normalizedFields['location'],
            $sourceUrl,
            $company->evaluar_url,
            $normalizedFields['municipality'],
        );

        $rawData = [
            'feed_url' => $feedUrl,
            'pubDate_original' => $pubDateOriginal,
            'published_at' => $publishedAt?->toIso8601String(),
            'date_parse_error' => $pubDateOriginal !== '' && !$publishedAt,
            'scraped_at' => now()->toIso8601String(),
            'company_url' => $company->evaluar_url,
            'content_hash' => $contentHash,
            'scrape_range' => [
                'from' => $from->toDateString(),
                'to' => $to->toDateString(),
            ],
            'expiration_sources' => [
                'title' => $normalizedFields['expiration_source'] === 'title' ? $normalizedFields['expiration_detected_text'] : null,
                'xml' => $xmlExpiration,
                'description' => $descriptionExpiration,
                'page' => $pageExpiration,
                'gemini' => $analysis['expiration_date'] ?? null,
            ],
            'expiration_source' => $normalizedFields['expiration_source'],
            'expiration_detected_text' => $normalizedFields['expiration_detected_text'],
            'location_source' => $normalizedFields['location_source'],
            'location_detected_text' => $normalizedFields['location_detected_text'],
            'salary_source' => $normalizedFields['salary_source'],
            'salary_detected_text' => $normalizedFields['salary_detected_text'],
            'gemini_used' => (bool) ($gemini['used'] ?? false),
            'gemini_success' => (bool) ($gemini['success'] ?? false),
            'gemini_model' => $gemini['model'] ?? config('services.gemini.model', 'gemini-2.5-flash-lite'),
            'gemini_error' => $gemini['error'] ?? null,
            'gemini_error_type' => $gemini['error_type'] ?? null,
            'gemini_http_status' => $gemini['http_status'] ?? null,
            'gemini_attempts' => $gemini['gemini_attempts'] ?? null,
            'gemini_analyzed_at' => $gemini['analyzed_at'] ?? null,
            'gemini_usage_metadata' => $gemini['usage_metadata'] ?? null,
            'gemini_prompt_tokens' => $gemini['prompt_tokens'] ?? null,
            'gemini_candidates_tokens' => $gemini['candidates_tokens'] ?? null,
            'gemini_total_tokens' => $gemini['total_tokens'] ?? null,
            'gemini_thoughts_tokens' => $gemini['thoughts_tokens'] ?? null,
            'gemini_response_metadata' => $gemini['gemini_response_metadata'] ?? null,
            'gemini_skipped_due_to_quota' => (bool) ($gemini['gemini_skipped_due_to_quota'] ?? false),
            'description_truncated_for_gemini' => (bool) ($gemini['description_truncated_for_gemini'] ?? false),
            'description_original_length' => $gemini['description_original_length'] ?? null,
            'description_sent_length' => $gemini['description_sent_length'] ?? null,
            'areas' => $analysis['areas'] ?? [],
            'area_ids' => $analysis['area_ids'] ?? [],
            'gemini_areas' => $analysis['areas'] ?? [],
            'gemini_area_ids' => $analysis['area_ids'] ?? [],
            'gemini_area_principal' => $analysis['area_principal'] ?? null,
            'gemini_profesiones_sugeridas' => [],
            'raw_ai_areas' => $analysis['area_ids'] ?? [],
            'raw_ai_professions' => [],
            'resolved_area_ids' => $areaAssignment['area_ids'],
            'profession_assignment_source' => 'area_profession_pivot',
            'profession_assignment_error' => $areaAssignment['error'],
            'manual_review_required' => $previewStatus === 'error',
            'manual_review_reasons' => $previewStatus === 'error'
                ? array_values(array_filter([$gemini['error'] ?? null, $areaAssignment['error']]))
                : [],
            'municipality' => $normalizedFields['municipality'],
        ];

        $status = $this->savePreview($company, $sourceUrl, [
            'bot_company_id' => $company->id,
            'title' => Str::limit($title, 255, ''),
            'original_description' => $originalDescription,
            'description' => $description,
            'area' => $areaNames ? implode(', ', $areaNames) : 'No especificado',
            'professions' => $professionNames ? implode(', ', $professionNames) : 'No especificado',
            'department' => $normalizedFields['department'],
            'location' => $normalizedFields['location'],
            'expiration_date' => $normalizedFields['expiration_date'],
            'salary' => $normalizedFields['salary'],
            'raw_data' => $rawData,
            'status' => $previewStatus,
            'scrape_batch_id' => $batchId,
            'removed_from_batch_at' => null,
            'selected_area_id' => $areaAssignment['area_ids'][0] ?? null,
            'selected_profession_ids' => $areaAssignment['profession_ids'],
        ]);
        $preview = BotVacancyPreview::where('source_url', $sourceUrl)->first();

        $this->professionAssignments->logDecision([
            'source' => 'evaluar',
            'raw_ai_areas' => $analysis['area_ids'] ?? [],
            'raw_ai_professions' => [],
            'resolved_area_ids' => $areaAssignment['area_ids'],
            'professions_before' => [],
            'professions_from_areas' => $areaAssignment['profession_ids'],
            'professions_after' => $preview?->selected_profession_ids ?? [],
            'preview_id' => $preview?->id,
            'scrape_batch_id' => $batchId,
            'professions_edited_manually' => $preview ? $this->professionAssignments->professionsEditedManually($preview) : false,
        ]);

        return [
            'status' => $status,
            'gemini_used' => (bool) ($gemini['used'] ?? false),
            'gemini_success' => (bool) ($gemini['success'] ?? false),
            'gemini_error' => $gemini['error'] ?? null,
            'gemini_error_type' => $gemini['error_type'] ?? null,
            'gemini_attempts' => $gemini['gemini_attempts'] ?? null,
            'gemini_prompt_tokens' => $gemini['prompt_tokens'] ?? null,
            'gemini_candidates_tokens' => $gemini['candidates_tokens'] ?? null,
            'gemini_total_tokens' => $gemini['total_tokens'] ?? null,
            'gemini_thoughts_tokens' => $gemini['thoughts_tokens'] ?? null,
        ];
    }

    private function savePreview(BotCompany $company, string $sourceUrl, array $data): string
    {
        $existing = BotVacancyPreview::where('source_url', $sourceUrl)->first();

        if ($existing && $existing->status === 'published') {
            $sourceHash = hash('sha256', $sourceUrl);

            if ($this->existingPublishedAnnouncementExists($sourceUrl, $sourceHash, $existing->convocatoria_id)) {
                return 'already_published';
            }

            $existing->update(array_merge($data, [
                'status' => 'preview',
                'convocatoria_id' => null,
                'scrape_batch_id' => $data['scrape_batch_id'],
                'removed_from_batch_at' => null,
                'raw_data' => array_merge($existing->raw_data ?? [], $data['raw_data'], [
                    'reactivated_deleted_announcement' => true,
                    'reactivated_at' => now()->toIso8601String(),
                ]),
            ]));

            return 'reactivated_deleted';
        }

        if ($existing && $existing->status === 'edited') {
            $update = [
                'bot_company_id' => $company->id,
                'raw_data' => array_merge($existing->raw_data ?? [], $data['raw_data']),
                'scrape_batch_id' => $data['scrape_batch_id'],
                'removed_from_batch_at' => null,
            ];

            $update['original_description'] = $data['original_description'];

            $existing->update($update);

            return 'already_previewed';
        }

        BotVacancyPreview::updateOrCreate(
            ['source_url' => $sourceUrl],
            $data,
        );

        return $existing ? 'updated' : 'saved';
    }

    private function existingReusablePreview(BotCompany $company, string $sourceUrl): ?BotVacancyPreview
    {
        return BotVacancyPreview::query()
            ->where('bot_company_id', $company->id)
            ->where('source_url', $sourceUrl)
            ->whereIn('status', ['preview', 'edited', 'error'])
            ->first();
    }

    private function skipExistingPreview(
        BotVacancyPreview $preview,
        BotCompany $company,
        string $feedUrl,
        string $pubDateOriginal,
        ?Carbon $publishedAt,
        Carbon $from,
        Carbon $to,
        ?string $batchId,
        string $contentHash,
    ): array {
        $previousHash = data_get($preview->raw_data, 'content_hash');
        $rawData = array_merge($preview->raw_data ?? [], [
            'feed_url' => $feedUrl,
            'pubDate_original' => $pubDateOriginal,
            'published_at' => $publishedAt?->toIso8601String(),
            'date_parse_error' => $pubDateOriginal !== '' && !$publishedAt,
            'scraped_at' => now()->toIso8601String(),
            'company_url' => $company->evaluar_url,
            'scrape_range' => [
                'from' => $from->toDateString(),
                'to' => $to->toDateString(),
            ],
            'previous_content_hash' => $previousHash,
            'content_hash' => $contentHash,
            'content_hash_changed' => $previousHash && $previousHash !== $contentHash,
            'gemini_skipped_existing_preview' => true,
            'gemini_skip_reason' => 'existing_preview_before_analysis',
            'gemini_skipped_at' => now()->toIso8601String(),
        ]);
        $expirationCandidate = data_get($rawData, 'expiration_sources.xml')
            ?: data_get($rawData, 'expiration_sources.description')
            ?: data_get($rawData, 'expiration_sources.page');
        $expirationSource = data_get($rawData, 'expiration_sources.xml')
            ? 'xml'
            : (data_get($rawData, 'expiration_sources.description')
                ? 'description'
                : (data_get($rawData, 'expiration_sources.page') ? 'page' : null));

        if ($expirationCandidate && $preview->status !== 'edited') {
            $rawData['expiration_source'] = $expirationSource;
            $rawData['expiration_detected_text'] = $expirationCandidate;
        }

        $preview->update([
            'bot_company_id' => $company->id,
            'scrape_batch_id' => $batchId,
            'removed_from_batch_at' => null,
            'expiration_date' => $expirationCandidate && $preview->status !== 'edited'
                ? $this->normalizer->expirationForStorage((string) $expirationCandidate)
                : $preview->expiration_date,
            'raw_data' => $rawData,
        ]);
        $preview->refresh();
        $assignment = $this->areaAssignmentFromPreview($preview);
        $this->professionAssignments->applyToPreview($preview, $assignment, [
            'source' => 'evaluar_existing_preview',
            'raw_ai_areas' => data_get($preview->raw_data, 'gemini_area_ids', data_get($preview->raw_data, 'gemini_areas', [])),
            'raw_ai_professions' => data_get($preview->raw_data, 'gemini_profesiones_sugeridas', []),
        ]);

        $this->logGeminiDecision($company, $preview->title, 'skipped', [
            'skip_reason' => 'existing_preview_before_analysis',
            'preview_id' => $preview->id,
            'status' => $preview->status,
        ]);

        return [
            'status' => 'already_previewed',
            'gemini_used' => false,
            'gemini_skipped_existing_preview' => true,
            'gemini_skip_reason' => 'existing_preview_before_analysis',
        ];
    }

    private function areaAssignmentFromPreview(BotVacancyPreview $preview): array
    {
        $rawData = $preview->raw_data ?? [];
        $areaIds = data_get($rawData, 'resolved_area_ids')
            ?: data_get($rawData, 'gemini_area_ids')
            ?: data_get($rawData, 'area_ids')
            ?: data_get($rawData, 'ai_analysis.area_ids');

        if (is_array($areaIds) && $areaIds !== []) {
            return $this->professionAssignments->resolve($areaIds);
        }

        $areaNames = collect([
            ...((array) data_get($rawData, 'gemini_areas', [])),
            ...((array) data_get($rawData, 'areas', [])),
            data_get($rawData, 'gemini_area_principal'),
            data_get($rawData, 'ai_analysis.area_profesional_principal'),
            $preview->area,
        ])->filter(fn(mixed $name): bool => is_string($name) && trim($name) !== '')->values()->all();

        return $this->professionAssignments->resolveExactAreaNames($areaNames);
    }

    private function contentHash(string $title, string $company, string $description): string
    {
        return hash('sha256', implode('|', [
            $this->normalize($title),
            $this->normalize($company),
            $this->normalize($description),
        ]));
    }

    private function logGeminiDecision(BotCompany $company, string $title, string $status, array $context = []): void
    {
        Log::info('BOT Gemini analysis decision', SensitiveDataSanitizer::context([
            'company' => $company->name,
            'title' => Str::limit($title, 120, ''),
            'status' => $status,
            ...$context,
        ], 300, 4, 40));
    }

    private function existingPublishedAnnouncementExists(string $sourceUrl, ?string $sourceHash = null, mixed $convocatoriaId = null): bool
    {
        if ($convocatoriaId && Announcement::query()->whereKey($convocatoriaId)->exists()) {
            return true;
        }

        if ($sourceUrl && Announcement::query()->where('source_url', $sourceUrl)->exists()) {
            return true;
        }

        if ($sourceHash && Announcement::query()->where('source_hash', $sourceHash)->exists()) {
            return true;
        }

        return false;
    }

    private function markPublishedPreviewAsSeen(string $sourceUrl, array $rawData): void
    {
        $preview = BotVacancyPreview::where('source_url', $sourceUrl)
            ->where('status', 'published')
            ->first();

        if (!$preview) {
            return;
        }

        $preview->update([
            'raw_data' => array_merge($preview->raw_data ?? [], $rawData),
        ]);
    }

    private function childText(\DOMElement $item, string $localName): string
    {
        foreach ($item->childNodes as $child) {
            if ($child instanceof \DOMElement && strtolower($child->localName) === strtolower($localName)) {
                return trim($child->textContent);
            }
        }

        return '';
    }

    private function extractDescriptionFromItem(\DOMElement $item): string
    {
        $encoded = $this->childText($item, 'encoded');

        return $encoded !== '' ? $encoded : $this->childText($item, 'description');
    }

    private function expirationFromXml(\DOMElement $item): ?string
    {
        $fields = [
            'job_expires',
            '_job_expires',
            'expires',
            'expiration_date',
            'dateexpires',
            'validthrough',
            'application_deadline',
            'deadline',
            'closing_date',
            'fecha_vencimiento',
            'vencimiento',
        ];

        foreach ($fields as $field) {
            $value = $this->cleanText($this->childText($item, $field));
            if ($value !== '') {
                return $this->normalizeDateText($value) ?: $value;
            }
        }

        return null;
    }

    private function expirationFromDescription(string $description): ?string
    {
        $keywords = [
            'postular hasta',
            'fecha limite',
            'fecha limite',
            'recepcion de postulaciones',
            'recepcion de postulaciones',
            'fecha de cierre',
            'cierre',
            'hasta el',
            'fecha de vencimiento',
            'vencimiento',
            'limite de postulacion',
        ];

        $normalizedDescription = Str::of($description)->ascii()->toString();

        foreach ($keywords as $keyword) {
            if (preg_match('/' . preg_quote($keyword, '/') . '\s*:?\s*(?:el\s*)?([^\n\.]{3,100})/iu', $normalizedDescription, $matches)) {
                $candidate = trim($matches[1]);

                return $this->normalizeDateText($candidate) ?: $candidate;
            }
        }

        return null;
    }

    private function expirationFromPage(string $url): ?string
    {
        try {
            $response = $this->evaluarGet($url, [
                'User-Agent' => 'Mozilla/5.0',
                'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
            ]);

            if (!$response->successful()) {
                return null;
            }

            $html = $response->body();
            $jsonLd = $this->validThroughFromJsonLd($html);

            if ($jsonLd) {
                return $this->normalizeDateText($jsonLd) ?: $jsonLd;
            }

            return $this->expirationFromDescription($this->cleanDescription($html));
        } catch (\Throwable $exception) {
            Log::warning('BOT Evaluar no pudo obtener la fecha de expiracion.', [
                'url' => SensitiveDataSanitizer::url($url, 1000),
                ...SensitiveDataSanitizer::exception($exception, 300),
            ]);

            return null;
        }
    }

    private function validThroughFromJsonLd(string $html): ?string
    {
        $previous = libxml_use_internal_errors(true);
        $dom = new \DOMDocument();
        $loaded = $dom->loadHTML($html, LIBXML_NONET);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        if (!$loaded) {
            return null;
        }

        foreach ($dom->getElementsByTagName('script') as $script) {
            if (!Str::contains(strtolower($script->getAttribute('type')), 'ld+json')) {
                continue;
            }

            $decoded = json_decode(trim($script->textContent), true);
            $validThrough = $this->findValidThrough($decoded);

            if ($validThrough) {
                return $validThrough;
            }
        }

        return null;
    }

    private function findValidThrough(mixed $value): ?string
    {
        if (!is_array($value)) {
            return null;
        }

        $type = $value['@type'] ?? null;
        $typeText = is_array($type) ? implode(' ', $type) : (string) $type;

        if (isset($value['validThrough']) && (!$typeText || Str::contains(strtolower($typeText), 'jobposting'))) {
            return (string) $value['validThrough'];
        }

        foreach ($value as $child) {
            $found = $this->findValidThrough($child);
            if ($found) {
                return $found;
            }
        }

        return null;
    }

    private function parsePublishedAt(?string $pubDate): ?Carbon
    {
        $pubDate = $this->cleanText((string) $pubDate);

        if ($pubDate === '') {
            return null;
        }

        $parsers = [
            fn() => Carbon::parse($pubDate),
            fn() => Carbon::createFromFormat('D, d M Y H:i:s O', $pubDate),
            fn() => Carbon::createFromFormat('D, d M Y H:i:s T', $pubDate),
        ];

        foreach ($parsers as $parser) {
            try {
                return $parser();
            } catch (\Throwable) {
                continue;
            }
        }

        return null;
    }

    private function normalizeDateText(string $value): ?string
    {
        $value = $this->cleanText($value);

        if ($value === '' || $this->normalize($value) === 'no especificado') {
            return null;
        }

        if (preg_match('/\b(\d{4})-(\d{1,2})-(\d{1,2})\b/', $value, $matches)) {
            return Carbon::create((int) $matches[1], (int) $matches[2], (int) $matches[3])->toDateString();
        }

        if (preg_match('/\b(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{2,4})\b/', $value, $matches)) {
            $year = (int) $matches[3];
            $year = $year < 100 ? 2000 + $year : $year;

            return Carbon::create($year, (int) $matches[2], (int) $matches[1])->toDateString();
        }

        $ascii = Str::of($value)->ascii()->lower()->toString();
        $months = [
            'enero' => 1,
            'febrero' => 2,
            'marzo' => 3,
            'abril' => 4,
            'mayo' => 5,
            'junio' => 6,
            'julio' => 7,
            'agosto' => 8,
            'septiembre' => 9,
            'setiembre' => 9,
            'octubre' => 10,
            'noviembre' => 11,
            'diciembre' => 12,
        ];

        if (preg_match('/\b(\d{1,2})\s+de\s+([a-z]+)(?:\s+de)?\s+(\d{4})\b/', $ascii, $matches)) {
            $month = $months[$matches[2]] ?? null;
            if ($month) {
                return Carbon::create((int) $matches[3], $month, (int) $matches[1])->toDateString();
            }
        }

        try {
            return Carbon::parse($value)->toDateString();
        } catch (\Throwable) {
            return null;
        }
    }

    private function isInternshipOrPractice(string ...$texts): bool
    {
        $text = $this->normalizeInternshipText(implode(' ', $texts));

        if (preg_match('/\b(pasantia|pasantias|pasante|pasantes|practica|practicas|practicante|practicantes|internship|intern|interns|trainee)\b/i', $text)) {
            return true;
        }

        return preg_match('/\bpracticas?\s+(?:pre\s*)?profesionales?\b|\bpracticas?\s+preprofesionales?\b/i', $text) === 1;
    }

    private function normalizeInternshipText(string $text): string
    {
        $text = mb_strtolower($text, 'UTF-8');
        $text = strtr($text, [
            'á' => 'a',
            'é' => 'e',
            'í' => 'i',
            'ó' => 'o',
            'ú' => 'u',
            'ü' => 'u',
            'ñ' => 'n',
        ]);

        return trim(preg_replace('/[^a-z0-9]+/i', ' ', $text) ?: '');
    }

    private function removeUnpublishedInternshipPreview(string $sourceUrl): void
    {
        $preview = BotVacancyPreview::where('source_url', $sourceUrl)->first();

        if (!$preview) {
            return;
        }

        if ($preview->status === 'published') {
            $preview->update([
                'raw_data' => array_merge($preview->raw_data ?? [], [
                    'internship_or_practice_detected' => true,
                    'internship_or_practice_detected_at' => now()->toIso8601String(),
                ]),
            ]);

            return;
        }

        $preview->delete();
    }

    private function cleanDescription(string $html): string
    {
        $html = html_entity_decode($html, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $html = html_entity_decode($html, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $html = preg_replace('/<\s*(br|\/p|\/li)\s*\/?>/i', "\n", $html);
        $text = strip_tags($html);
        $text = $this->cleanText($text);
        $lines = array_filter(array_map('trim', preg_split('/\R+/', $text) ?: []));

        return implode("\n", $lines);
    }

    private function cleanText(string $text): string
    {
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = str_replace(
            ["\u{2013}", "\u{2014}", "\u{00A0}", '\\u2013', '\\u2014', '\\xa0'],
            ['-', '-', ' ', '-', '-', ' '],
            $text
        );

        return trim($text);
    }

    private function trabajonautasDescription(string $location, string $link, string $companyUrl, ?array $municipality = null): string
    {
        $linkEscaped = e($link);
        $companyUrlEscaped = e($companyUrl);

        $description = implode('', [
            '<p><strong>MODALIDAD DE POSTULACIÓN:</strong></p>',
            '<p>De manera digital utilizando el siguiente enlace de color rojo:</p>',
            '<p><br></p>',
            '<p><a class="text-tbn-primary" href="' . $linkEscaped . '" target="_blank" rel="noopener">' . $linkEscaped . '</a></p>',
            '<p><br></p>',
            '<p>¡Impulsa tu futuro profesional!</p>',
            '<p><br></p>',
            '<p>Esta convocatoria fue verificada por el equipo de <strong style="font-weight: 500;"><b>TRABAJONAUTAS.COM</b></strong> y representa una excelente oportunidad de crecimiento para ti. ¡No la dejes pasar!</p>',
            '<p><br></p>',
            '<p><strong style="font-weight:700;">Fuente:</strong> <a class="text-tbn-primary" href="' . $companyUrlEscaped . '" target="_blank" rel="noopener">' . $companyUrlEscaped . '</a></p>',
            '<p><br></p>',
            '<p>Descarga todos los detalles en el/los archivo(s) adjunto(s):</p>',
        ]);

        return $this->prependWorkplaceToDescription($description, $municipality);
    }

    private function prependWorkplaceToDescription(string $description, ?array $municipality): string
    {
        if (!$municipality || !empty($municipality['is_main_city'])) {
            return $description;
        }

        if (Str::contains($this->normalize($description), 'lugar de trabajo')) {
            return $description;
        }

        $municipalityName = trim((string) ($municipality['municipality'] ?? ''));
        $department = trim((string) ($municipality['department'] ?? ''));

        if ($municipalityName === '' || $department === '') {
            return $description;
        }

        $workplace = '<p><strong>LUGAR DE TRABAJO:</strong><br> </p>';
        $workplace = $workplace . '<p>' . e($municipalityName) . ', ' . e($department) . '<br></p><br>';

        return $workplace . $description;
    }

    private function appendSummaryError(array &$summary, mixed $error): void
    {
        if (count($summary['errors'] ?? []) >= 25) {
            return;
        }

        $safe = SensitiveDataSanitizer::text($error, 500);

        if ($safe !== null && $safe !== '') {
            $summary['errors'][] = $safe;
        }
    }

    private function normalize(string $value): string
    {
        return Str::of($value)
            ->ascii()
            ->lower()
            ->replaceMatches('/[^a-z0-9]+/', ' ')
            ->squish()
            ->toString();
    }
}
