<?php

namespace App\Services\Bot;

use App\Models\Announcement;
use App\Models\BotCompany;
use App\Models\BotVacancyPreview;
use App\Models\Company;
use App\Models\CompanyType;
use App\Models\Location;
use App\Models\SicoesScrapeBatch;
use App\Models\SicoesScrapeBatchItem;
use App\Services\ProfessionAssignmentService;
use App\Support\SensitiveDataSanitizer;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SicoesDocumentImporterService
{
    private const ANALYSIS_SCHEMA_VERSION = 'sicoes-document-v2-2026-07-11';

    private const PROMPT_VERSION = 'sicoes-prompt-v1-2026-07-11';

    public function __construct(
        private SicoesDocumentAiAnalyzer $analyzer,
        private SicoesDocumentEligibilityClassifier $eligibilityClassifier,
        private ProfessionAssignmentService $professionAssignments,
    ) {}

    public function importRun(
        array $run,
        int $botCompanyId,
        string $userId,
        ?string $batchId = null,
        ?callable $onProgress = null,
    ): array {
        $botCompany = BotCompany::findOrFail($botCompanyId);
        $slug = $this->dateSlug($run['slug'] ?? $run['date'] ?? null);
        $basePath = $this->basePath($run);
        $documents = $this->documentsForRun($basePath, $slug);

        $summary = [
            'total_items_feed' => count($documents),
            'saved' => 0,
            'updated' => 0,
            'shown_in_batch' => 0,
            'already_published' => 0,
            'already_previewed' => 0,
            'reactivated_deleted' => 0,
            'skipped_without_cuce' => 0,
            'ai_provider' => config('sicoes.ai.provider', 'anthropic'),
            'ai_enabled' => (bool) config('services.anthropic.api_key'),
            'ai_model' => config('services.anthropic.model', 'claude-haiku-4-5-20251001'),
            'ai_calls' => 0,
            'ai_cache_hits' => 0,
            'ai_errors' => 0,
            'ai_prompt_tokens' => 0,
            'ai_output_tokens' => 0,
            'ai_total_tokens' => 0,
            'anthropic_enabled' => (bool) config('services.anthropic.api_key'),
            'anthropic_calls' => 0,
            'anthropic_errors' => 0,
            'document_processed' => 0,
            'document_errors' => 0,
            'document_discarded' => 0,
            'preclassified_discards' => 0,
            'discarded_not_individual_consultant' => 0,
            'discarded_company_or_goods' => 0,
            'discarded_by_type' => [],
            'discarded_details' => [],
            'preview_ids' => [],
            'announcement_ids' => [],
            'errors' => [],
        ];

        foreach ($documents as $index => $document) {
            $onProgress && $onProgress([
                'status' => 'running',
                'total' => count($documents),
                'processed' => $index,
                'cuce' => $document['cuce'] ?? null,
                'document' => $document['filename'] ?? null,
                'message' => '[STEP 7] Analizando documento con IA: '.($document['filename'] ?? 'sin_archivo'),
            ]);

            try {
                $result = $this->importDocument($document, $botCompany, $userId, $batchId);
                $this->recordBatchItem($batchId, $document, $result);
                $status = $result['status'] ?? null;

                if ($status === 'saved') {
                    $summary['saved']++;
                } elseif ($status === 'updated') {
                    $summary['updated']++;
                } elseif ($status === 'already_previewed') {
                    $summary['already_previewed']++;
                } elseif ($status === 'already_published') {
                    $summary['already_published']++;
                } elseif ($status === 'reactivated_deleted') {
                    $summary['reactivated_deleted']++;
                } elseif ($status === 'discarded') {
                    $summary['document_discarded']++;
                    if (! empty($result['preclassified'])) {
                        $summary['preclassified_discards']++;
                    }
                    $discardType = (string) ($result['discard_type'] ?? 'no_determinado');
                    $discardCategory = (string) ($result['discard_category'] ?? 'no_consultor_persona');
                    $summary['discarded_by_type'][$discardType] = ((int) ($summary['discarded_by_type'][$discardType] ?? 0)) + 1;

                    if ($discardCategory === 'empresa_bienes_obra') {
                        $summary['discarded_company_or_goods']++;
                    } else {
                        $summary['discarded_not_individual_consultant']++;
                    }

                    if (! empty($result['discard_detail'])) {
                        $summary['discarded_details'][] = $result['discard_detail'];
                    }
                }

                if (! empty($result['ai_used'])) {
                    $summary['ai_calls']++;
                    $summary['anthropic_calls']++;
                }

                if (! empty($result['ai_cache_hit'])) {
                    $summary['ai_cache_hits']++;
                }

                foreach (['ai_prompt_tokens', 'ai_output_tokens', 'ai_total_tokens'] as $tokenKey) {
                    $summary[$tokenKey] += (int) ($result[$tokenKey] ?? 0);
                }

                if (! empty($result['ai_error'])) {
                    $summary['ai_errors']++;
                    $summary['anthropic_errors']++;
                }

                if (! empty($result['document_error'])) {
                    $summary['document_errors']++;
                    $this->appendSummaryError($summary, $result['document_error']);
                }

                if (! empty($result['preview_id'])) {
                    $summary['preview_ids'][] = (int) $result['preview_id'];
                }

                $summary['document_processed']++;

                $onProgress && $onProgress([
                    'status' => 'running',
                    'total' => count($documents),
                    'processed' => $index + 1,
                    'saved' => $summary['saved'],
                    'updated' => $summary['updated'],
                    'failed' => $summary['document_errors'],
                    'discarded' => $summary['document_discarded'],
                    'ai_calls' => $summary['ai_calls'],
                    'ai_cache_hits' => $summary['ai_cache_hits'],
                    'ai_errors' => $summary['ai_errors'],
                    'cuce' => $document['cuce'] ?? null,
                    'document' => $document['filename'] ?? null,
                    'preview_id' => $result['preview_id'] ?? null,
                    'message' => $status === 'discarded'
                        ? '[STEP 8] Documento descartado por clasificacion IA: '.($result['discard_type'] ?? 'no_determinado')
                        : '[STEP 8] Preview SICOES por documento guardado: '.(($result['preview_id'] ?? null) ?: 'N/A'),
                ]);
            } catch (\InvalidArgumentException) {
                $summary['skipped_without_cuce']++;
            } catch (\Throwable $exception) {
                Log::warning('SICOES no pudo procesar un documento.', [
                    'cuce' => SensitiveDataSanitizer::text($document['cuce'] ?? null, 100),
                    'document' => SensitiveDataSanitizer::basename($document['filename'] ?? null),
                    ...SensitiveDataSanitizer::exception($exception, 300),
                ]);
                $summary['document_errors']++;
                $safeError = SensitiveDataSanitizer::exception($exception, 500)['message']
                    ?: 'Error no identificado al procesar el documento.';
                $this->appendSummaryError($summary, $safeError);
                $this->recordBatchItem($batchId, $document, [
                    'status' => 'error',
                    'document_error' => $safeError,
                ]);
                $recordedItem = $this->batchItem($batchId, $document);

                if ((bool) data_get($recordedItem?->ai_metadata, 'used')) {
                    $summary['ai_calls']++;
                    $summary['anthropic_calls']++;
                }

                if ((bool) data_get($recordedItem?->ai_metadata, 'cache_hit')) {
                    $summary['ai_cache_hits']++;
                }

                $summary['document_processed']++;

                $onProgress && $onProgress([
                    'status' => 'running',
                    'total' => count($documents),
                    'processed' => $index + 1,
                    'saved' => $summary['saved'],
                    'updated' => $summary['updated'],
                    'failed' => $summary['document_errors'],
                    'discarded' => $summary['document_discarded'],
                    'ai_calls' => $summary['ai_calls'],
                    'ai_cache_hits' => $summary['ai_cache_hits'],
                    'ai_errors' => $summary['ai_errors'],
                    'cuce' => $document['cuce'] ?? null,
                    'document' => $document['filename'] ?? null,
                    'message' => '[STEP 8] Documento con error: '.SensitiveDataSanitizer::text($safeError, 160),
                ]);
            }
        }

        if ($batchId) {
            $summary['shown_in_batch'] = SicoesScrapeBatchItem::query()
                ->where('batch_id', $batchId)
                ->whereNotNull('preview_id')
                ->whereNull('removed_at')
                ->whereIn('status', ['preview', 'edited', 'error'])
                ->count();
        }

        return $summary;
    }

    private function importDocument(array $document, BotCompany $botCompany, string $userId, ?string $batchId): array
    {
        $cuce = trim((string) ($document['cuce'] ?? ''));

        if ($cuce === '') {
            throw new \InvalidArgumentException('Documento SICOES sin CUCE.');
        }

        $sourceUrl = $this->documentSourceUrl($document);
        $sourceHash = hash('sha256', $sourceUrl);
        $legacySourceHash = hash('sha256', 'sicoes:'.$cuce);
        $publishedAnnouncementId = $this->findPublishedAnnouncementId(
            sourceUrl: $sourceUrl,
            sourceHash: $sourceHash,
            legacySourceHash: $legacySourceHash,
            rowSourceUrl: $document['source_url'] ?? null,
        );
        $publishedPreview = BotVacancyPreview::where('source_url', $sourceUrl)
            ->where('status', 'published')
            ->first();

        if (
            $publishedPreview
            && $this->existingPublishedAnnouncementExists($sourceUrl, $sourceHash, $legacySourceHash, $publishedPreview->convocatoria_id, $document['source_url'] ?? null)
        ) {
            $this->markPublishedPreviewAsSeen($sourceUrl, [
                'source' => 'sicoes',
                'cuce' => $cuce,
                'document_hash' => $document['document_hash'] ?? null,
                'document_filename' => $document['filename'] ?? null,
                'scraped_at' => now()->toIso8601String(),
                'already_published_detected' => true,
            ]);

            return ['status' => 'already_published'];
        }

        if (! $publishedPreview && $publishedAnnouncementId) {
            return ['status' => 'already_published'];
        }

        $analysisKey = $this->analysisKey($document);
        $textResult = $this->documentText($document);

        if (! $textResult['ok']) {
            return $this->saveErrorPreview(
                document: $document,
                botCompany: $botCompany,
                userId: $userId,
                batchId: $batchId,
                sourceUrl: $sourceUrl,
                sourceHash: $sourceHash,
                legacySourceHash: $legacySourceHash,
                attachments: $this->documentAttachments($document),
                errorType: 'text_extraction_error',
                error: $textResult['error'] ?? 'No se pudo convertir el documento a texto.',
                aiMeta: ['analysis_key' => $analysisKey],
            );
        }

        $preclassification = $this->eligibilityClassifier->classify($document, $textResult['text']);
        $analysis = null;

        if (($preclassification['decision'] ?? null) === 'rejected') {
            $analysis = [
                'success' => true,
                'used' => false,
                'provider' => 'local_rules',
                'model' => config('services.anthropic.model', 'claude-haiku-4-5-20251001'),
                'http_status' => null,
                'error' => null,
                'error_type' => null,
                'data' => $this->eligibilityClassifier->rejectedAnalysis($preclassification, $document),
                'analyzed_at' => now()->toIso8601String(),
                'analysis_key' => $analysisKey,
                'cache_hit' => false,
                'preclassified' => true,
            ];
        } else {
            $cachedItem = $this->cachedAnalysisItem($analysisKey);
            if ($cachedItem && data_get($cachedItem->ai_metadata, 'provider') !== 'local_rules') {
                $analysis = $this->analysisFromCache($cachedItem, $analysisKey);
            }

            if (! $analysis) {
                $analysis = $this->analyzer->analyze([
                    ...$document,
                    'preclassification' => $preclassification,
                ], $textResult['text']);
                $analysis['analysis_key'] = $analysisKey;
                $analysis['cache_hit'] = false;
            }
        }

        if (! empty($analysis['used']) && empty($analysis['success'])) {
            return $this->saveErrorPreview(
                document: $document,
                botCompany: $botCompany,
                userId: $userId,
                batchId: $batchId,
                sourceUrl: $sourceUrl,
                sourceHash: $sourceHash,
                legacySourceHash: $legacySourceHash,
                attachments: $this->documentAttachments($document),
                errorType: $analysis['error_type'] ?? 'ai_error',
                error: $analysis['error'] ?? 'La IA no devolvio una ficha valida.',
                aiMeta: $analysis,
                textMeta: $textResult,
            );
        }

        if (empty($analysis['success']) || ! is_array($analysis['data'] ?? null)) {
            return $this->saveErrorPreview(
                document: $document,
                botCompany: $botCompany,
                userId: $userId,
                batchId: $batchId,
                sourceUrl: $sourceUrl,
                sourceHash: $sourceHash,
                legacySourceHash: $legacySourceHash,
                attachments: $this->documentAttachments($document),
                errorType: $analysis['error_type'] ?? 'ai_invalid_result',
                error: $analysis['error'] ?? 'La IA no devolvio una ficha valida.',
                aiMeta: $analysis,
                textMeta: $textResult,
            );
        }

        $analysis['data'] = $this->eligibilityClassifier->reconcile($analysis['data'], $preclassification);

        $data = $analysis['data'];
        $this->recordBatchItem($batchId, $document, [
            'status' => 'analyzed',
            'ai_used' => (bool) ($analysis['used'] ?? false),
            'ai_cache_hit' => (bool) ($analysis['cache_hit'] ?? false),
            'analysis_key' => $analysisKey,
            'analysis_success' => true,
            'analysis_data' => $data,
            'ai_metadata' => $this->aiMetadata($analysis),
        ]);
        $classificationWarnings = $this->classificationWarnings($data);

        if ($this->shouldDiscard($data)) {
            return $this->discardDocument(
                document: $document,
                batchId: $batchId,
                sourceUrl: $sourceUrl,
                sourceHash: $sourceHash,
                legacySourceHash: $legacySourceHash,
                analysis: $analysis,
                textMeta: $textResult,
                warnings: $classificationWarnings,
            );
        }

        $areaAssignment = $this->professionAssignments->resolve($data['area_ids'] ?? []);

        if (! $areaAssignment['valid']) {
            $analysis['area_assignment'] = $areaAssignment;

            return $this->saveErrorPreview(
                document: $document,
                botCompany: $botCompany,
                userId: $userId,
                batchId: $batchId,
                sourceUrl: $sourceUrl,
                sourceHash: $sourceHash,
                legacySourceHash: $legacySourceHash,
                attachments: $this->documentAttachments($document),
                errorType: 'area_resolution_error',
                error: $areaAssignment['error'] ?? 'Claude no devolvio area_ids validos del catalogo.',
                aiMeta: $analysis,
                textMeta: $textResult,
            );
        }

        $warnings = $this->warnings($document, $data);
        $warnings = array_values(array_unique([...$warnings, ...$classificationWarnings]));
        $location = $this->normalizeLocation($data);
        $salaryValue = max(0, (int) data_get($data, 'sueldo.valor', 0));
        $manualReview = $this->manualReviewRequired($data, $location, $warnings);
        $title = $this->previewTitle($document, $data);
        $description = $this->description($data, $location, $cuce, $warnings);
        $attachments = $this->documentAttachments($document);
        $existingPreview = BotVacancyPreview::where('source_url', $sourceUrl)->first();
        $professionsBefore = $existingPreview?->selected_profession_ids ?? [];

        $previewData = [
            'bot_company_id' => $botCompany->id,
            'title' => $title,
            'source_url' => $sourceUrl,
            'original_description' => Str::limit($textResult['text'], 65000, ''),
            'description' => $description,
            'area' => implode(', ', $areaAssignment['area_names']),
            'professions' => implode(', ', $areaAssignment['profession_names']),
            'department' => $location['department'],
            'location' => $location['location'],
            'expiration_date' => $this->expirationDate($document),
            'salary' => (string) $salaryValue,
            'raw_data' => [
                'source' => 'sicoes',
                'bot_company_id' => $botCompany->id,
                'bot_company_name' => $botCompany->name,
                'cuce' => $cuce,
                'document_id' => $document['document_id'],
                'document_hash' => $document['document_hash'],
                'document_filename' => $document['filename'],
                'document_path' => $this->storageRelativePath($document['path']),
                'document_text_path' => $this->storageRelativePath($textResult['text_path'] ?? null),
                'document_text_method' => $textResult['method'] ?? null,
                'row_source_url' => $document['source_url'] ?? null,
                'source_hash' => $sourceHash,
                'legacy_source_hash' => $legacySourceHash,
                'scraped_at' => now()->toIso8601String(),
                'sicoes_batch_id' => $batchId,
                'sicoes_publication_date' => $document['published_at'] ?? null,
                'sicoes_expiration_date' => $document['expires_at'] ?? null,
                'analysis_schema_version' => self::ANALYSIS_SCHEMA_VERSION,
                'analysis_prompt_version' => self::PROMPT_VERSION,
                'analysis_key' => $analysisKey,
                'already_published_detected' => false,
                'published_announcement_id' => null,
                'ai_provider' => $analysis['provider'] ?? config('sicoes.ai.provider', 'anthropic'),
                'ai_used' => (bool) ($analysis['used'] ?? false),
                'ai_success' => (bool) ($analysis['success'] ?? false),
                'ai_error' => $analysis['error'] ?? null,
                'ai_error_type' => $analysis['error_type'] ?? null,
                'ai_model' => $analysis['model'] ?? config('services.anthropic.model', 'claude-haiku-4-5-20251001'),
                'ai_http_status' => $analysis['http_status'] ?? null,
                'ai_analyzed_at' => $analysis['analyzed_at'] ?? null,
                'ai_attempts' => $analysis['ai_attempts'] ?? null,
                'ai_usage_metadata' => $analysis['ai_usage_metadata'] ?? null,
                'ai_prompt_tokens' => $analysis['ai_prompt_tokens'] ?? null,
                'ai_output_tokens' => $analysis['ai_output_tokens'] ?? null,
                'ai_total_tokens' => $analysis['ai_total_tokens'] ?? null,
                'ai_cache_hit' => (bool) ($analysis['cache_hit'] ?? false),
                'manual_review_required' => $manualReview,
                'manual_review_reasons' => $this->manualReviewReasons($data, $location, $warnings),
                'warnings' => $warnings,
                'ai_analysis' => $data,
                'ai_classification' => $this->classificationData($data),
                'ai_evidence' => $data['evidencias'] ?? [],
                'ai_salary' => $data['sueldo'] ?? [],
                'ai_location' => $data['ubicacion_detectada'] ?? [],
                'raw_ai_areas' => $data['area_ids'] ?? [],
                'raw_ai_professions' => [],
                'resolved_area_ids' => $areaAssignment['area_ids'],
                'profession_assignment_source' => 'area_profession_pivot',
                'profession_assignment_error' => null,
                'matched_profession_ids' => $areaAssignment['profession_ids'],
                'municipality' => $location['municipality'],
                'location_source' => $location['source'],
                'location_detected_text' => $location['detected_text'],
                'salary_source' => 'sicoes_document_ai',
                'salary_detected_text' => $data['detalle_sueldos'] ?? data_get($data, 'sueldo.detalle'),
                'anthropic_used' => (bool) ($analysis['used'] ?? false),
                'anthropic_success' => (bool) ($analysis['success'] ?? false),
                'anthropic_error' => $analysis['error'] ?? null,
                'anthropic_error_type' => $analysis['error_type'] ?? null,
                'anthropic_model' => $analysis['model'] ?? config('services.anthropic.model', 'claude-haiku-4-5-20251001'),
                'anthropic_http_status' => $analysis['http_status'] ?? null,
                'anthropic_analyzed_at' => $analysis['analyzed_at'] ?? null,
                'anthropic_attempts' => $analysis['anthropic_attempts'] ?? null,
                'anthropic_stop_reason' => $analysis['anthropic_stop_reason'] ?? null,
                'anthropic_usage_metadata' => $analysis['anthropic_usage_metadata'] ?? null,
                'anthropic_input_tokens' => $analysis['anthropic_input_tokens'] ?? null,
                'anthropic_output_tokens' => $analysis['anthropic_output_tokens'] ?? null,
                'anthropic_total_tokens' => $analysis['anthropic_total_tokens'] ?? null,
                'anthropic_response_metadata' => $analysis['anthropic_response_metadata'] ?? null,
                'item' => $this->safeSourceItem($document['row'] ?? []),
            ],
            'status' => 'preview',
            'scrape_batch_id' => $batchId,
            'removed_from_batch_at' => null,
            'selected_company_id' => $this->resolveCompany($document['entity'] ?? 'SICOES', $userId)->id,
            'selected_area_id' => $areaAssignment['area_ids'][0] ?? null,
            'selected_profession_ids' => $areaAssignment['profession_ids'],
            'selected_location_ids' => $this->resolveLocations($location['department']),
            'is_pro' => false,
            'attachments' => $attachments,
        ];

        $status = DB::transaction(fn (): string => $this->savePreview($botCompany, $sourceUrl, $previewData));
        $preview = BotVacancyPreview::where('source_url', $sourceUrl)->first();
        $previewId = $preview?->id;

        $this->professionAssignments->logDecision([
            'source' => 'sicoes_document',
            'raw_ai_areas' => $data['area_ids'] ?? [],
            'raw_ai_professions' => [],
            'resolved_area_ids' => $areaAssignment['area_ids'],
            'professions_before' => $professionsBefore,
            'professions_from_areas' => $areaAssignment['profession_ids'],
            'professions_after' => $preview?->selected_profession_ids ?? [],
            'preview_id' => $previewId,
            'scrape_batch_id' => $batchId,
            'professions_edited_manually' => $preview ? $this->professionAssignments->professionsEditedManually($preview) : false,
        ]);

        return [
            'status' => $status,
            'preview_id' => $previewId ? (int) $previewId : null,
            'ai_used' => (bool) ($analysis['used'] ?? false),
            'ai_cache_hit' => (bool) ($analysis['cache_hit'] ?? false),
            'ai_error' => empty($analysis['success']) ? ($analysis['error'] ?? 'AI error') : null,
            'ai_prompt_tokens' => (int) ($analysis['ai_prompt_tokens'] ?? 0),
            'ai_output_tokens' => (int) ($analysis['ai_output_tokens'] ?? 0),
            'ai_total_tokens' => (int) ($analysis['ai_total_tokens'] ?? 0),
            'analysis_key' => $analysisKey,
            'analysis_success' => true,
            'analysis_data' => $data,
            'ai_metadata' => $this->aiMetadata($analysis),
        ];
    }

    private function discardDocument(
        array $document,
        ?string $batchId,
        string $sourceUrl,
        string $sourceHash,
        string $legacySourceHash,
        array $analysis,
        array $textMeta,
        array $warnings = [],
    ): array {
        $data = $analysis['data'] ?? [];
        $type = (string) ($data['tipo_oportunidad'] ?? 'no_determinado');
        $category = $this->discardCategory($data);
        $reason = $this->discardReason($data);
        $detail = [
            'cuce' => $document['cuce'] ?? null,
            'document' => $document['filename'] ?? null,
            'type' => $type,
            'category' => $category,
            'reason' => $reason,
            'evidence' => $data['evidencia_clasificacion'] ?? null,
        ];
        $rawData = [
            'source' => 'sicoes',
            'cuce' => $document['cuce'] ?? null,
            'document_id' => $document['document_id'] ?? null,
            'document_hash' => $document['document_hash'] ?? null,
            'document_filename' => $document['filename'] ?? null,
            'document_path' => $this->storageRelativePath($document['path'] ?? null),
            'document_text_path' => $this->storageRelativePath($textMeta['text_path'] ?? null),
            'document_text_method' => $textMeta['method'] ?? null,
            'row_source_url' => $document['source_url'] ?? null,
            'source_hash' => $sourceHash,
            'legacy_source_hash' => $legacySourceHash,
            'sicoes_batch_id' => $batchId,
            'sicoes_publication_date' => $document['published_at'] ?? null,
            'sicoes_expiration_date' => $document['expires_at'] ?? null,
            'analysis_schema_version' => self::ANALYSIS_SCHEMA_VERSION,
            'analysis_prompt_version' => self::PROMPT_VERSION,
            'analysis_key' => $analysis['analysis_key'] ?? null,
            'scraped_at' => now()->toIso8601String(),
            'discarded_by_ai' => (bool) ($analysis['used'] ?? false),
            'discarded_by_classifier' => true,
            'discard_category' => $category,
            'discard_type' => $type,
            'discard_reason' => $reason,
            'discard_evidence' => $data['evidencia_clasificacion'] ?? null,
            'warnings' => $warnings,
            'ai_classification' => $this->classificationData($data),
            'ai_analysis' => $data,
            'ai_provider' => $analysis['provider'] ?? config('sicoes.ai.provider', 'anthropic'),
            'ai_used' => (bool) ($analysis['used'] ?? false),
            'ai_success' => (bool) ($analysis['success'] ?? false),
            'ai_error' => $analysis['error'] ?? null,
            'ai_error_type' => $analysis['error_type'] ?? null,
            'ai_model' => $analysis['model'] ?? config('services.anthropic.model', 'claude-haiku-4-5-20251001'),
            'ai_http_status' => $analysis['http_status'] ?? null,
            'ai_analyzed_at' => $analysis['analyzed_at'] ?? null,
            'ai_attempts' => $analysis['ai_attempts'] ?? null,
            'ai_usage_metadata' => $analysis['ai_usage_metadata'] ?? null,
            'ai_prompt_tokens' => $analysis['ai_prompt_tokens'] ?? null,
            'ai_output_tokens' => $analysis['ai_output_tokens'] ?? null,
            'ai_total_tokens' => $analysis['ai_total_tokens'] ?? null,
            'ai_cache_hit' => (bool) ($analysis['cache_hit'] ?? false),
            'anthropic_used' => (bool) ($analysis['used'] ?? false),
            'anthropic_success' => (bool) ($analysis['success'] ?? false),
            'anthropic_error' => $analysis['error'] ?? null,
            'anthropic_error_type' => $analysis['error_type'] ?? null,
            'anthropic_model' => $analysis['model'] ?? config('services.anthropic.model', 'claude-haiku-4-5-20251001'),
            'anthropic_http_status' => $analysis['http_status'] ?? null,
            'anthropic_analyzed_at' => $analysis['analyzed_at'] ?? null,
            'anthropic_attempts' => $analysis['anthropic_attempts'] ?? null,
            'anthropic_stop_reason' => $analysis['anthropic_stop_reason'] ?? null,
            'anthropic_usage_metadata' => $analysis['anthropic_usage_metadata'] ?? null,
            'anthropic_input_tokens' => $analysis['anthropic_input_tokens'] ?? null,
            'anthropic_output_tokens' => $analysis['anthropic_output_tokens'] ?? null,
            'anthropic_total_tokens' => $analysis['anthropic_total_tokens'] ?? null,
            'anthropic_response_metadata' => $analysis['anthropic_response_metadata'] ?? null,
            'item' => $this->safeSourceItem($document['row'] ?? []),
        ];

        $this->markDiscardedPreviewRemoved($sourceUrl, $batchId, $rawData);

        Log::info('SICOES documento descartado por clasificacion.', SensitiveDataSanitizer::context([
            'cuce' => $document['cuce'] ?? null,
            'document' => SensitiveDataSanitizer::basename($document['filename'] ?? null),
            'classifier' => ! empty($analysis['used']) ? 'anthropic' : 'local_rules',
            'cache_hit' => (bool) ($analysis['cache_hit'] ?? false),
            'preclassified' => (bool) ($analysis['preclassified'] ?? false),
            'type' => $type,
            'category' => $category,
            'reason' => $reason,
        ], 300, 3, 20));

        return [
            'status' => 'discarded',
            'preview_id' => null,
            'ai_used' => (bool) ($analysis['used'] ?? false),
            'ai_cache_hit' => (bool) ($analysis['cache_hit'] ?? false),
            'ai_error' => null,
            'ai_prompt_tokens' => (int) ($analysis['ai_prompt_tokens'] ?? 0),
            'ai_output_tokens' => (int) ($analysis['ai_output_tokens'] ?? 0),
            'ai_total_tokens' => (int) ($analysis['ai_total_tokens'] ?? 0),
            'discarded' => true,
            'preclassified' => (bool) ($analysis['preclassified'] ?? false),
            'discard_type' => $type,
            'discard_category' => $category,
            'discard_detail' => $detail,
            'analysis_key' => $analysis['analysis_key'] ?? null,
            'analysis_success' => true,
            'analysis_data' => $data,
            'ai_metadata' => $this->aiMetadata($analysis),
        ];
    }

    private function saveErrorPreview(
        array $document,
        BotCompany $botCompany,
        string $userId,
        ?string $batchId,
        string $sourceUrl,
        string $sourceHash,
        string $legacySourceHash,
        array $attachments,
        string $errorType,
        string $error,
        array $aiMeta = [],
        array $textMeta = [],
    ): array {
        $cuce = (string) ($document['cuce'] ?? '');
        $analysisSuccess = ! empty($aiMeta['success']) && is_array($aiMeta['data'] ?? null);
        $location = [
            'department' => 'No especificado',
            'location' => 'No especificado',
            'municipality' => null,
            'source' => 'error',
            'detected_text' => null,
            'description' => 'No especificado',
        ];

        $previewData = [
            'bot_company_id' => $botCompany->id,
            'title' => $this->previewTitle($document, []),
            'source_url' => $sourceUrl,
            'original_description' => isset($textMeta['text'])
                ? Str::limit((string) $textMeta['text'], 65000, '')
                : null,
            'description' => $this->errorDescription($cuce, $errorType, $error),
            'area' => 'No especificado',
            'professions' => 'No especificado',
            'department' => 'No especificado',
            'location' => 'No especificado',
            'expiration_date' => $this->expirationDate($document),
            'salary' => '0',
            'raw_data' => [
                'source' => 'sicoes',
                'cuce' => $cuce,
                'document_id' => $document['document_id'] ?? null,
                'document_hash' => $document['document_hash'] ?? null,
                'document_filename' => $document['filename'] ?? null,
                'document_path' => $this->storageRelativePath($document['path'] ?? null),
                'document_text_path' => $this->storageRelativePath($textMeta['text_path'] ?? null),
                'document_text_method' => $textMeta['method'] ?? null,
                'row_source_url' => $document['source_url'] ?? null,
                'source_hash' => $sourceHash,
                'legacy_source_hash' => $legacySourceHash,
                'sicoes_batch_id' => $batchId,
                'sicoes_publication_date' => $document['published_at'] ?? null,
                'sicoes_expiration_date' => $document['expires_at'] ?? null,
                'analysis_schema_version' => self::ANALYSIS_SCHEMA_VERSION,
                'analysis_prompt_version' => self::PROMPT_VERSION,
                'analysis_key' => $aiMeta['analysis_key'] ?? null,
                'scraped_at' => now()->toIso8601String(),
                'manual_review_required' => true,
                'manual_review_reasons' => [$errorType],
                'document_error_type' => $errorType,
                'document_error' => SensitiveDataSanitizer::text($error, 500),
                'raw_ai_areas' => data_get($aiMeta, 'data.area_ids', []),
                'raw_ai_professions' => [],
                'resolved_area_ids' => data_get($aiMeta, 'area_assignment.area_ids', []),
                'profession_assignment_source' => 'area_profession_pivot',
                'profession_assignment_error' => data_get($aiMeta, 'area_assignment.error', $errorType === 'area_resolution_error' ? $error : null),
                'location_source' => $location['source'],
                'location_detected_text' => null,
                'salary_source' => 'error',
                'salary_detected_text' => null,
                'ai_provider' => $aiMeta['provider'] ?? config('sicoes.ai.provider', 'anthropic'),
                'ai_used' => (bool) ($aiMeta['used'] ?? false),
                'ai_success' => $analysisSuccess,
                'ai_error' => $aiMeta['error'] ?? $error,
                'ai_error_type' => $aiMeta['error_type'] ?? $errorType,
                'ai_model' => $aiMeta['model'] ?? config('services.anthropic.model', 'claude-haiku-4-5-20251001'),
                'ai_http_status' => $aiMeta['http_status'] ?? null,
                'ai_attempts' => $aiMeta['ai_attempts'] ?? null,
                'ai_usage_metadata' => $aiMeta['ai_usage_metadata'] ?? null,
                'ai_prompt_tokens' => $aiMeta['ai_prompt_tokens'] ?? null,
                'ai_output_tokens' => $aiMeta['ai_output_tokens'] ?? null,
                'ai_total_tokens' => $aiMeta['ai_total_tokens'] ?? null,
                'ai_cache_hit' => (bool) ($aiMeta['cache_hit'] ?? false),
                'anthropic_used' => (bool) ($aiMeta['used'] ?? false),
                'anthropic_success' => $analysisSuccess,
                'anthropic_error' => $aiMeta['error'] ?? $error,
                'anthropic_error_type' => $aiMeta['error_type'] ?? $errorType,
                'anthropic_model' => $aiMeta['model'] ?? config('services.anthropic.model', 'claude-haiku-4-5-20251001'),
                'anthropic_http_status' => $aiMeta['http_status'] ?? null,
                'anthropic_attempts' => $aiMeta['anthropic_attempts'] ?? null,
                'anthropic_stop_reason' => $aiMeta['anthropic_stop_reason'] ?? null,
                'anthropic_usage_metadata' => $aiMeta['anthropic_usage_metadata'] ?? null,
                'anthropic_input_tokens' => $aiMeta['anthropic_input_tokens'] ?? null,
                'anthropic_output_tokens' => $aiMeta['anthropic_output_tokens'] ?? null,
                'anthropic_total_tokens' => $aiMeta['anthropic_total_tokens'] ?? null,
                'anthropic_response_metadata' => $aiMeta['anthropic_response_metadata'] ?? null,
                'item' => $this->safeSourceItem($document['row'] ?? []),
            ],
            'status' => 'error',
            'scrape_batch_id' => $batchId,
            'removed_from_batch_at' => null,
            'selected_company_id' => $this->resolveCompany($document['entity'] ?? 'SICOES', $userId)->id,
            'selected_area_id' => null,
            'selected_profession_ids' => [],
            'selected_location_ids' => $this->resolveLocations('No especificado'),
            'is_pro' => false,
            'attachments' => $attachments,
        ];

        $status = DB::transaction(fn (): string => $this->savePreview($botCompany, $sourceUrl, $previewData));
        $preview = BotVacancyPreview::where('source_url', $sourceUrl)->first();
        $previewId = $preview?->id;

        $this->professionAssignments->logDecision([
            'source' => 'sicoes_document_error',
            'raw_ai_areas' => data_get($aiMeta, 'data.area_ids', []),
            'raw_ai_professions' => [],
            'resolved_area_ids' => data_get($aiMeta, 'area_assignment.area_ids', []),
            'professions_before' => [],
            'professions_from_areas' => [],
            'professions_after' => $preview?->selected_profession_ids ?? [],
            'preview_id' => $previewId,
            'scrape_batch_id' => $batchId,
            'professions_edited_manually' => $preview ? $this->professionAssignments->professionsEditedManually($preview) : false,
        ]);

        return [
            'status' => $status,
            'preview_id' => $previewId ? (int) $previewId : null,
            'document_error' => SensitiveDataSanitizer::text(
                "{$cuce} ".SensitiveDataSanitizer::basename($document['filename'] ?? null).": {$error}",
                500,
            ),
            'ai_used' => (bool) ($aiMeta['used'] ?? false),
            'ai_cache_hit' => (bool) ($aiMeta['cache_hit'] ?? false),
            'ai_error' => $analysisSuccess ? null : ($aiMeta['error'] ?? $error),
            'ai_prompt_tokens' => (int) ($aiMeta['ai_prompt_tokens'] ?? 0),
            'ai_output_tokens' => (int) ($aiMeta['ai_output_tokens'] ?? 0),
            'ai_total_tokens' => (int) ($aiMeta['ai_total_tokens'] ?? 0),
            'analysis_key' => $aiMeta['analysis_key'] ?? null,
            'analysis_success' => $analysisSuccess,
            'analysis_data' => $analysisSuccess ? $aiMeta['data'] : null,
            'ai_metadata' => $this->aiMetadata($aiMeta),
        ];
    }

    private function savePreview(BotCompany $botCompany, string $sourceUrl, array $data): string
    {
        $existing = BotVacancyPreview::where('source_url', $sourceUrl)->first();

        if ($existing && $existing->status === 'published') {
            $sourceHash = hash('sha256', $sourceUrl);
            $legacySourceHash = (string) data_get($existing->raw_data, 'legacy_source_hash');

            if ($this->existingPublishedAnnouncementExists($sourceUrl, $sourceHash, $legacySourceHash, $existing->convocatoria_id, data_get($existing->raw_data, 'row_source_url'))) {
                return 'already_published';
            }

            $existing->update(array_merge($data, [
                'status' => $data['status'] === 'error' ? 'error' : 'preview',
                'convocatoria_id' => null,
                'raw_data' => array_merge($existing->raw_data ?? [], $data['raw_data'], [
                    'reactivated_deleted_announcement' => true,
                    'reactivated_at' => now()->toIso8601String(),
                ]),
            ]));

            return 'reactivated_deleted';
        }

        if ($existing && $existing->status === 'edited') {
            $existing->update([
                'bot_company_id' => $botCompany->id,
                'original_description' => $data['original_description'],
                'scrape_batch_id' => $data['scrape_batch_id'],
                'removed_from_batch_at' => null,
                'attachments' => $this->mergeAttachments($existing->attachments ?? [], $data['attachments'] ?? []),
                'raw_data' => array_merge($existing->raw_data ?? [], $data['raw_data']),
            ]);

            return 'already_previewed';
        }

        BotVacancyPreview::updateOrCreate(
            ['source_url' => $sourceUrl],
            $data,
        );

        return $existing ? 'updated' : 'saved';
    }

    private function analysisKey(array $document, ?string $promptVersion = null): string
    {
        return hash('sha256', implode('|', [
            (string) ($document['document_hash'] ?? ''),
            (string) config('sicoes.ai.provider', 'anthropic'),
            (string) config('services.anthropic.model', 'claude-haiku-4-5-20251001'),
            self::ANALYSIS_SCHEMA_VERSION,
            $promptVersion ?? self::PROMPT_VERSION,
        ]));
    }

    private function cachedAnalysisItem(string $analysisKey): ?SicoesScrapeBatchItem
    {
        return SicoesScrapeBatchItem::query()
            ->where('analysis_key', $analysisKey)
            ->whereNotNull('analysis_result')
            ->latest('id')
            ->first();
    }

    private function analysisFromCache(SicoesScrapeBatchItem $item, string $analysisKey): array
    {
        $meta = $item->ai_metadata ?? [];

        return [
            'success' => true,
            'used' => false,
            'provider' => $meta['provider'] ?? config('sicoes.ai.provider', 'anthropic'),
            'model' => $meta['model'] ?? config('services.anthropic.model', 'claude-haiku-4-5-20251001'),
            'http_status' => $meta['http_status'] ?? null,
            'error' => null,
            'error_type' => null,
            'data' => $item->analysis_result,
            'analyzed_at' => $meta['analyzed_at'] ?? optional($item->created_at)->toIso8601String(),
            'analysis_key' => $analysisKey,
            'cache_hit' => true,
            'cached_from_batch_id' => $item->batch_id,
            'cached_from_item_id' => $item->id,
            'ai_prompt_tokens' => 0,
            'ai_output_tokens' => 0,
            'ai_total_tokens' => 0,
        ];
    }

    private function aiMetadata(array $analysis): array
    {
        return [
            'provider' => $analysis['provider'] ?? config('sicoes.ai.provider', 'anthropic'),
            'model' => $analysis['model'] ?? config('services.anthropic.model', 'claude-haiku-4-5-20251001'),
            'http_status' => $analysis['http_status'] ?? null,
            'success' => (bool) ($analysis['success'] ?? false),
            'used' => (bool) ($analysis['used'] ?? false),
            'cache_hit' => (bool) ($analysis['cache_hit'] ?? false),
            'preclassified' => (bool) ($analysis['preclassified'] ?? false),
            'analyzed_at' => $analysis['analyzed_at'] ?? null,
            'attempts' => $analysis['ai_attempts'] ?? $analysis['anthropic_attempts'] ?? null,
            'input_tokens' => $analysis['ai_prompt_tokens'] ?? $analysis['anthropic_input_tokens'] ?? null,
            'output_tokens' => $analysis['ai_output_tokens'] ?? $analysis['anthropic_output_tokens'] ?? null,
            'total_tokens' => $analysis['ai_total_tokens'] ?? $analysis['anthropic_total_tokens'] ?? null,
            'stop_reason' => $analysis['anthropic_stop_reason'] ?? null,
            'schema_version' => self::ANALYSIS_SCHEMA_VERSION,
            'prompt_version' => self::PROMPT_VERSION,
            'cached_from_batch_id' => $analysis['cached_from_batch_id'] ?? null,
            'cached_from_item_id' => $analysis['cached_from_item_id'] ?? null,
            'response_metadata' => $analysis['anthropic_response_metadata'] ?? null,
        ];
    }

    private function recordBatchItem(?string $batchId, array $document, array $result): void
    {
        if (! $batchId || ! SicoesScrapeBatch::whereKey($batchId)->exists()) {
            return;
        }

        $sourceUrl = $this->documentSourceUrl($document);
        $documentId = (string) ($document['document_id'] ?? hash('sha256', $sourceUrl));
        $existingItem = SicoesScrapeBatchItem::query()
            ->where('batch_id', $batchId)
            ->where('document_id', $documentId)
            ->first();
        $analysis = is_array($result['analysis_data'] ?? null) ? $result['analysis_data'] : null;
        $preview = ! empty($result['preview_id'])
            ? BotVacancyPreview::find($result['preview_id'])
            : null;
        $status = match ($result['status'] ?? 'error') {
            'discarded' => 'discarded',
            'already_published' => 'already_published',
            'analyzed' => 'analyzed',
            default => $preview?->status === 'edited'
                ? 'edited'
                : ($preview?->status === 'error' || ! empty($result['document_error']) ? 'error' : 'preview'),
        };
        $discardDetail = is_array($result['discard_detail'] ?? null) ? $result['discard_detail'] : [];

        $values = [
            'preview_id' => $preview?->id ?? $existingItem?->preview_id,
            'document_hash' => (string) ($document['document_hash'] ?? ''),
            'analysis_key' => $result['analysis_key'] ?? $existingItem?->analysis_key,
            'source_hash' => hash('sha256', $sourceUrl),
            'source_url' => $sourceUrl,
            'cuce' => (string) ($document['cuce'] ?? ''),
            'filename' => (string) ($document['filename'] ?? ''),
            'status' => $status,
            'eligible' => $analysis === null ? $existingItem?->eligible : (bool) ($analysis['eligible'] ?? ! ($analysis['debe_descartarse'] ?? true)),
            'contract_type' => $analysis['contract_type'] ?? data_get($analysis, 'preclassification.contract_type') ?? $existingItem?->contract_type,
            'discard_reason' => SensitiveDataSanitizer::text(
                $discardDetail['reason'] ?? ($analysis['motivo_descarte'] ?? ($result['document_error'] ?? null)),
                500,
            ),
            'classification_evidence' => SensitiveDataSanitizer::text(
                $discardDetail['evidence'] ?? ($analysis['evidencia_clasificacion'] ?? null),
                500,
            ),
            'removed_at' => null,
        ];

        if (! empty($result['analysis_success'])) {
            $values['analysis_result'] = $analysis;
            $values['ai_metadata'] = $result['ai_metadata'] ?? null;
        } elseif (! empty($result['ai_metadata'])) {
            $values['ai_metadata'] = $result['ai_metadata'];
        }

        SicoesScrapeBatchItem::updateOrCreate(
            [
                'batch_id' => $batchId,
                'document_id' => $documentId,
            ],
            $values,
        );
    }

    private function batchItem(?string $batchId, array $document): ?SicoesScrapeBatchItem
    {
        if (! $batchId) {
            return null;
        }

        return SicoesScrapeBatchItem::query()
            ->where('batch_id', $batchId)
            ->where('document_id', (string) ($document['document_id'] ?? ''))
            ->first();
    }

    private function documentsForRun(string $basePath, ?string $slug): array
    {
        if (! $slug) {
            return [];
        }

        $inputDir = $basePath.DIRECTORY_SEPARATOR.'entrada'.DIRECTORY_SEPARATOR.'words'.DIRECTORY_SEPARATOR.$slug;
        $rows = $this->rowsForRun($basePath, $slug);
        $rowsByCuce = collect($rows)->keyBy(fn (array $row): string => (string) ($row['cuce'] ?? ''));
        $downloadReport = $this->downloadReport($inputDir, $slug);

        if (! is_dir($inputDir)) {
            return [];
        }

        $files = collect(glob($inputDir.DIRECTORY_SEPARATOR.'*') ?: [])
            ->filter(fn (string $path): bool => is_file($path))
            ->filter(function (string $path): bool {
                $basename = basename($path);
                $extension = strtolower(pathinfo($basename, PATHINFO_EXTENSION));

                return ! str_starts_with($basename, '~$')
                    && ! str_starts_with($basename, '_')
                    && in_array($extension, ['doc', 'docx', 'pdf'], true);
            })
            ->sort()
            ->values();

        return $files->map(function (string $path) use ($rowsByCuce, $downloadReport, $basePath, $slug): array {
            $filename = basename($path);
            [$index, $cuce] = $this->indexAndCuceFromFilename($filename);
            $row = $rowsByCuce->get($cuce, []);
            $hash = hash_file('sha256', $path) ?: hash('sha256', $filename.'|'.$cuce);
            $sourceUrl = (string) ($row['ficha'] ?? "https://www.sicoes.gob.bo/portal/contrataciones/ficha/fichaProceso.php?cp={$cuce}");

            return [
                'document_id' => hash('sha256', implode('|', [$cuce, $filename, $hash, $sourceUrl])),
                'document_hash' => $hash,
                'index' => $index,
                'cuce' => $cuce,
                'filename' => $filename,
                'path' => $path,
                'extension' => strtolower(pathinfo($filename, PATHINFO_EXTENSION)),
                'size' => filesize($path) ?: null,
                'modified_at' => date('c', filemtime($path) ?: time()),
                'text_path' => $this->textPath($basePath, $slug, $index, $cuce),
                'source_url' => $sourceUrl,
                'title' => $this->text($row['objetoContratacion'] ?? $row['objeto_contratacion'] ?? $row['titulo_convocatoria'] ?? "Convocatoria SICOES {$cuce}"),
                'entity' => $this->text($row['entidad'] ?? $row['empresa'] ?? 'SICOES'),
                'published_at' => $this->text($row['fechaPublicacion'] ?? $row['fecha_publicacion'] ?? ''),
                'expires_at' => $this->text($row['fechaPresentacion'] ?? $row['fecha_presentacion'] ?? ''),
                'row' => $row,
                'download' => $this->downloadRecord($downloadReport, $cuce, $filename),
            ];
        })->values()->all();
    }

    private function basePath(array $run): string
    {
        $jsonPath = (string) ($run['json_path'] ?? '');

        if ($jsonPath !== '') {
            return dirname(dirname($jsonPath));
        }

        return storage_path('app/bot/sicoes-scraper/Sicoes');
    }

    private function rowsForRun(string $basePath, string $slug): array
    {
        $path = $basePath.DIRECTORY_SEPARATOR.'salida'.DIRECTORY_SEPARATOR.'convocatorias'.DIRECTORY_SEPARATOR.$slug.'.json';
        $decoded = $this->readJson($path);

        return array_is_list($decoded) ? $decoded : [];
    }

    private function downloadReport(string $inputDir, string $slug): array
    {
        $decoded = $this->readJson($inputDir.DIRECTORY_SEPARATOR."_descargas-{$slug}.json");

        return is_array($decoded['resultados'] ?? null) ? $decoded['resultados'] : [];
    }

    private function readJson(string $path): array
    {
        if (! is_file($path)) {
            return [];
        }

        $decoded = json_decode(file_get_contents($path) ?: '', true);

        return is_array($decoded) ? $decoded : [];
    }

    private function documentText(array $document): array
    {
        $textPath = $document['text_path'] ?? null;

        if ($textPath && is_file($textPath)) {
            $text = $this->cleanDocumentText(file_get_contents($textPath) ?: '');

            if ($text !== '') {
                return [
                    'ok' => true,
                    'text' => $text,
                    'text_path' => $textPath,
                    'method' => 'node_text_file',
                ];
            }
        }

        if (($document['extension'] ?? '') === 'docx') {
            try {
                $text = $this->extractDocxText((string) $document['path']);

                if ($text !== '') {
                    return [
                        'ok' => true,
                        'text' => $text,
                        'text_path' => null,
                        'method' => 'php_zip_docx',
                    ];
                }
            } catch (\Throwable $exception) {
                return [
                    'ok' => false,
                    'error' => 'No se pudo extraer texto DOCX: '
                        .(SensitiveDataSanitizer::text($exception->getMessage(), 300) ?: 'error no identificado'),
                    'method' => 'php_zip_docx',
                ];
            }
        }

        return [
            'ok' => false,
            'error' => 'No existe texto extraido para este documento y el formato no se pudo procesar automaticamente.',
            'method' => 'unavailable',
        ];
    }

    private function extractDocxText(string $path): string
    {
        if (! class_exists(\ZipArchive::class)) {
            throw new \RuntimeException('ZipArchive no esta disponible en PHP.');
        }

        $zip = new \ZipArchive;

        if ($zip->open($path) !== true) {
            throw new \RuntimeException('No se pudo abrir el DOCX como ZIP.');
        }

        $parts = [];

        for ($i = 0; $i < $zip->numFiles; $i++) {
            $name = (string) $zip->getNameIndex($i);
            if (! preg_match('#^word/(document|header\d*|footer\d*|footnotes|endnotes|comments).*\.xml$#i', $name)) {
                continue;
            }

            $xml = $zip->getFromIndex($i);
            if (! is_string($xml) || $xml === '') {
                continue;
            }

            $xml = preg_replace('/<w:tab\/>/', "\t", $xml) ?: $xml;
            $xml = preg_replace('/<\/w:(p|tr)>/', "\n", $xml) ?: $xml;
            $xml = preg_replace('/<\/w:tc>/', "\t", $xml) ?: $xml;
            $parts[] = strip_tags($xml);
        }

        $zip->close();

        return $this->cleanDocumentText(implode("\n", $parts));
    }

    private function cleanDocumentText(string $text): string
    {
        $text = html_entity_decode($text, ENT_QUOTES | ENT_XML1, 'UTF-8');
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = strtr($text, [
            'Ã¡' => 'á', 'Ã©' => 'é', 'Ã­' => 'í', 'Ã³' => 'ó', 'Ãº' => 'ú',
            'Ã' => 'Á', 'Ã‰' => 'É', 'Ã' => 'Í', 'Ã“' => 'Ó', 'Ãš' => 'Ú',
            'Ã±' => 'ñ', 'Ã‘' => 'Ñ', 'Ã¼' => 'ü', 'Ãœ' => 'Ü',
            'â€“' => '-', 'â€”' => '-', 'â€œ' => '"', 'â€' => '"', 'â€˜' => "'", 'â€™' => "'",
            'Â°' => '°', 'Âº' => 'º', 'Âª' => 'ª', 'Â¿' => '¿', 'Â¡' => '¡',
        ]);
        $text = strtr($text, [
            'Ã¡' => 'á', 'Ã©' => 'é', 'Ã­' => 'í', 'Ã³' => 'ó', 'Ãº' => 'ú',
            'Ã' => 'Á', 'Ã‰' => 'É', 'Ã' => 'Í', 'Ã“' => 'Ó', 'Ãš' => 'Ú',
            'Ã±' => 'ñ', 'Ã‘' => 'Ñ', 'Ã¼' => 'ü', 'Ãœ' => 'Ü',
            'â€“' => '-', 'â€”' => '-', 'â€œ' => '"', 'â€' => '"',
            'â€˜' => "'", 'â€™' => "'", 'Â°' => '°', 'Âº' => 'º',
            'Âª' => 'ª', 'Â¿' => '¿', 'Â¡' => '¡',
        ]);
        $text = str_replace(["\u{00A0}", "\u{200B}"], ' ', $text);
        $lines = array_map(
            fn (string $line): string => trim(preg_replace('/[ \t]+/', ' ', $line) ?: ''),
            preg_split('/\R+/', $text) ?: [],
        );

        return trim(implode("\n", array_values(array_filter($lines, fn (string $line): bool => $line !== ''))));
    }

    private function normalizeLocation(array $analysis): array
    {
        $detected = $analysis['ubicacion_detectada'] ?? [];
        $text = $this->text($analysis['lugar_trabajo'] ?? data_get($detected, 'texto') ?? '');
        $municipalityText = $this->text(data_get($detected, 'municipio') ?? '');
        $departmentText = $this->text(data_get($detected, 'departamento') ?? '');
        $confidence = $this->normalize((string) data_get($detected, 'confianza', ''));
        $municipality = $this->findMunicipality($municipalityText) ?: $this->findMunicipality($text);

        if ($municipality && ! in_array($confidence, ['baja', 'no especificado', 'no_especificado'], true)) {
            $municipalityName = $this->displayCatalogName($municipality['name']);
            $department = $municipality['department'];

            return [
                'department' => $department,
                'location' => $municipalityName,
                'municipality' => [
                    'municipality' => $municipalityName,
                    'department' => $department,
                    'is_main_city' => in_array($this->normalize($municipalityName), ['la paz', 'cochabamba', 'santa cruz de la sierra', 'oruro', 'potosi', 'sucre', 'tarija', 'trinidad', 'cobija'], true),
                ],
                'source' => 'municipality_catalog',
                'detected_text' => $text ?: $municipalityText,
                'description' => "{$municipalityName}, Municipio de {$municipalityName}, Departamento de {$department}",
            ];
        }

        $department = $this->findDepartment($departmentText) ?: $this->findDepartment($text);
        if ($department && ! in_array($confidence, ['baja', 'no especificado', 'no_especificado'], true)) {
            return [
                'department' => $department,
                'location' => $text ?: $department,
                'municipality' => null,
                'source' => 'department_detected',
                'detected_text' => $text ?: $departmentText,
                'description' => $text ?: $department,
            ];
        }

        return [
            'department' => 'No especificado',
            'location' => 'No especificado',
            'municipality' => null,
            'source' => 'manual_review',
            'detected_text' => $text ?: $municipalityText ?: $departmentText ?: null,
            'description' => 'No especificado',
        ];
    }

    private function displayCatalogName(string $name): string
    {
        $particles = ['de', 'del', 'la', 'las', 'los', 'y', 'el'];
        $words = preg_split('/\s+/', Str::headline($name)) ?: [];

        return collect($words)
            ->filter(fn (string $word): bool => $word !== '')
            ->values()
            ->map(function (string $word, int $index) use ($particles): string {
                $lower = Str::lower($word);

                return $index > 0 && in_array($lower, $particles, true)
                    ? $lower
                    : $word;
            })
            ->implode(' ');
    }

    private function findMunicipality(string $value): ?array
    {
        $needle = $this->normalize($value);
        if ($needle === '' || $needle === 'no especificado') {
            return null;
        }

        $municipalities = config('bolivia_municipalities', []);

        if (isset($municipalities[$needle])) {
            return ['name' => $needle, 'department' => $municipalities[$needle]];
        }

        foreach ($municipalities as $name => $department) {
            $normalizedName = $this->normalize((string) $name);
            if (mb_strlen($normalizedName) >= 5 && Str::contains($needle, $normalizedName)) {
                return ['name' => $name, 'department' => $department];
            }
        }

        return null;
    }

    private function findDepartment(string $value): ?string
    {
        $needle = $this->normalize($value);
        if ($needle === '' || $needle === 'no especificado') {
            return null;
        }

        $departments = collect(config('bolivia_municipalities', []))
            ->values()
            ->unique()
            ->values();

        foreach ($departments as $department) {
            $normalized = $this->normalize((string) $department);
            if ($needle === $normalized || Str::contains($needle, $normalized)) {
                return (string) $department;
            }
        }

        return null;
    }

    private function resolveCompany(string $name, string $userId): Company
    {
        $name = trim($name) !== '' ? Str::limit(trim($name), 255, '') : 'SICOES';
        $company = Company::withTrashed()->where('company_name', $name)->first();

        if ($company) {
            if (method_exists($company, 'restore') && $company->trashed()) {
                $company->restore();
                Cache::forget('companies');
            }

            return $company;
        }

        $typeId = CompanyType::where('company_type_name', 'like', 'P%blica')->value('id') ?: CompanyType::value('id') ?: 1;

        $company = Company::create([
            'company_name' => $name,
            'description' => 'Entidad importada automaticamente desde SICOES.',
            'company_image' => 'empresas/tbn-new-default.webp',
            'company_type_id' => $typeId,
            'user_id' => $userId,
        ]);

        Cache::forget('companies');

        return $company;
    }

    private function resolveLocations(?string $department): array
    {
        $department = trim((string) $department);
        $department = $department !== '' && $this->normalize($department) !== 'no especificado'
            ? $department
            : 'No especificado';

        $location = Location::all(['id', 'location_name'])->first(
            fn (Location $location): bool => $this->normalize($location->location_name) === $this->normalize($department),
        ) ?: Location::create(['location_name' => Str::limit($department, 255, '')]);

        Cache::forget('locations');

        return [(int) $location->id];
    }

    private function documentAttachments(array $document): array
    {
        $path = (string) ($document['path'] ?? '');
        if (! is_file($path)) {
            return [];
        }

        $slug = basename(dirname($path));
        $filename = basename($path);
        $publicPath = 'convocatorias/sicoes/'.$slug.'/'.$filename;

        if (! Storage::disk('public')->exists($publicPath)) {
            $contents = @file_get_contents($path);
            if ($contents === false) {
                return [];
            }

            if (! Storage::disk('public')->put($publicPath, $contents)) {
                return [];
            }
        }

        return [[
            'url' => $publicPath,
            'original_name' => $filename,
            'source' => 'sicoes',
            'cuce' => $document['cuce'] ?? null,
            'document_id' => $document['document_id'] ?? null,
            'document_hash' => $document['document_hash'] ?? null,
            'local_path' => $path,
            'attached_at' => now()->toIso8601String(),
        ]];
    }

    private function mergeAttachments(array $current, array $incoming): array
    {
        return collect([...$current, ...$incoming])
            ->filter(fn ($attachment): bool => is_array($attachment) && ! empty($attachment['url']))
            ->unique(fn (array $attachment): string => (string) $attachment['url'])
            ->values()
            ->all();
    }

    private function description(array $analysis, array $location, string $cuce, array $warnings): string
    {
        $duration = $this->fieldOrUnspecified($analysis['duracion_contrato'] ?? null);
        $mode = $this->fieldOrUnspecified($analysis['modalidad_postulacion'] ?? null);
        $salaryDetail = $this->salaryDetail($analysis);
        $evidence = $analysis['evidencias'] ?? [];

        $parts = [
            '<p><strong>LUGAR DEL TRABAJO:</strong></p>',
            '<p>'.e($location['description'] ?: 'No especificado').'</p>',
            '<p><br></p>',
            '<p><strong>DURACION DEL CONTRATO:</strong></p>',
            '<p>'.e($duration).'</p>',
            '<p><br></p>',
            '<p><strong>MODALIDAD DE POSTULACION:</strong></p>',
            '<p>'.e($mode).'</p>',
            '<p><br></p>',
            '<p><strong>CUCE:</strong></p>',
            '<p>'.e($cuce ?: 'No especificado').'</p>',
            '<p><br></p>',
            '<p><strong>DETALLE DE SUELDOS:</strong></p>',
            $this->paragraphs($salaryDetail),
            '<p><br></p>',
            '<p><strong>EVIDENCIAS PARA REVISION:</strong></p>',
            $this->evidenceParagraph('Lugar', $evidence['lugar_trabajo'] ?? null),
            $this->evidenceParagraph('Duracion', $evidence['duracion_contrato'] ?? null),
            $this->evidenceParagraph('Modalidad', $evidence['modalidad_postulacion'] ?? null),
            $this->evidenceParagraph('Sueldo', $evidence['sueldo'] ?? null),
        ];

        if ($warnings) {
            $parts[] = '<p><br></p>';
            $parts[] = '<p><strong>ADVERTENCIAS DE REVISION:</strong></p>';
            $parts[] = $this->paragraphs($warnings);
        }

        $parts[] = '<p><br></p>';
        $parts[] = '<p>¡Impulsa tu futuro profesional! Esta convocatoria fue verificada por el equipo de <strong>TRABAJONAUTAS.COM</strong> y representa una excelente oportunidad de crecimiento para ti. ¡No la dejes pasar!</p>';
        $parts[] = '<p><br></p>';
        $parts[] = '<p><strong>Fuente:</strong> www.sicoes.gob.bo</p>';
        $parts[] = '<p>Descarga todos los detalles en el/los archivo(s) adjunto(s):</p>';

        return implode('', array_filter($parts));
    }

    private function errorDescription(string $cuce, string $errorType, string $error): string
    {
        return implode('', [
            '<p><strong>LUGAR DEL TRABAJO:</strong></p><p>No especificado</p><p><br></p>',
            '<p><strong>DURACION DEL CONTRATO:</strong></p><p>No especificado</p><p><br></p>',
            '<p><strong>MODALIDAD DE POSTULACION:</strong></p><p>No especificado</p><p><br></p>',
            '<p><strong>CUCE:</strong></p><p>'.e($cuce ?: 'No especificado').'</p><p><br></p>',
            '<p><strong>DETALLE DE SUELDOS:</strong></p><p>No especificado</p><p><br></p>',
            '<p><strong>ERROR DE PROCESAMIENTO:</strong></p>',
            '<p>'.e($errorType.': '.$error).'</p>',
        ]);
    }

    private function salaryDetail(array $analysis): array
    {
        $detail = $this->fieldOrUnspecified($analysis['detalle_sueldos'] ?? data_get($analysis, 'sueldo.detalle'));
        $salaryRows = data_get($analysis, 'sueldo.sueldos', []);
        $lines = [];

        if (is_array($salaryRows)) {
            foreach ($salaryRows as $row) {
                if (! is_array($row)) {
                    continue;
                }

                $role = $this->text($row['cargo'] ?? '');
                $amount = $this->text($row['monto'] ?? '');
                $period = $this->text($row['periodicidad'] ?? '');
                $line = trim(($role ? "{$role}: " : '').trim("{$amount} {$period}"));

                if ($line !== '') {
                    $lines[] = $line;
                }
            }
        }

        return $lines ?: [$detail];
    }

    private function paragraphs(array|string $lines): string
    {
        return collect(is_array($lines) ? $lines : preg_split('/\R+/', $lines))
            ->map(fn ($line): string => $this->fieldOrUnspecified($line))
            ->filter()
            ->map(fn (string $line): string => '<p>'.e($line).'</p>')
            ->implode('');
    }

    private function evidenceParagraph(string $label, mixed $value): string
    {
        $value = $this->fieldOrUnspecified($value);

        return '<p><strong>'.e($label).':</strong> '.e($value).'</p>';
    }

    private function fieldOrUnspecified(mixed $value): string
    {
        $value = $this->text($value);

        return $value !== '' && $this->normalize($value) !== 'no especificado'
            ? $value
            : 'No especificado';
    }

    private function shouldDiscard(array $analysis): bool
    {
        if ((bool) ($analysis['debe_descartarse'] ?? true)) {
            return true;
        }

        $type = (string) ($analysis['tipo_oportunidad'] ?? 'no_determinado');
        if (in_array($type, ['empresa_consultora', 'bienes_servicios', 'obra', 'otro', 'no_determinado'], true)) {
            return true;
        }

        if (empty($analysis['es_oportunidad_consultor_persona'])) {
            return true;
        }

        return $this->hasStrongDiscardSignals($analysis);
    }

    private function classificationData(array $analysis): array
    {
        return [
            'eligible' => (bool) ($analysis['eligible'] ?? ! ($analysis['debe_descartarse'] ?? true)),
            'contract_type' => $analysis['contract_type'] ?? $this->eligibilityClassifier->contractTypeFromAnalysis($analysis),
            'es_oportunidad_consultor_persona' => (bool) ($analysis['es_oportunidad_consultor_persona'] ?? false),
            'tipo_oportunidad' => $analysis['tipo_oportunidad'] ?? 'no_determinado',
            'debe_descartarse' => (bool) ($analysis['debe_descartarse'] ?? true),
            'motivo_descarte' => $analysis['motivo_descarte'] ?? null,
            'evidencia_clasificacion' => $analysis['evidencia_clasificacion'] ?? null,
        ];
    }

    private function classificationWarnings(array $analysis): array
    {
        $warnings = [];
        $type = (string) ($analysis['tipo_oportunidad'] ?? 'no_determinado');

        if (
            in_array($type, ['consultor_linea', 'consultor_producto', 'consultoria_individual'], true)
            && ! $this->shouldDiscardByType($analysis)
            && $this->hasStrongBusinessEntitySignals($analysis)
        ) {
            $warnings[] = 'La IA marco consultoria individual, pero la evidencia menciona empresa/persona juridica. Revisar antes de publicar.';
        }

        return $warnings;
    }

    private function shouldDiscardByType(array $analysis): bool
    {
        return in_array((string) ($analysis['tipo_oportunidad'] ?? 'no_determinado'), ['empresa_consultora', 'bienes_servicios', 'obra', 'otro', 'no_determinado'], true);
    }

    private function hasStrongDiscardSignals(array $analysis): bool
    {
        $text = $this->classificationSearchText($analysis);

        return Str::contains($text, [
            'empresa consultora',
            'firma consultora',
            'empresa especializada',
            'persona juridica',
            'sociedad comercial',
            'matricula de comercio',
            'matricula comercio',
            'nit empresarial',
            'experiencia institucional',
            'propuesta empresarial',
            'equipo empresarial',
            'compra de bienes',
            'adquisicion de bienes',
            'provision de bienes',
            'provision de materiales',
        ]);
    }

    private function hasStrongBusinessEntitySignals(array $analysis): bool
    {
        $text = $this->classificationSearchText($analysis);

        return Str::contains($text, [
            'empresa consultora',
            'firma consultora',
            'empresa especializada',
            'persona juridica',
            'sociedad comercial',
            'matricula de comercio',
            'matricula comercio',
            'nit empresarial',
            'experiencia institucional',
            'propuesta empresarial',
            'equipo empresarial',
        ]);
    }

    private function classificationSearchText(array $analysis): string
    {
        return $this->normalize(implode(' ', array_filter([
            $analysis['titulo_objeto'] ?? null,
            $analysis['tipo_oportunidad'] ?? null,
            $analysis['motivo_descarte'] ?? null,
            $analysis['evidencia_clasificacion'] ?? null,
            $analysis['descripcion'] ?? null,
            data_get($analysis, 'evidencias.profesiones'),
            data_get($analysis, 'evidencias.modalidad_postulacion'),
        ])));
    }

    private function discardCategory(array $analysis): string
    {
        $type = (string) ($analysis['tipo_oportunidad'] ?? 'no_determinado');

        if (in_array($type, ['empresa_consultora', 'bienes_servicios', 'obra'], true) || $this->hasStrongDiscardSignals($analysis)) {
            return 'empresa_bienes_obra';
        }

        return 'no_consultor_persona';
    }

    private function discardReason(array $analysis): string
    {
        $reason = $this->text($analysis['motivo_descarte'] ?? '');

        if ($reason !== '') {
            return $reason;
        }

        return match ((string) ($analysis['tipo_oportunidad'] ?? 'no_determinado')) {
            'empresa_consultora' => 'Convocatoria orientada a empresa/persona juridica o firma consultora.',
            'bienes_servicios' => 'Convocatoria orientada a bienes o servicios no laborales.',
            'obra' => 'Convocatoria orientada a obra o construccion.',
            'otro' => 'No corresponde a una oportunidad laboral para consultor individual.',
            default => 'No hay evidencia suficiente para confirmar consultoria individual/persona natural.',
        };
    }

    private function warnings(array $document, array $analysis): array
    {
        $warnings = [];
        $rowCuce = $this->normalize((string) ($document['cuce'] ?? ''));
        $aiCuce = $this->normalize((string) ($analysis['cuce'] ?? ''));

        if ($rowCuce !== '' && $aiCuce !== '' && $rowCuce !== $aiCuce) {
            $warnings[] = 'El CUCE de la fila SICOES no coincide con el CUCE detectado en el documento.';
        }

        foreach (($analysis['advertencias'] ?? []) as $warning) {
            $warning = $this->text($warning);
            if ($warning !== '') {
                $warnings[] = $warning;
            }
        }

        return array_values(array_unique($warnings));
    }

    private function manualReviewRequired(array $analysis, array $location, array $warnings): bool
    {
        return $warnings !== []
            || $this->normalize($location['department']) === 'no especificado'
            || (bool) data_get($analysis, 'sueldo.revision_manual', false)
            || (int) data_get($analysis, 'sueldo.valor', 0) === 0;
    }

    private function manualReviewReasons(array $analysis, array $location, array $warnings): array
    {
        $reasons = $warnings;

        if ($this->normalize($location['department']) === 'no especificado') {
            $reasons[] = 'Ubicacion no determinada con seguridad.';
        }

        if ((bool) data_get($analysis, 'sueldo.revision_manual', false) || (int) data_get($analysis, 'sueldo.valor', 0) === 0) {
            $reasons[] = 'Sueldo requiere revision manual.';
        }

        return array_values(array_unique($reasons));
    }

    private function existingPublishedAnnouncementExists(string $sourceUrl, ?string $sourceHash = null, ?string $legacySourceHash = null, mixed $convocatoriaId = null, ?string $rowSourceUrl = null): bool
    {
        return $this->findPublishedAnnouncementId($sourceUrl, $sourceHash, $legacySourceHash, $rowSourceUrl, $convocatoriaId) !== null;
    }

    private function findPublishedAnnouncementId(string $sourceUrl, ?string $sourceHash = null, ?string $legacySourceHash = null, ?string $rowSourceUrl = null, mixed $convocatoriaId = null): ?int
    {
        if ($convocatoriaId && Announcement::query()->whereKey($convocatoriaId)->exists()) {
            return (int) $convocatoriaId;
        }

        foreach (array_filter([$sourceUrl, $rowSourceUrl]) as $url) {
            $id = Announcement::query()->where('source_url', $url)->value('id');
            if ($id) {
                return (int) $id;
            }
        }

        foreach (array_filter([$sourceHash, $legacySourceHash]) as $hash) {
            $id = Announcement::query()->where('source_hash', $hash)->value('id');
            if ($id) {
                return (int) $id;
            }
        }

        return null;
    }

    private function markPublishedPreviewAsSeen(string $sourceUrl, array $rawData): void
    {
        $preview = BotVacancyPreview::where('source_url', $sourceUrl)
            ->where('status', 'published')
            ->first();

        if (! $preview) {
            return;
        }

        $preview->update([
            'raw_data' => array_merge($preview->raw_data ?? [], $rawData),
        ]);
    }

    private function markDiscardedPreviewRemoved(string $sourceUrl, ?string $batchId, array $rawData): void
    {
        $query = BotVacancyPreview::where('source_url', $sourceUrl)
            ->whereIn('status', ['preview', 'edited', 'error']);

        if ($batchId) {
            $query->where('scrape_batch_id', $batchId);
        }

        $preview = $query->first();

        if (! $preview) {
            return;
        }

        $preview->update([
            'removed_from_batch_at' => now(),
            'raw_data' => array_merge($preview->raw_data ?? [], $rawData, [
                'removed_from_batch_reason' => 'discarded_by_ai_classification',
            ]),
        ]);
    }

    private function documentSourceUrl(array $document): string
    {
        $base = (string) ($document['source_url'] ?? '');
        if ($base === '') {
            $base = 'https://www.sicoes.gob.bo/portal/contrataciones/ficha/fichaProceso.php?cp='.rawurlencode((string) ($document['cuce'] ?? ''));
        }

        return $base.'#doc-'.substr((string) ($document['document_id'] ?? hash('sha256', $base)), 0, 20);
    }

    private function textPath(string $basePath, string $slug, ?int $index, string $cuce): ?string
    {
        $dir = $basePath.DIRECTORY_SEPARATOR.'salida'.DIRECTORY_SEPARATOR.'resultados'.DIRECTORY_SEPARATOR.$slug.DIRECTORY_SEPARATOR.'textos-extraidos';
        if (! is_dir($dir) || ! $cuce) {
            return null;
        }

        if ($index !== null) {
            $exact = $dir.DIRECTORY_SEPARATOR.str_pad((string) $index, 2, '0', STR_PAD_LEFT).'_'.$cuce.'.txt';
            if (is_file($exact)) {
                return $exact;
            }
        }

        $matches = glob($dir.DIRECTORY_SEPARATOR.'*'.$cuce.'*.txt') ?: [];

        return $matches[0] ?? null;
    }

    private function indexAndCuceFromFilename(string $filename): array
    {
        if (preg_match('/^(\d+)_([0-9]{2}-[0-9]{4}-[0-9]{2}-[0-9]+-\d+-\d+)/', $filename, $matches)) {
            return [(int) $matches[1], $matches[2]];
        }

        if (preg_match('/([0-9]{2}-[0-9]{4}-[0-9]{2}-[0-9]+-\d+-\d+)/', $filename, $matches)) {
            return [null, $matches[1]];
        }

        return [null, ''];
    }

    private function downloadRecord(array $report, string $cuce, string $filename): ?array
    {
        foreach ($report as $row) {
            if (! is_array($row)) {
                continue;
            }

            if (($row['cuce'] ?? '') !== $cuce) {
                continue;
            }

            if (($row['archivo'] ?? '') === 'existente' || ($row['archivo'] ?? '') === $filename) {
                return $row;
            }
        }

        return null;
    }

    private function previewTitle(array $document, array $analysis): string
    {
        return Str::limit($this->fieldOrUnspecified(
            $analysis['titulo_objeto']
            ?? $document['title']
            ?? 'Convocatoria SICOES '.($document['cuce'] ?? ''),
        ), 255, '');
    }

    private function expirationDate(array $document): string
    {
        $value = $this->text($document['expires_at'] ?? '');

        if ($value === '') {
            return '';
        }

        foreach (['d/m/Y H:i:s', 'd/m/Y H:i', 'Y-m-d H:i:s', 'Y-m-d H:i'] as $format) {
            try {
                return Carbon::createFromFormat($format, $value)->format('Y-m-d H:i:s');
            } catch (\Throwable) {
                // Try the next exact source format.
            }
        }

        foreach (['d/m/Y', 'Y-m-d'] as $format) {
            try {
                return Carbon::createFromFormat($format, $value)->setTime(23, 59)->format('Y-m-d H:i:s');
            } catch (\Throwable) {
                // Try the next exact source format.
            }
        }

        return '';
    }

    private function dateSlug(mixed $value): ?string
    {
        $value = trim((string) $value);
        if ($value === '') {
            return null;
        }

        if (preg_match('/^\d{2}-\d{2}-\d{4}$/', $value)) {
            return $value;
        }

        if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $value)) {
            return str_replace('/', '-', $value);
        }

        try {
            return \Carbon\Carbon::parse($value)->format('d-m-Y');
        } catch (\Throwable) {
            return null;
        }
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

    private function safeSourceItem(array $row): array
    {
        $safe = array_intersect_key($row, array_flip([
            'cuce',
            'entidad',
            'empresa',
            'objetoContratacion',
            'objeto_contratacion',
            'titulo_convocatoria',
            'fechaPublicacion',
            'fecha_publicacion',
            'fechaPresentacion',
            'fecha_presentacion',
            'modalidad',
            'estado',
            'ficha',
            'pagina',
            'numero',
        ]));

        if (isset($safe['ficha'])) {
            $safe['ficha'] = SensitiveDataSanitizer::url($safe['ficha'], 1000);
        }

        return SensitiveDataSanitizer::context($safe, 1000, 3, 30);
    }

    private function storageRelativePath(mixed $path): ?string
    {
        if ($path === null || trim((string) $path) === '') {
            return null;
        }

        $normalized = str_replace('\\', '/', (string) $path);
        $storageRoot = rtrim(str_replace('\\', '/', storage_path('app')), '/').'/';

        if (str_starts_with(strtolower($normalized), strtolower($storageRoot))) {
            return ltrim(substr($normalized, strlen($storageRoot)), '/');
        }

        return SensitiveDataSanitizer::basename($normalized);
    }

    private function text(mixed $value): string
    {
        if (is_array($value) || is_object($value)) {
            $value = json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }

        return trim(html_entity_decode((string) $value, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
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
