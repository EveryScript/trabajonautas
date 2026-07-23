<?php

$cdpPort = min(65535, max(1, (int) env('SICOES_CDP_PORT', 9222)));

return [
    'refresh_downloads' => filter_var(
        env('SICOES_REFRESH_DOWNLOADS', true),
        FILTER_VALIDATE_BOOLEAN,
    ),

    'assisted_download' => filter_var(
        env('SICOES_ASSISTED_DOWNLOAD', true),
        FILTER_VALIDATE_BOOLEAN,
    ),

    'process' => [
        'timeout' => max(60, (int) env('SICOES_PROCESS_TIMEOUT', 7200)),
        'idle_timeout' => max(30, (int) env('SICOES_PROCESS_IDLE_TIMEOUT', 240)),
    ],

    'manual_download' => [
        'timeout_ms' => max(1000, (int) env('SICOES_MANUAL_DOWNLOAD_TIMEOUT_MS', 600000)),
        'directory' => env('SICOES_MANUAL_DOWNLOAD_DIR') ?: null,
    ],

    'node' => [
        'path' => env('SICOES_NODE_PATH') ?: null,
    ],

    'browser' => [
        'path' => env('SICOES_BROWSER_PATH') ?: null,
        'cdp_port' => $cdpPort,
        'cdp_url' => env('SICOES_CDP_URL') ?: "http://127.0.0.1:{$cdpPort}",
    ],

    'ai' => [
        'provider' => strtolower((string) env('SICOES_AI_PROVIDER', 'anthropic')),
        'retries' => min(5, max(1, (int) env('SICOES_AI_RETRIES', 2))),
        'timeout' => min(300, max(5, (int) env('SICOES_AI_TIMEOUT', 120))),
        'max_text_chars' => min(500000, max(1000, (int) env('SICOES_AI_MAX_TEXT_CHARS', 250000))),
        'use_gemini_validation' => filter_var(
            env('SICOES_USE_GEMINI_VALIDATION', false),
            FILTER_VALIDATE_BOOLEAN,
        ),
    ],
];
