<?php

namespace App\Services\Bot;

use App\Models\Area;
use App\Support\SensitiveDataSanitizer;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class SicoesDocumentAiAnalyzer
{
    public function analyze(array $document, string $text): array
    {
        $provider = config('sicoes.ai.provider', 'anthropic');
        $key = config('services.anthropic.api_key');
        $model = config('services.anthropic.model', 'claude-haiku-4-5-20251001');
        $version = config('services.anthropic.version', '2023-06-01');
        $maxTokens = (int) config('services.anthropic.max_tokens', 4000);
        $prepared = $this->prepareText($text);

        if ($provider !== 'anthropic') {
            return $this->error('unsupported_provider', 'SICOES_AI_PROVIDER debe ser anthropic para SICOES.', false, $model, [
                'provider' => $provider,
                ...$prepared['meta'],
            ]);
        }

        if (! $key) {
            return $this->error('missing_api_key', 'ANTHROPIC_API_KEY no configurada.', false, $model, [
                'provider' => $provider,
                ...$prepared['meta'],
            ]);
        }

        $attempts = (int) config('sicoes.ai.retries', 2);
        $last = null;

        for ($attempt = 1; $attempt <= $attempts; $attempt++) {
            try {
                $response = Http::timeout((int) config('sicoes.ai.timeout', 120))
                    ->withHeaders([
                        'content-type' => 'application/json',
                        'x-api-key' => $key,
                        'anthropic-version' => $version,
                    ])
                    ->post('https://api.anthropic.com/v1/messages', [
                        'model' => $model,
                        'max_tokens' => $maxTokens > 0 ? $maxTokens : 4000,
                        'temperature' => 0,
                        'system' => $this->systemPrompt(),
                        'messages' => [[
                            'role' => 'user',
                            'content' => [[
                                'type' => 'text',
                                'text' => $this->userPrompt($document, $prepared['text']),
                            ]],
                        ]],
                    ]);

                $body = $response->json();
                $usage = $this->usageMetadata(is_array($body) ? $body : []);

                if (! $response->successful()) {
                    $last = $this->error(
                        $this->classifyHttpError($response->status(), $response->body()),
                        $this->httpError($response->status(), $response->body()),
                        true,
                        $model,
                        [
                            'provider' => $provider,
                            'http_status' => $response->status(),
                            'anthropic_response_metadata' => SensitiveDataSanitizer::payloadMetadata($response->body()),
                            'ai_attempts' => $attempt,
                            'anthropic_attempts' => $attempt,
                            ...$usage,
                            ...$prepared['meta'],
                        ],
                    );

                    if ($attempt < $attempts && $this->shouldRetryHttp($response->status())) {
                        $this->backoff($attempt);

                        continue;
                    }

                    return $last;
                }

                $stopReason = (string) data_get($body, 'stop_reason', '');
                $rawText = $this->responseText(is_array($body) ? $body : []);

                if ($stopReason === 'refusal') {
                    return $this->error(
                        'ai_refusal',
                        'Claude rechazo la solicitud de analisis del documento SICOES.',
                        true,
                        $model,
                        [
                            'provider' => $provider,
                            'http_status' => $response->status(),
                            'anthropic_response_metadata' => SensitiveDataSanitizer::payloadMetadata($rawText),
                            'ai_attempts' => $attempt,
                            'anthropic_attempts' => $attempt,
                            'anthropic_stop_reason' => $stopReason,
                            ...$usage,
                            ...$prepared['meta'],
                        ],
                    );
                }

                $decoded = $this->extractJson($rawText);

                if (! is_array($decoded) || ! $this->hasRequiredShape($decoded)) {
                    $last = $this->error(
                        'invalid_json',
                        'Claude respondio, pero el JSON SICOES no tiene la estructura requerida.',
                        true,
                        $model,
                        [
                            'provider' => $provider,
                            'http_status' => $response->status(),
                            'anthropic_response_metadata' => SensitiveDataSanitizer::payloadMetadata($rawText),
                            'ai_attempts' => $attempt,
                            'anthropic_attempts' => $attempt,
                            'anthropic_stop_reason' => $stopReason,
                            ...$usage,
                            ...$prepared['meta'],
                        ],
                    );

                    if ($attempt < $attempts) {
                        $this->backoff($attempt);

                        continue;
                    }

                    return $last;
                }

                return [
                    'success' => true,
                    'used' => true,
                    'provider' => $provider,
                    'model' => $model,
                    'http_status' => $response->status(),
                    'error' => null,
                    'error_type' => null,
                    'data' => $this->normalizeResponse($decoded),
                    'anthropic_response_metadata' => SensitiveDataSanitizer::payloadMetadata($rawText),
                    'ai_attempts' => $attempt,
                    'anthropic_attempts' => $attempt,
                    'anthropic_stop_reason' => $stopReason,
                    'analyzed_at' => now()->toIso8601String(),
                    ...$usage,
                    ...$prepared['meta'],
                ];
            } catch (\Throwable $exception) {
                $last = $this->error(
                    $this->classifyThrowable($exception),
                    $this->humanThrowableMessage($exception),
                    true,
                    $model,
                    [
                        'provider' => $provider,
                        'ai_attempts' => $attempt,
                        'anthropic_attempts' => $attempt,
                        ...$prepared['meta'],
                    ],
                );

                if ($attempt < $attempts && ! in_array($last['error_type'] ?? null, ['ssl_error', 'api_key_error'], true)) {
                    $this->backoff($attempt);

                    continue;
                }

                return $last;
            }
        }

        return $last ?: $this->error('unknown', 'No se pudo analizar el documento SICOES con Claude.', true, $model, [
            'provider' => $provider,
            ...$prepared['meta'],
        ]);
    }

    private function systemPrompt(): string
    {
        return <<<'PROMPT'
Eres un extractor estricto para documentos SICOES de Bolivia.

Objetivo principal:
- Identificar si el documento es una oportunidad laboral/consultoria para una persona natural consultora.
- Extraer una ficha estructurada solo si corresponde a consultor individual, consultor de linea, consultor por producto o consultoria individual.

Reglas criticas:
- Analiza UN SOLO documento. No mezcles informacion de otras convocatorias.
- Prioriza Terminos de Referencia, TDR, modalidad de contratacion, objeto, metodo de seleccion, perfil requerido, formacion academica, experiencia, funciones, requisitos legales, lugar, plazo y forma de pago.
- No inventes datos. Si falta informacion, usa "No especificado".
- Devuelve solo JSON valido. No markdown, no explicaciones fuera del JSON.
- Prioriza precision sobre cantidad.
- Para clasificacion profesional devuelve exclusivamente area_ids del catalogo proporcionado. No propongas profesiones.
- Si no hay evidencia suficiente para confirmar que es consultoria individual/persona natural, usa tipo_oportunidad "no_determinado" y debe_descartarse true.
- Si el documento parece ser para empresa, persona juridica, firma consultora, bienes, obras, productos, equipamiento, materiales, medicamentos, alimentos, maquinaria, vehiculos, seguros, auditorias empresariales u otros procesos no laborales, debe_descartarse true.
- Si hay senales fuertes de compra de bienes/servicios/obra, descarta aunque aparezca la palabra "consultoria" en texto general.
- Si se exige NIT empresarial, matricula de comercio, experiencia institucional, propuesta empresarial, equipo empresarial, persona juridica o sociedad comercial, descarta.
- Si pide profesionales individuales, persona natural, consultor individual de linea, consultor individual por producto o consultoria individual, acepta.
- Si el objeto menciona una carretera, construccion u obra, no descartes por esa palabra aislada: decide segun la modalidad legal del proponente. Un equipo de consultores individuales que trabajara en un proyecto de obra sigue siendo elegible.
- Si existen varios items o cargos de consultores individuales, acepta y usa contract_type "multiple_individual".
- Si marcas aceptado pero tipo_oportunidad es empresa_consultora, bienes_servicios, obra, otro o no_determinado, corrige: debe_descartarse true.
- Si marcas consultoria individual y hay evidencia de empresa/persona juridica, agrega advertencia.
PROMPT;
    }

    private function userPrompt(array $document, string $text): string
    {
        $areas = Area::query()
            ->orderBy('id')
            ->get(['id', 'area_name'])
            ->map(fn (Area $area): string => json_encode([
                'id' => (int) $area->id,
                'nombre' => $area->area_name,
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
            ->implode("\n");

        $cuce = $document['cuce'] ?? 'No especificado';
        $title = $document['title'] ?? 'No especificado';
        $entity = $document['entity'] ?? 'No especificado';
        $filename = $document['filename'] ?? 'No especificado';
        $publishedAt = $document['published_at'] ?? 'No especificado';
        $preclassification = $this->preclassificationContext($document);

        return <<<PROMPT
Devuelve exclusivamente JSON valido con esta estructura exacta:

{
  "eligible": false,
  "contract_type": "individual | multiple_individual | individual_product | rejected_company | rejected_goods | rejected_works | rejected_service | other_rejected",
  "es_oportunidad_consultor_persona": false,
  "tipo_oportunidad": "consultor_linea | consultor_producto | consultoria_individual | empresa_consultora | bienes_servicios | obra | otro | no_determinado",
  "debe_descartarse": true,
  "motivo_descarte": null,
  "evidencia_clasificacion": "",
  "cuce": "",
  "titulo_objeto": "",
  "area_ids": [],
  "ubicacion_detectada": {
    "texto": "",
    "municipio": "",
    "departamento": "",
    "confianza": "alta|media|baja|no_especificado"
  },
  "lugar_trabajo": "",
  "duracion_contrato": "",
  "modalidad_postulacion": "",
  "sueldo": {
    "valor": 0,
    "detalle": "",
    "sueldos": [
      {
        "cargo": "",
        "monto": "",
        "periodicidad": "",
        "evidencia": ""
      }
    ],
    "revision_manual": false
  },
  "detalle_sueldos": "",
  "descripcion": "",
  "evidencias": {
    "cuce": "",
    "area_profesional": "",
    "lugar_trabajo": "",
    "duracion_contrato": "",
    "modalidad_postulacion": "",
    "sueldo": ""
  },
  "advertencias": []
}

Clasificacion obligatoria:
- Acepta solo oportunidades para personas consultoras: consultor individual de linea, consultor individual por producto, consultoria individual, consultores en linea, consultor/a por producto o servicios de consultoria individual con perfil profesional/persona natural.
- Descarta empresa consultora, firma consultora, empresa especializada, servicios generales de una empresa, proveedor empresarial, persona juridica, sociedad comercial, consultoria por empresa, bienes, obras, compras, productos, materiales, medicamentos, alimentos, maquinaria, vehiculos, seguros y auditorias empresariales.
- Si debe_descartarse es true, llena motivo_descarte y evidencia_clasificacion.
- eligible debe ser exactamente el inverso de debe_descartarse.
- Usa contract_type "multiple_individual" cuando el documento agrupe varios cargos o items de personas consultoras.
- Las referencias a la empresa constructora, al proyecto, a la carretera o a la obra donde trabajara el consultor son contexto y no convierten por si solas al proponente en empresa.

Reglas de datos:
- CUCE: usa preferentemente el CUCE de la fila SICOES. Si el documento muestra otro CUCE, devuelvelo y explica la diferencia en advertencias.
- Ubicacion: detecta lugar de trabajo real. Si solo hay departamento, deja municipio vacio y departamento detectado. Si no hay certeza, usa confianza "no_especificado".
- Sueldo.valor debe ser el monto entero real si hay un solo sueldo/honorario mensual claro. Ejemplo: 3045.
- Sueldo.valor debe ser 1 si hay varios cargos o items salariales distintos, aunque sus montos sean iguales. Detalla todos los cargos y montos en sueldo.sueldos y detalle_sueldos.
- Si hay varias vacantes del mismo cargo con un unico sueldo comun, devuelve una sola fila salarial para ese cargo y usa el monto entero real.
- Sueldo.valor debe ser 0 si no hay ningun monto de remuneracion o presupuesto identificable.
- Si solo hay presupuesto total/referencial sin pago mensual, indica eso en detalle_sueldos y marca sueldo.revision_manual true.
- Si hay pago por cuotas mensuales, honorarios mensuales, monto mensual o pago mensual, tomalo como sueldo mensual.
- detalle_sueldos debe incluir cargos y montos cuando existan varios.
- Devuelve evidencias textuales cortas para sueldo, duracion, modalidad, lugar, clasificacion y area.
- area_ids debe contener solo IDs exactos del catalogo. No devuelvas nombres, IDs inexistentes ni profesiones.
- Si ninguna area del catalogo corresponde con seguridad, devuelve area_ids vacio. Laravel marcara el documento para revision.

Catalogo de areas del sistema (un objeto JSON por linea):
{$areas}

Contexto de la fila SICOES:
CUCE fila: {$cuce}
Titulo u objeto: {$title}
Entidad: {$entity}
Archivo: {$filename}
Fecha publicacion: {$publishedAt}
Preclasificacion local basada en senales explicitas: {$preclassification}

Texto completo del documento:
{$text}
PROMPT;
    }

    private function prepareText(string $text): array
    {
        $max = (int) config('sicoes.ai.max_text_chars', 250000);
        $max = $max > 0 ? $max : 250000;
        $text = trim($text);
        $length = mb_strlen($text);
        $truncated = $length > $max;

        return [
            'text' => $truncated ? mb_substr($text, 0, $max) : $text,
            'meta' => [
                'document_text_original_length' => $length,
                'document_text_sent_length' => $truncated ? $max : $length,
                'document_text_truncated_for_ai' => $truncated,
            ],
        ];
    }

    private function normalizeResponse(array $data): array
    {
        $salary = is_array($data['sueldo'] ?? null) ? $data['sueldo'] : [];
        $location = is_array($data['ubicacion_detectada'] ?? null) ? $data['ubicacion_detectada'] : [];
        $evidence = is_array($data['evidencias'] ?? null) ? $data['evidencias'] : [];
        $type = $this->tipoOportunidad($data['tipo_oportunidad'] ?? 'no_determinado');
        $mustDiscard = (bool) ($data['debe_descartarse'] ?? true);

        if (in_array($type, ['empresa_consultora', 'bienes_servicios', 'obra', 'otro', 'no_determinado'], true)) {
            $mustDiscard = true;
        }

        $normalizedSalary = $this->normalizeSalary($salary);
        $eligible = ! $mustDiscard && (bool) ($data['es_oportunidad_consultor_persona'] ?? $data['eligible'] ?? false);

        return [
            'eligible' => $eligible,
            'contract_type' => $this->contractType($data['contract_type'] ?? null, $type, $mustDiscard),
            'es_oportunidad_consultor_persona' => $eligible,
            'tipo_oportunidad' => $type,
            'debe_descartarse' => $mustDiscard,
            'motivo_descarte' => $this->nullableText($data['motivo_descarte'] ?? null),
            'evidencia_clasificacion' => $this->text($data['evidencia_clasificacion'] ?? ''),
            'cuce' => $this->text($data['cuce'] ?? ''),
            'titulo_objeto' => $this->text($data['titulo_objeto'] ?? ''),
            'area_ids' => $this->integerList($data['area_ids'] ?? []),
            'ubicacion_detectada' => [
                'texto' => $this->text($location['texto'] ?? ''),
                'municipio' => $this->text($location['municipio'] ?? ''),
                'departamento' => $this->text($location['departamento'] ?? ''),
                'confianza' => $this->text($location['confianza'] ?? 'no_especificado') ?: 'no_especificado',
            ],
            'lugar_trabajo' => $this->text($data['lugar_trabajo'] ?? ''),
            'duracion_contrato' => $this->text($data['duracion_contrato'] ?? ''),
            'modalidad_postulacion' => $this->text($data['modalidad_postulacion'] ?? ''),
            'sueldo' => $normalizedSalary,
            'detalle_sueldos' => $this->text($data['detalle_sueldos'] ?? ''),
            'descripcion' => $this->text($data['descripcion'] ?? ''),
            'evidencias' => [
                'cuce' => $this->text($evidence['cuce'] ?? ''),
                'area_profesional' => $this->text($evidence['area_profesional'] ?? ''),
                'lugar_trabajo' => $this->text($evidence['lugar_trabajo'] ?? ''),
                'duracion_contrato' => $this->text($evidence['duracion_contrato'] ?? ''),
                'modalidad_postulacion' => $this->text($evidence['modalidad_postulacion'] ?? ''),
                'sueldo' => $this->text($evidence['sueldo'] ?? ''),
            ],
            'advertencias' => $this->stringList($data['advertencias'] ?? []),
        ];
    }

    private function hasRequiredShape(array $data): bool
    {
        foreach ([
            'eligible',
            'contract_type',
            'es_oportunidad_consultor_persona',
            'tipo_oportunidad',
            'debe_descartarse',
            'motivo_descarte',
            'evidencia_clasificacion',
            'cuce',
            'titulo_objeto',
            'area_ids',
            'ubicacion_detectada',
            'sueldo',
            'descripcion',
            'evidencias',
        ] as $key) {
            if (! array_key_exists($key, $data)) {
                return false;
            }
        }

        return is_bool($data['eligible'])
            && is_string($data['contract_type'])
            && is_bool($data['es_oportunidad_consultor_persona'])
            && is_array($data['area_ids'])
            && is_array($data['ubicacion_detectada'])
            && is_array($data['sueldo'])
            && is_array($data['evidencias']);
    }

    private function extractJson(string $text): ?array
    {
        $text = $this->cleanText($text);
        $text = Str::of($text)
            ->replace(['```json', '```JSON', '```Json', '```'], '')
            ->trim()
            ->toString();
        $start = strpos($text, '{');
        $end = strrpos($text, '}');

        if ($start !== false && $end !== false && $end >= $start) {
            $text = substr($text, $start, $end - $start + 1);
        }

        $decoded = json_decode($text, true);

        return is_array($decoded) ? $decoded : null;
    }

    private function responseText(array $response): string
    {
        return collect($response['content'] ?? [])
            ->filter(fn ($part): bool => is_array($part) && ($part['type'] ?? null) === 'text')
            ->map(fn (array $part): string => (string) ($part['text'] ?? ''))
            ->implode("\n");
    }

    private function usageMetadata(array $response): array
    {
        $usage = data_get($response, 'usage');
        $inputTokens = data_get($usage, 'input_tokens');
        $outputTokens = data_get($usage, 'output_tokens');

        return [
            'ai_usage_metadata' => is_array($usage) ? $usage : null,
            'ai_prompt_tokens' => $inputTokens,
            'ai_output_tokens' => $outputTokens,
            'ai_total_tokens' => is_numeric($inputTokens) && is_numeric($outputTokens) ? $inputTokens + $outputTokens : null,
            'anthropic_usage_metadata' => is_array($usage) ? $usage : null,
            'anthropic_input_tokens' => $inputTokens,
            'anthropic_output_tokens' => $outputTokens,
            'anthropic_total_tokens' => is_numeric($inputTokens) && is_numeric($outputTokens) ? $inputTokens + $outputTokens : null,
        ];
    }

    private function error(string $type, string $message, bool $used, string $model, array $extra = []): array
    {
        unset($extra['raw_response'], $extra['anthropic_raw_response']);
        $extra = SensitiveDataSanitizer::context($extra, 500, 4, 50);

        return [
            'success' => false,
            'used' => $used,
            'provider' => $extra['provider'] ?? 'anthropic',
            'model' => $model,
            'http_status' => $extra['http_status'] ?? null,
            'error' => SensitiveDataSanitizer::text($message, 500),
            'error_type' => $type,
            'data' => null,
            ...$extra,
        ];
    }

    private function classifyHttpError(int $status, string $body): string
    {
        if (in_array($status, [401, 403], true)) {
            return 'api_key_error';
        }

        if ($status === 429) {
            return 'rate_limited';
        }

        if ($status >= 500) {
            return 'server_error';
        }

        $message = $this->normalize($this->httpError($status, $body));

        if (Str::contains($message, ['quota', 'exceeded', 'too many requests', 'rate limit'])) {
            return 'rate_limited';
        }

        return 'http_error';
    }

    private function classifyThrowable(\Throwable $exception): string
    {
        $message = $this->normalize($exception->getMessage());

        if (Str::contains($message, ['curl error 60', 'ssl certificate problem'])) {
            return 'ssl_error';
        }

        if (Str::contains($message, ['timeout', 'timed out', 'operation timed out'])) {
            return 'timeout';
        }

        if (Str::contains($message, ['unauthorized', 'forbidden', 'api key'])) {
            return 'api_key_error';
        }

        if (Str::contains($message, ['quota', 'exceeded', 'too many requests', 'rate limit'])) {
            return 'rate_limited';
        }

        return 'http_error';
    }

    private function humanThrowableMessage(\Throwable $exception): string
    {
        return match ($this->classifyThrowable($exception)) {
            'ssl_error' => 'Error SSL al conectar con Anthropic. Verifica curl.cainfo, openssl.cafile o el certificado del entorno.',
            'api_key_error' => 'Error de configuracion Anthropic. Verifica ANTHROPIC_API_KEY.',
            default => SensitiveDataSanitizer::text($exception->getMessage(), 500)
                ?: 'Error al conectar con Anthropic.',
        };
    }

    private function httpError(int $status, string $body): string
    {
        $decoded = json_decode($body, true);
        $message = data_get($decoded, 'error.message') ?: data_get($decoded, 'message');

        return 'HTTP '.$status.($message ? ': '.SensitiveDataSanitizer::text($message, 300) : '');
    }

    private function shouldRetryHttp(int $status): bool
    {
        return $status === 429 || $status >= 500;
    }

    private function backoff(int $attempt): void
    {
        usleep(1_000_000 * $attempt);
    }

    private function tipoOportunidad(mixed $value): string
    {
        $value = $this->normalize($this->text($value));
        $allowed = [
            'consultor_linea',
            'consultor_producto',
            'consultoria_individual',
            'empresa_consultora',
            'bienes_servicios',
            'obra',
            'otro',
            'no_determinado',
        ];

        return in_array($value, $allowed, true) ? $value : 'no_determinado';
    }

    private function preclassificationContext(array $document): string
    {
        $classification = $document['preclassification'] ?? null;

        if (! is_array($classification)) {
            return 'No disponible; clasifica usando el documento completo.';
        }

        return json_encode([
            'decision' => $classification['decision'] ?? null,
            'contract_type' => $classification['contract_type'] ?? null,
            'evidence' => $classification['evidence'] ?? null,
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: 'No disponible.';
    }

    private function normalizeSalary(array $salary): array
    {
        $rows = is_array($salary['sueldos'] ?? null) ? array_values($salary['sueldos']) : [];
        $salaryItems = collect($rows)
            ->filter(fn (mixed $row): bool => is_array($row))
            ->map(fn (array $row): array => [
                'role' => $this->normalize($this->text($row['cargo'] ?? '')),
                'amount' => $this->integerAmount($row['monto'] ?? null),
            ])
            ->filter(fn (array $item): bool => $item['amount'] > 0)
            ->values();
        $amounts = $salaryItems
            ->pluck('amount')
            ->unique()
            ->values();
        $roles = $salaryItems
            ->pluck('role')
            ->filter()
            ->unique()
            ->values();
        $multipleDistinctItems = $roles->count() > 1
            || ($salaryItems->count() > 1 && $roles->isEmpty());

        if ($multipleDistinctItems || $amounts->count() > 1) {
            $value = 1;
        } elseif ($amounts->count() === 1) {
            $value = (int) $amounts->first();
        } else {
            $value = $this->integerAmount($salary['valor'] ?? 0);
            if ($value === 2) {
                $value = 1;
            }
        }

        return [
            'valor' => max(0, $value),
            'detalle' => $this->text($salary['detalle'] ?? ''),
            'sueldos' => $rows,
            'revision_manual' => (bool) ($salary['revision_manual'] ?? false),
        ];
    }

    private function integerAmount(mixed $value): int
    {
        if (is_int($value) || is_float($value)) {
            return max(0, (int) $value);
        }

        $value = trim((string) $value);
        if ($value === '') {
            return 0;
        }

        if (preg_match('/\d[\d.,\s]*/', $value, $matches) !== 1) {
            return 0;
        }

        $number = preg_replace('/\s+/', '', $matches[0]) ?: '';
        $number = preg_replace('/[.,]\d{2}$/', '', $number) ?: $number;
        $digits = preg_replace('/\D/', '', $number) ?: '';

        return $digits !== '' ? (int) $digits : 0;
    }

    private function contractType(mixed $value, string $opportunityType, bool $mustDiscard): string
    {
        $normalized = $this->normalize($this->text($value));
        $allowed = [
            'individual',
            'multiple_individual',
            'individual_product',
            'rejected_company',
            'rejected_goods',
            'rejected_works',
            'rejected_service',
            'other_rejected',
        ];

        if (in_array($normalized, $allowed, true)) {
            return $normalized;
        }

        if ($mustDiscard) {
            return match ($opportunityType) {
                'empresa_consultora' => 'rejected_company',
                'bienes_servicios' => 'rejected_goods',
                'obra' => 'rejected_works',
                default => 'other_rejected',
            };
        }

        return $opportunityType === 'consultor_producto' ? 'individual_product' : 'individual';
    }

    private function nullableText(mixed $value): ?string
    {
        $text = $this->text($value);

        return $text === '' || $this->normalize($text) === 'null' ? null : $text;
    }

    private function stringList(mixed $value): array
    {
        return collect(is_array($value) ? $value : explode(',', (string) $value))
            ->map(fn ($item): string => $this->text($item))
            ->filter(fn (string $item): bool => $item !== '')
            ->unique(fn (string $item): string => $this->normalize($item))
            ->values()
            ->all();
    }

    private function integerList(mixed $value): array
    {
        if (! is_array($value)) {
            return [];
        }

        return collect($value)
            ->filter(fn (mixed $id): bool => is_int($id) || (is_string($id) && ctype_digit(trim($id))))
            ->map(fn (mixed $id): int => (int) $id)
            ->filter(fn (int $id): bool => $id > 0)
            ->unique()
            ->values()
            ->all();
    }

    private function text(mixed $value): string
    {
        if (is_array($value) || is_object($value)) {
            $value = json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }

        return trim($this->cleanText((string) $value));
    }

    private function cleanText(string $text): string
    {
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        return trim(str_replace(["\u{2013}", "\u{2014}", "\u{00A0}"], ['-', '-', ' '], $text));
    }

    private function normalize(string $value): string
    {
        return Str::of($value)
            ->ascii()
            ->lower()
            ->replaceMatches('/[^a-z0-9_]+/', '_')
            ->trim('_')
            ->toString();
    }
}
