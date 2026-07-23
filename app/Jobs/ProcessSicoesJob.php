<?php

namespace App\Jobs;

use App\Models\SicoesScrapeBatch;
use App\Services\Bot\SicoesDocumentImporterService;
use App\Services\Bot\SicoesRunnerService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ProcessSicoesJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $timeout = 7500;

    public int $tries = 120;

    public int $maxExceptions = 1;

    public bool $failOnTimeout = true;

    public function __construct(
        public string $date,
        public int $botCompanyId,
        public string $userId,
        public ?string $runId = null,
    ) {}

    public function middleware(): array
    {
        return [
            (new WithoutOverlapping('sicoes-scraper'))
                ->releaseAfter(60)
                ->expireAfter($this->timeout + 300),
        ];
    }

    public function handle(SicoesRunnerService $runner, SicoesDocumentImporterService $importer): array
    {
        $this->batch()?->update([
            'status' => 'running',
            'started_at' => now(),
        ]);

        $this->putProgress([
            'run_id' => $this->runId,
            'status' => 'running',
            'date' => $this->date,
            'total' => 0,
            'processed' => 0,
            'saved' => 0,
            'updated' => 0,
            'failed' => 0,
            'last_step' => 'Job iniciado',
            'started_at' => now()->toDateTimeString(),
        ]);

        $import = $this->emptyImportSummary();
        $run = $runner->run(
            $this->date,
            onProgress: function (array $payload): void {
                Log::info('SICOES progreso.', [
                    'date' => $this->date,
                    'run_id' => $this->runId,
                    ...$payload,
                ]);

                $this->putProgress([
                    'status' => 'running',
                    'total' => (int) ($payload['total'] ?? Cache::get($this->progressKey(), [])['total'] ?? 0),
                    'last_cuce' => $payload['cuce'] ?? null,
                    'last_step' => $payload['message'] ?? 'SICOES progreso',
                    'updated_at' => now()->toDateTimeString(),
                ]);
            },
        );

        $import = $importer->importRun(
            run: $run,
            botCompanyId: $this->botCompanyId,
            userId: $this->userId,
            batchId: $this->runId,
            onProgress: function (array $payload) use (&$import): void {
                Log::info('SICOES documento procesado.', [
                    'date' => $this->date,
                    'run_id' => $this->runId,
                    ...$payload,
                ]);

                $this->putProgress([
                    'status' => 'running',
                    'total' => (int) ($payload['total'] ?? ($import['total_items_feed'] ?? 0)),
                    'processed' => (int) ($payload['processed'] ?? ($import['document_processed'] ?? 0)),
                    'saved' => (int) ($payload['saved'] ?? ($import['saved'] ?? 0)),
                    'updated' => (int) ($payload['updated'] ?? ($import['updated'] ?? 0)),
                    'failed' => (int) ($payload['failed'] ?? (($import['document_errors'] ?? 0) ?: count($import['errors'] ?? []))),
                    'discarded' => (int) ($payload['discarded'] ?? ($import['document_discarded'] ?? 0)),
                    'ai_calls' => (int) ($payload['ai_calls'] ?? ($import['ai_calls'] ?? 0)),
                    'ai_cache_hits' => (int) ($payload['ai_cache_hits'] ?? ($import['ai_cache_hits'] ?? 0)),
                    'ai_errors' => (int) ($payload['ai_errors'] ?? ($import['ai_errors'] ?? 0)),
                    'last_cuce' => $payload['cuce'] ?? null,
                    'last_preview_id' => $payload['preview_id'] ?? null,
                    'last_document' => $payload['document'] ?? null,
                    'last_step' => $payload['message'] ?? 'SICOES procesando documento con IA',
                    'updated_at' => now()->toDateTimeString(),
                ]);
            },
        );

        $result = [
            ...$run,
            ...$import,
        ];

        Log::info('SICOES documentos previsualizados correctamente.', [
            'date' => $this->date,
            'run_id' => $this->runId,
            'bot_company_id' => $this->botCompanyId,
            'json_path' => $run['json_path'] ?? null,
            'total' => $result['total_items_feed'] ?? null,
            'saved' => $result['saved'] ?? null,
            'updated' => $result['updated'] ?? null,
            'document_processed' => $result['document_processed'] ?? null,
            'document_errors' => $result['document_errors'] ?? null,
            'document_discarded' => $result['document_discarded'] ?? null,
            'preclassified_discards' => $result['preclassified_discards'] ?? null,
            'discarded_not_individual_consultant' => $result['discarded_not_individual_consultant'] ?? null,
            'discarded_company_or_goods' => $result['discarded_company_or_goods'] ?? null,
            'ai_provider' => $result['ai_provider'] ?? null,
            'ai_calls' => $result['ai_calls'] ?? null,
            'ai_cache_hits' => $result['ai_cache_hits'] ?? null,
            'ai_errors' => $result['ai_errors'] ?? null,
            'errors' => $result['errors'] ?? [],
        ]);

        $this->putProgress([
            'status' => 'finished',
            'total' => $result['total_items_feed'] ?? 0,
            'processed' => $result['document_processed'] ?? ($result['total_items_feed'] ?? 0),
            'saved' => $result['saved'] ?? 0,
            'updated' => $result['updated'] ?? 0,
            'failed' => ($result['document_errors'] ?? 0) ?: count($result['errors'] ?? []),
            'discarded' => $result['document_discarded'] ?? 0,
            'preclassified_discards' => $result['preclassified_discards'] ?? 0,
            'discarded_not_individual_consultant' => $result['discarded_not_individual_consultant'] ?? 0,
            'discarded_company_or_goods' => $result['discarded_company_or_goods'] ?? 0,
            'ai_calls' => $result['ai_calls'] ?? 0,
            'ai_cache_hits' => $result['ai_cache_hits'] ?? 0,
            'ai_errors' => $result['ai_errors'] ?? 0,
            'shown_in_batch' => $result['shown_in_batch'] ?? 0,
            'last_step' => 'SICOES finalizado. Revisa los previews por documento antes de publicar.',
            'finished_at' => now()->toDateTimeString(),
        ]);

        $this->batch()?->update([
            'status' => 'finished',
            'documents_found' => $this->documentsFound($run, $result),
            'documents_downloaded' => (int) ($result['total_items_feed'] ?? 0),
            'documents_processed' => (int) ($result['document_processed'] ?? 0),
            'previews_count' => (int) ($result['shown_in_batch'] ?? 0),
            'discarded_count' => (int) ($result['document_discarded'] ?? 0),
            'errors_count' => (int) ($result['document_errors'] ?? 0),
            'ai_calls' => (int) ($result['ai_calls'] ?? 0),
            'ai_cache_hits' => (int) ($result['ai_cache_hits'] ?? 0),
            'summary' => $this->batchSummary($result),
            'finished_at' => now(),
        ]);

        return $result;
    }

    public function failed(\Throwable $exception): void
    {
        $current = Cache::get($this->progressKey(), []);

        if (! $this->canWriteProgress()) {
            $this->batch()?->update([
                'status' => 'failed',
                'errors_count' => 1,
                'summary' => ['error' => \Illuminate\Support\Str::limit($exception->getMessage(), 1000, '')],
                'finished_at' => now(),
            ]);
            Log::warning('SICOES job fallo obsoleto ignorado en progreso.', [
                'date' => $this->date,
                'bot_company_id' => $this->botCompanyId,
                'run_id' => $this->runId,
                'message' => $exception->getMessage(),
            ]);

            return;
        }

        $this->putProgress([
            ...$current,
            'run_id' => $this->runId,
            'status' => 'failed',
            'failed' => ((int) ($current['failed'] ?? 0)) + 1,
            'last_step' => 'Job fallo: '.$exception->getMessage(),
            'failed_at' => now()->toDateTimeString(),
        ]);

        Log::error('SICOES job fallo.', [
            'date' => $this->date,
            'bot_company_id' => $this->botCompanyId,
            'run_id' => $this->runId,
            'message' => $exception->getMessage(),
        ]);

        $this->batch()?->update([
            'status' => 'failed',
            'errors_count' => max(1, (int) ($current['failed'] ?? 0) + 1),
            'summary' => ['error' => \Illuminate\Support\Str::limit($exception->getMessage(), 1000, '')],
            'finished_at' => now(),
        ]);
    }

    private function emptyImportSummary(): array
    {
        return [
            'total_items_feed' => 0,
            'saved' => 0,
            'updated' => 0,
            'shown_in_batch' => 0,
            'already_published' => 0,
            'already_previewed' => 0,
            'reactivated_deleted' => 0,
            'skipped_without_cuce' => 0,
            'ai_provider' => config('services.sicoes_ai_provider', 'anthropic'),
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
    }

    private function putProgress(array $data): void
    {
        if (! $this->canWriteProgress()) {
            return;
        }

        $current = Cache::get($this->progressKey(), []);

        Cache::put($this->progressKey(), [
            ...$current,
            ...$data,
        ], now()->addDay());
    }

    private function canWriteProgress(): bool
    {
        $current = Cache::get($this->progressKey(), []);
        $currentRunId = $current['run_id'] ?? null;

        if ($currentRunId && ! $this->runId) {
            return false;
        }

        if ($currentRunId && $this->runId && ! hash_equals((string) $currentRunId, (string) $this->runId)) {
            return false;
        }

        return true;
    }

    private function progressKey(): string
    {
        return 'sicoes:progress:'.str_replace(['/', '\\', ' '], '-', $this->date);
    }

    private function batch(): ?SicoesScrapeBatch
    {
        return $this->runId ? SicoesScrapeBatch::find($this->runId) : null;
    }

    private function batchSummary(array $result): array
    {
        return collect($result)
            ->except(['runner_output'])
            ->all();
    }

    private function documentsFound(array $run, array $result): int
    {
        return (int) ($result['total_items_feed'] ?? $run['sicoes_items'] ?? 0);
    }
}
