<?php

namespace App\Services\Bot;

use App\Models\Area;
use App\Models\BotCompany;
use App\Support\TlsVerification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class GeminiVacancyAnalyzer
{
    public function analyze(string $title, BotCompany|string $company, string $description): array
    {
        return $this->analyzeWithMeta($title, $company, $description)['data'];
    }

    public function analyzeWithMeta(string $title, BotCompany|string $company, string $description, array $options = []): array
    {
        $key = config('services.gemini.key');
        $model = trim((string) config('services.gemini.model', 'gemini-2.5-flash-lite'));
        $companyName = $company instanceof BotCompany ? $company->name : $company;
        $preparedDescription = $this->prepareDescription($description);

        if (! preg_match('/^[A-Za-z0-9._-]{1,100}$/', $model)) {
            return $this->metaError(
                errorType: 'invalid_configuration',
                error: 'GEMINI_MODEL contiene un valor no permitido.',
                used: false,
                model: 'invalid',
                extra: $preparedDescription['meta'],
            );
        }

        if (!empty($options['skip_due_to_quota'])) {
            return $this->metaError(
                errorType: 'quota_exceeded_skipped',
                error: 'Gemini quota excedida en este batch; se usó fallback.',
                used: false,
                model: $model,
                extra: [
                    'gemini_skipped_due_to_quota' => true,
                    ...$preparedDescription['meta'],
                ],
            );
        }

        if (!$key) {
            return $this->metaError(
                errorType: 'missing_api_key',
                error: 'GEMINI_API_KEY no configurada',
                used: false,
                model: $model,
                extra: $preparedDescription['meta'],
            );
        }

        try {
            $response = null;
            $lastThrowable = null;
            $attempts = 0;

            while ($attempts < 3) {
                $attempts++;

                try {
                    $response = $this->sendRequest($model, $key, $title, $companyName, $preparedDescription['text']);
                    $status = $response->status();

                    if (in_array($status, [500, 502, 503, 504], true) && $attempts < 3) {
                        usleep(1_000_000);
                        continue;
                    }

                    if ($status === 429 && $attempts < 2) {
                        usleep(2_000_000);
                        continue;
                    }

                    break;
                } catch (\Throwable $exception) {
                    $lastThrowable = $exception;
                    $type = $this->classifyThrowable($exception);

                    if ($type === 'timeout' && $attempts < 3) {
                        usleep(1_000_000);
                        continue;
                    }

                    throw $exception;
                }
            }

            if (!$response && $lastThrowable) {
                throw $lastThrowable;
            }

            $responseJson = $response->json();
            $usageMetadata = $this->usageMetadata($responseJson);

            if (!$response->successful()) {
                $status = $response->status();

                return $this->metaError(
                    errorType: $this->classifyHttpError($status, $response->body()),
                    error: $this->httpError($status, $response->body()),
                    used: true,
                    model: $model,
                    httpStatus: $status,
                    extra: [
                        'gemini_attempts' => $attempts,
                        ...$usageMetadata,
                        ...$preparedDescription['meta'],
                    ],
                );
            }

            $text = data_get($responseJson, 'candidates.0.content.parts.0.text');
            if (!$text) {
                return $this->metaError(
                    errorType: 'invalid_json',
                    error: 'Gemini devolvió una respuesta vacía.',
                    used: true,
                    model: $model,
                    httpStatus: $response->status(),
                    extra: [
                        'gemini_attempts' => $attempts,
                        ...$usageMetadata,
                        ...$preparedDescription['meta'],
                    ],
                );
            }

            $decoded = $this->extractJson($text);
            if (!is_array($decoded)) {
                return $this->metaError(
                    errorType: 'invalid_json',
                    error: 'Gemini respondió, pero el JSON no se pudo interpretar.',
                    used: true,
                    model: $model,
                    httpStatus: $response->status(),
                    rawResponse: config('app.debug') ? Str::limit($text, 2000, '') : null,
                    extra: [
                        'gemini_attempts' => $attempts,
                        ...$usageMetadata,
                        ...$preparedDescription['meta'],
                    ],
                );
            }

            return [
                'data' => $this->normalizeAnalysis($decoded, $title, $description),
                'used' => true,
                'success' => true,
                'model' => $model,
                'error' => null,
                'error_type' => null,
                'http_status' => $response->status(),
                'raw_response' => config('app.debug') ? Str::limit($text, 2000, '') : null,
                'gemini_attempts' => $attempts,
                'analyzed_at' => now()->toIso8601String(),
                ...$usageMetadata,
                ...$preparedDescription['meta'],
            ];
        } catch (\Throwable $exception) {
            $type = $this->classifyThrowable($exception);

            return $this->metaError(
                errorType: $type,
                error: $this->humanThrowableMessage($exception, $type),
                used: true,
                model: $model,
                extra: [
                    'gemini_attempts' => $attempts ?? null,
                    ...$preparedDescription['meta'],
                    ...$this->sslDiagnostics($exception, $type),
                ],
            );
        }
    }

    private function sendRequest(string $model, string $key, string $title, string $companyName, string $description)
    {
        $verify = TlsVerification::option(
            'Gemini',
            (bool) config('services.gemini.verify_ssl', true),
            config('services.gemini.ca_bundle'),
        );

        return Http::connectTimeout((int) config('services.gemini.connect_timeout', 10))
            ->timeout((int) config('services.gemini.timeout', 60))
            ->withHeaders([
                'Content-Type' => 'application/json',
                'x-goog-api-key' => $key,
            ])
            ->withOptions([
                'verify' => $verify,
                'allow_redirects' => false,
            ])
            ->post('https://generativelanguage.googleapis.com/v1beta/models/'.rawurlencode($model).':generateContent', [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $this->prompt($title, $companyName, $description)],
                        ],
                    ],
                ],
                'generationConfig' => [
                    'temperature' => 0.2,
                    'responseMimeType' => 'application/json',
                ],
            ]);
    }

    private function sslDiagnostics(\Throwable $exception, string $type): array
    {
        if (
            $type !== 'ssl_error'
            || ! (bool) config('services.gemini.debug_ssl', false)
            || ! app()->environment('local', 'testing')
        ) {
            return [];
        }

        preg_match('/cURL error\s+(\d+)/i', $exception->getMessage(), $matches);
        $exceptionType = preg_replace('/[^A-Za-z0-9_]/', '', class_basename($exception)) ?: 'Throwable';

        return [
            'gemini_ssl_diagnostics' => [
                'exception_type' => Str::limit($exceptionType, 100, ''),
                'curl_error_code' => isset($matches[1]) ? (int) $matches[1] : null,
                'verify_ssl' => (bool) config('services.gemini.verify_ssl', true),
                'ca_bundle_configured' => trim((string) config('services.gemini.ca_bundle')) !== '',
            ],
        ];
    }

    public function fallback(): array
    {
        return [
            'area_ids' => [],
            'areas' => [],
            'area_principal' => 'No especificado',
            'profesiones_sugeridas' => [],
            'area' => 'No especificado',
            'professions' => 'No especificado',
            'department' => 'No especificado',
            'location' => 'No especificado',
            'salary' => 0,
            'expiration_date' => 'No especificado',
            'municipality' => null,
        ];
    }

    private function metaError(
        string $errorType,
        string $error,
        bool $used,
        string $model,
        ?int $httpStatus = null,
        ?string $rawResponse = null,
        array $extra = [],
    ): array {
        return [
            'data' => $this->fallback(),
            'used' => $used,
            'success' => false,
            'model' => $model,
            'error' => Str::limit($error, 300, ''),
            'error_type' => $errorType,
            'http_status' => $httpStatus,
            'raw_response' => $rawResponse,
            ...$extra,
        ];
    }

    private function prepareDescription(string $description): array
    {
        $max = (int) config('services.gemini.max_description_chars', 6000);
        $max = $max > 0 ? $max : 6000;
        $length = mb_strlen($description);
        $truncated = $length > $max;
        $text = $truncated ? mb_substr($description, 0, $max) : $description;

        return [
            'text' => $text,
            'meta' => [
                'description_truncated_for_gemini' => $truncated,
                'description_original_length' => $length,
                'description_sent_length' => mb_strlen($text),
            ],
        ];
    }

    private function usageMetadata(?array $response): array
    {
        $usage = data_get($response, 'usageMetadata');

        if (!is_array($usage)) {
            return [
                'usage_metadata' => null,
                'prompt_tokens' => null,
                'candidates_tokens' => null,
                'total_tokens' => null,
                'thoughts_tokens' => null,
            ];
        }

        return [
            'usage_metadata' => $usage,
            'prompt_tokens' => data_get($usage, 'promptTokenCount'),
            'candidates_tokens' => data_get($usage, 'candidatesTokenCount'),
            'total_tokens' => data_get($usage, 'totalTokenCount'),
            'thoughts_tokens' => data_get($usage, 'thoughtsTokenCount'),
        ];
    }

    private function classifyHttpError(int $status, string $body): string
    {
        $message = $this->normalize($this->httpError($status, $body));

        if ($status === 429 || Str::contains($message, ['quota', 'exceeded', 'too many requests', 'rate limit'])) {
            return 'quota_exceeded';
        }

        return 'http_error';
    }

    private function classifyThrowable(\Throwable $exception): string
    {
        $message = $this->normalize($exception->getMessage());

        if (Str::contains($message, ['curl error 60', 'ssl certificate problem', 'ca bundle', 'tls'])) {
            return 'ssl_error';
        }

        if (Str::contains($message, ['timeout', 'timed out', 'operation timed out'])) {
            return 'timeout';
        }

        if (Str::contains($message, ['quota', 'exceeded', 'too many requests', 'rate limit'])) {
            return 'quota_exceeded';
        }

        return 'http_error';
    }

    private function humanThrowableMessage(\Throwable $exception, string $type): string
    {
        if ($type === 'ssl_error') {
            return 'Error SSL. Verifica curl.cainfo y openssl.cafile en php.ini, y ejecuta php artisan optimize:clear.';
        }

        return Str::limit($exception->getMessage(), 300, '');
    }

    private function prompt(string $title, string $company, string $description): string
    {
        $allowedAreas = Area::query()
            ->orderBy('id')
            ->get(['id', 'area_name'])
            ->map(fn(Area $area): string => json_encode([
                'id' => (int) $area->id,
                'nombre' => $area->area_name,
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
            ->implode("\n");

        return <<<PROMPT
Analiza esta convocatoria laboral.

Devuelve exclusivamente JSON valido con esta estructura:

{
  "area_ids": [],
  "ubicacion_departamento": "",
  "ubicacion": "",
  "sueldo": "",
  "fecha_expiracion": ""
}

Reglas:
- No uses markdown.
- No expliques.
- No devuelvas texto adicional.
- area_ids debe ser un array con uno o mas IDs exactos del catalogo de areas.
- No devuelvas nombres de areas ni profesiones.
- No inventes IDs.
- Si no identificas ninguna area valida con seguridad, devuelve area_ids vacio.
- No inventes areas.
- ubicacion_departamento debe ser el departamento si se puede detectar.
- Ubicacion debe ser ciudad, municipio, sucursal o lugar de trabajo.
- Si no hay sueldo, devolver exactamente: 0.
- Si hay sueldo, devuelve el monto detectado.
- Si no hay fecha de expiracion, devolver exactamente: No especificado.
- Para fecha de expiracion busca especialmente: postular hasta, fecha limite, fecha límite, recepción de postulaciones, recepcion de postulaciones, fecha de cierre, cierre, hasta el, fecha de vencimiento, vencimiento.

Catalogo de areas permitido (un objeto JSON por linea):
{$allowedAreas}

Datos:
Titulo: {$title}
Empresa: {$company}
Descripcion: {$description}
PROMPT;
    }

    public function cleanText(string $text): string
    {
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        return trim(str_replace(["\u{2013}", "\u{2014}", "\u{00A0}"], ['-', '-', ' '], $text));
    }

    public function extractJson(string $text): ?array
    {
        $text = $this->cleanText($text);
        $text = Str::of($text)->replace(['```json', '```JSON', '```'], '')->trim()->toString();
        $start = strpos($text, '{');
        $end = strrpos($text, '}');

        if ($start !== false && $end !== false && $end >= $start) {
            $text = substr($text, $start, $end - $start + 1);
        }

        $decoded = json_decode($text, true);

        return is_array($decoded) ? $decoded : null;
    }

    private function normalizeAnalysis(array $decoded, string $title, string $description): array
    {
        $areaIds = $this->normalizeAreaIds($decoded['area_ids'] ?? []);
        $areas = Area::query()
            ->whereIn('id', $areaIds)
            ->orderBy('id')
            ->get(['id', 'area_name']);
        $validAreas = $areas->pluck('area_name')->values()->all();
        $location = $this->cleanText((string) ($decoded['ubicacion'] ?? $decoded['location'] ?? ''));
        $municipality = $this->detectMunicipality($location, $title, $description);
        $department = $this->cleanText((string) ($decoded['ubicacion_departamento'] ?? $decoded['department'] ?? ''));
        $department = $department ?: ($municipality['department'] ?? $this->detectDepartment($location, $title, $description));

        if (!$location && $municipality) {
            $location = $municipality['municipality'];
        }

        return [
            'area_ids' => $areaIds,
            'areas' => $validAreas,
            'area_principal' => $validAreas[0] ?? 'No especificado',
            'profesiones_sugeridas' => [],
            'area' => $validAreas ? implode(', ', $validAreas) : 'No especificado',
            'professions' => 'No especificado',
            'department' => $department,
            'location' => $location ?: 'No especificado',
            'salary' => $this->normalizeSalaryValue($decoded['sueldo'] ?? $decoded['salary'] ?? null),
            'expiration_date' => $this->cleanText((string) ($decoded['fecha_expiracion'] ?? $decoded['expiration_date'] ?? 'No especificado')) ?: 'No especificado',
            'municipality' => $municipality,
        ];
    }

    private function normalizeAreaIds(mixed $areaIds): array
    {
        if (!is_array($areaIds)) {
            return [];
        }

        $ids = collect($areaIds)
            ->filter(fn(mixed $id): bool => is_int($id) || (is_string($id) && ctype_digit(trim($id))))
            ->map(fn(mixed $id): int => (int) $id)
            ->filter(fn(int $id): bool => $id > 0)
            ->unique()
            ->sort()
            ->values()
            ->all();

        return $ids;
    }

    private function detectMunicipality(string ...$texts): ?array
    {
        $haystack = $this->normalize(implode(' ', $texts));

        if ($haystack === '') {
            return null;
        }

        foreach (config('bolivia_municipalities', []) as $municipality => $department) {
            $needle = $this->normalize((string) $municipality);

            if ($needle !== '' && preg_match('/\b' . preg_quote($needle, '/') . '\b/', $haystack)) {
                return [
                    'municipality' => Str::headline($needle),
                    'department' => (string) $department,
                    'is_main_city' => $this->isMainCity($needle),
                ];
            }
        }

        return null;
    }

    private function detectDepartment(string ...$texts): string
    {
        $combined = $this->normalize(implode(' ', $texts));

        $departments = [
            'la paz' => 'La Paz',
            'santa cruz' => 'Santa Cruz',
            'cochabamba' => 'Cochabamba',
            'oruro' => 'Oruro',
            'potosi' => 'Potosí',
            'tarija' => 'Tarija',
            'chuquisaca' => 'Chuquisaca',
            'beni' => 'Beni',
            'pando' => 'Pando',
        ];

        foreach ($departments as $needle => $label) {
            if (preg_match('/\b' . preg_quote($needle, '/') . '\b/', $combined)) {
                return $label;
            }
        }

        return 'No especificado';
    }

    private function isMainCity(string $location): bool
    {
        return in_array($this->normalize($location), [
            'la paz',
            'santa cruz',
            'santa cruz de la sierra',
            'cochabamba',
            'oruro',
            'potosi',
            'tarija',
            'sucre',
            'trinidad',
            'cobija',
        ], true);
    }

    private function normalizeSalaryValue(string|int|float|null $salary): string|int|float
    {
        if (is_int($salary) || is_float($salary)) {
            return $salary;
        }

        $salary = $this->cleanText((string) $salary);

        if (!$salary || in_array($this->normalize($salary), ['0', 'no especificado', 'sueldo no declarado por la institucion'], true)) {
            return 0;
        }

        if (preg_match('/\b(?:bs\.?|bob)\s*([0-9][0-9\.,\s]*)\b/i', $salary, $matches)
            || preg_match('/\b([0-9][0-9\.,\s]*)\s*(?:bs\.?|bob)\b/i', $salary, $matches)) {
            $number = preg_replace('/[^\d]/', '', $matches[1]);

            return $number !== '' ? (int) $number : $salary;
        }

        if (preg_match('/^\s*[0-9][0-9\.,\s]*\s*$/', $salary)) {
            $number = preg_replace('/[^\d]/', '', $salary);

            return $number !== '' ? (int) $number : 0;
        }

        return $salary;
    }

    private function normalize(string $value): string
    {
        return Str::of(strip_tags($this->cleanText($value)))
            ->ascii()
            ->lower()
            ->replaceMatches('/[^a-z0-9]+/', ' ')
            ->squish()
            ->toString();
    }

    private function httpError(int $status, string $body): string
    {
        $message = data_get(json_decode($body, true), 'error.message');
        $message = $message ?: Str::limit(strip_tags($body), 220, '');

        return trim("HTTP {$status}" . ($message ? ": {$message}" : ''));
    }
}
