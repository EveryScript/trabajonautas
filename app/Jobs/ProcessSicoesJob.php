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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

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
        $batch = $this->batch();

        if ($this->runId && ! $batch) {
            throw new \RuntimeException('El lote SICOES no existe o no pertenece a la empresa indicada.');
        }

        if ($batch?->isTerminal()) {
            Log::warning('SICOES omitio una entrega duplicada para un lote terminal.', [
                'date' => $this->date,
                'bot_company_id' => $this->botCompanyId,
                'run_id' => $this->runId,
                'batch_status' => $batch->status,
            ]);

            return [
                'status' => strtoupper((string) $batch->status),
                'runner_status' => 'SKIPPED_TERMINAL',
                'batch_status' => $batch->status,
                'skipped_terminal_batch' => true,
            ];
        }

        $batch?->update([
            'status' => SicoesScrapeBatch::STATUS_RUNNING,
            'started_at' => now(),
        ]);

        $this->putProgress([
            'run_id' => $this->runId,
            'status' => SicoesScrapeBatch::STATUS_RUNNING,
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
        $runnerStatus = strtoupper((string) ($run['status'] ?? 'UNKNOWN'));

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
        $errorCount = $this->errorCount($result);
        $batchStatus = $this->terminalStatus($runnerStatus, $result, $errorCount);
        $result['runner_status'] = $runnerStatus;
        $result['batch_status'] = $batchStatus;

        Log::info(
            $batchStatus === SicoesScrapeBatch::STATUS_COMPLETED
                ? 'SICOES documentos previsualizados correctamente.'
                : 'SICOES finalizo con resultados parciales.',
            [
                'date' => $this->date,
                'run_id' => $this->runId,
                'bot_company_id' => $this->botCompanyId,
                'runner_status' => $runnerStatus,
                'batch_status' => $batchStatus,
                'total' => $result['total_items_feed'] ?? null,
                'saved' => $result['saved'] ?? null,
                'updated' => $result['updated'] ?? null,
                'document_processed' => $result['document_processed'] ?? null,
                'document_errors' => $errorCount,
                'document_discarded' => $result['document_discarded'] ?? null,
                'preclassified_discards' => $result['preclassified_discards'] ?? null,
                'discarded_not_individual_consultant' => $result['discarded_not_individual_consultant'] ?? null,
                'discarded_company_or_goods' => $result['discarded_company_or_goods'] ?? null,
                'ai_provider' => $result['ai_provider'] ?? null,
                'ai_calls' => $result['ai_calls'] ?? null,
                'ai_cache_hits' => $result['ai_cache_hits'] ?? null,
                'ai_errors' => $result['ai_errors'] ?? null,
            ],
        );

        $this->putProgress([
            'status' => $batchStatus,
            'total' => $result['total_items_feed'] ?? 0,
            'processed' => $result['document_processed'] ?? ($result['total_items_feed'] ?? 0),
            'saved' => $result['saved'] ?? 0,
            'updated' => $result['updated'] ?? 0,
            'failed' => $errorCount,
            'discarded' => $result['document_discarded'] ?? 0,
            'preclassified_discards' => $result['preclassified_discards'] ?? 0,
            'discarded_not_individual_consultant' => $result['discarded_not_individual_consultant'] ?? 0,
            'discarded_company_or_goods' => $result['discarded_company_or_goods'] ?? 0,
            'ai_calls' => $result['ai_calls'] ?? 0,
            'ai_cache_hits' => $result['ai_cache_hits'] ?? 0,
            'ai_errors' => $result['ai_errors'] ?? 0,
            'shown_in_batch' => $result['shown_in_batch'] ?? 0,
            'last_step' => $batchStatus === SicoesScrapeBatch::STATUS_COMPLETED
                ? 'SICOES completado. Revisa los previews por documento antes de publicar.'
                : 'SICOES finalizo parcialmente. Revisa los errores y previews antes de publicar.',
            'finished_at' => now()->toDateTimeString(),
        ]);

        $this->batch()?->update([
            'status' => $batchStatus,
            'documents_found' => $this->documentsFound($run, $result),
            'documents_downloaded' => (int) ($result['total_items_feed'] ?? 0),
            'documents_processed' => (int) ($result['document_processed'] ?? 0),
            'previews_count' => (int) ($result['shown_in_batch'] ?? 0),
            'discarded_count' => (int) ($result['document_discarded'] ?? 0),
            'errors_count' => $errorCount,
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
        $batch = $this->batch();
        $exceptionType = $this->exceptionType($exception);

        if ($batch?->isTerminal()) {
            Log::warning('SICOES job fallo despues de alcanzar un estado terminal; se preservo el lote.', [
                'date' => $this->date,
                'bot_company_id' => $this->botCompanyId,
                'run_id' => $this->runId,
                'batch_status' => $batch->status,
                'exception_type' => $exceptionType,
            ]);

            return;
        }

        if (! $this->canWriteProgress()) {
            $this->markBatchFailed($exceptionType, 1);
            Log::warning('SICOES job fallo obsoleto ignorado en progreso.', [
                'date' => $this->date,
                'bot_company_id' => $this->botCompanyId,
                'run_id' => $this->runId,
                'exception_type' => $exceptionType,
            ]);

            return;
        }

        $errorCount = max(1, (int) ($current['failed'] ?? 0) + 1);
        $this->putProgress([
            ...$current,
            'run_id' => $this->runId,
            'status' => SicoesScrapeBatch::STATUS_FAILED,
            'failed' => $errorCount,
            'last_step' => "Job SICOES fallo ({$exceptionType}).",
            'failed_at' => now()->toDateTimeString(),
        ]);

        Log::error('SICOES job fallo.', [
            'date' => $this->date,
            'bot_company_id' => $this->botCompanyId,
            'run_id' => $this->runId,
            'exception_type' => $exceptionType,
            'exception_code' => $exception->getCode(),
        ]);

        $this->markBatchFailed($exceptionType, $errorCount);
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
        return $this->runId
            ? SicoesScrapeBatch::query()
                ->whereKey($this->runId)
                ->where('bot_company_id', $this->botCompanyId)
                ->first()
            : null;
    }

    private function errorCount(array $result): int
    {
        $errors = $result['errors'] ?? [];

        return max(
            0,
            (int) ($result['document_errors'] ?? 0),
            is_countable($errors) ? count($errors) : 0,
        );
    }

    private function terminalStatus(string $runnerStatus, array $result, int $errorCount): string
    {
        $total = max(0, (int) ($result['total_items_feed'] ?? 0));
        $processed = max(0, (int) ($result['document_processed'] ?? 0));

        return $runnerStatus === 'OK'
            && $errorCount === 0
            && $processed >= $total
                ? SicoesScrapeBatch::STATUS_COMPLETED
                : SicoesScrapeBatch::STATUS_PARTIAL;
    }

    private function batchSummary(array $result): array
    {
        return [
            'runner_status' => Str::limit((string) ($result['runner_status'] ?? 'UNKNOWN'), 32, ''),
            'batch_status' => Str::limit((string) ($result['batch_status'] ?? SicoesScrapeBatch::STATUS_PARTIAL), 32, ''),
            'total_items_feed' => (int) ($result['total_items_feed'] ?? 0),
            'saved' => (int) ($result['saved'] ?? 0),
            'updated' => (int) ($result['updated'] ?? 0),
            'shown_in_batch' => (int) ($result['shown_in_batch'] ?? 0),
            'already_published' => (int) ($result['already_published'] ?? 0),
            'already_previewed' => (int) ($result['already_previewed'] ?? 0),
            'reactivated_deleted' => (int) ($result['reactivated_deleted'] ?? 0),
            'skipped_without_cuce' => (int) ($result['skipped_without_cuce'] ?? 0),
            'document_processed' => (int) ($result['document_processed'] ?? 0),
            'document_errors' => $this->errorCount($result),
            'document_discarded' => (int) ($result['document_discarded'] ?? 0),
            'preclassified_discards' => (int) ($result['preclassified_discards'] ?? 0),
            'discarded_not_individual_consultant' => (int) ($result['discarded_not_individual_consultant'] ?? 0),
            'discarded_company_or_goods' => (int) ($result['discarded_company_or_goods'] ?? 0),
            'ai_provider' => Str::limit((string) ($result['ai_provider'] ?? ''), 64, ''),
            'ai_model' => Str::limit((string) ($result['ai_model'] ?? ''), 120, ''),
            'ai_calls' => (int) ($result['ai_calls'] ?? 0),
            'ai_cache_hits' => (int) ($result['ai_cache_hits'] ?? 0),
            'ai_errors' => (int) ($result['ai_errors'] ?? 0),
            'ai_prompt_tokens' => (int) ($result['ai_prompt_tokens'] ?? 0),
            'ai_output_tokens' => (int) ($result['ai_output_tokens'] ?? 0),
            'ai_total_tokens' => (int) ($result['ai_total_tokens'] ?? 0),
        ];
    }

    private function markBatchFailed(string $exceptionType, int $errorCount): void
    {
        if (! $this->runId) {
            return;
        }

        DB::transaction(function () use ($exceptionType, $errorCount): void {
            $batch = SicoesScrapeBatch::query()
                ->whereKey($this->runId)
                ->where('bot_company_id', $this->botCompanyId)
                ->whereIn('status', [
                    SicoesScrapeBatch::STATUS_QUEUED,
                    SicoesScrapeBatch::STATUS_RUNNING,
                ])
                ->lockForUpdate()
                ->first();

            if (! $batch) {
                return;
            }

            $summary = is_array($batch->summary) ? $batch->summary : [];
            $summary['batch_status'] = SicoesScrapeBatch::STATUS_FAILED;
            $summary['failure'] = [
                'type' => Str::limit($exceptionType, 160, ''),
                'failed_at' => now()->toIso8601String(),
            ];

            $batch->update([
                'status' => SicoesScrapeBatch::STATUS_FAILED,
                'errors_count' => max(1, (int) $batch->errors_count, $errorCount),
                'summary' => $summary,
                'finished_at' => now(),
            ]);
        });
    }

    private function exceptionType(\Throwable $exception): string
    {
        $type = preg_replace('/[^A-Za-z0-9_]/', '', class_basename($exception));

        return $type !== '' ? Str::limit($type, 160, '') : 'Throwable';
    }

    private function documentsFound(array $run, array $result): int
    {
        return (int) ($result['total_items_feed'] ?? $run['sicoes_items'] ?? 0);
    }
}
