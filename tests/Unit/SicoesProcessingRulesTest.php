<?php

namespace Tests\Unit;

use App\Jobs\ProcessSicoesJob;
use App\Livewire\Admin\Bot\BotPreview;
use App\Services\Bot\SicoesDocumentImporterService;
use PHPUnit\Framework\Attributes\DataProvider;
use ReflectionClass;
use ReflectionMethod;
use Tests\TestCase;

class SicoesProcessingRulesTest extends TestCase
{
    public function test_analysis_cache_key_includes_prompt_version(): void
    {
        config()->set('services.sicoes_ai_provider', 'anthropic');
        config()->set('services.anthropic.model', 'claude-haiku-4-5-20251001');

        $importer = (new ReflectionClass(SicoesDocumentImporterService::class))->newInstanceWithoutConstructor();
        $method = new ReflectionMethod(SicoesDocumentImporterService::class, 'analysisKey');
        $method->setAccessible(true);
        $document = ['document_hash' => 'stable-document-hash'];

        $first = $method->invoke($importer, $document, 'prompt-v1');
        $same = $method->invoke($importer, $document, 'prompt-v1');
        $changed = $method->invoke($importer, $document, 'prompt-v2');

        $this->assertSame($first, $same);
        $this->assertNotSame($first, $changed);
    }

    public function test_documents_found_prefers_real_imported_documents(): void
    {
        $job = new ProcessSicoesJob('2026-07-07', 1, 'test-user', 'test-batch');
        $method = new ReflectionMethod(ProcessSicoesJob::class, 'documentsFound');
        $method->setAccessible(true);

        $this->assertSame(5, $method->invoke(
            $job,
            ['sicoes_items' => 1],
            ['total_items_feed' => 5],
        ));
    }

    #[DataProvider('expirationCases')]
    public function test_sicoes_expiration_preserves_real_time_and_controls_invalid_values(
        string $source,
        string $expected,
    ): void {
        $importer = (new ReflectionClass(SicoesDocumentImporterService::class))->newInstanceWithoutConstructor();
        $method = new ReflectionMethod(SicoesDocumentImporterService::class, 'expirationDate');
        $method->setAccessible(true);

        $this->assertSame($expected, $method->invoke($importer, ['expires_at' => $source]));
    }

    public static function expirationCases(): array
    {
        return [
            'date with time' => ['20/07/2026 15:00', '2026-07-20 15:00:00'],
            'date only' => ['20/07/2026', '2026-07-20 23:59:00'],
            'invalid text' => ['fecha irrelevante', ''],
        ];
    }

    public function test_sicoes_publication_never_uses_fifteen_day_fallback(): void
    {
        $component = (new ReflectionClass(BotPreview::class))->newInstanceWithoutConstructor();
        $method = new ReflectionMethod(BotPreview::class, 'expirationForAnnouncement');
        $method->setAccessible(true);

        $this->expectException(\InvalidArgumentException::class);
        $method->invoke($component, 'fecha irrelevante', true);
    }
}
