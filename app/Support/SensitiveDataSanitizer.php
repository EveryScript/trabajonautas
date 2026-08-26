<?php

namespace App\Support;

use Illuminate\Support\Str;

final class SensitiveDataSanitizer
{
    private const REDACTED = '[REDACTED]';

    private const SENSITIVE_KEYS = [
        'token',
        'tokenarchivo',
        'access_token',
        'accesstoken',
        'refresh_token',
        'refreshtoken',
        'download_token',
        'downloadtoken',
        'auth',
        'auth_token',
        'authtoken',
        'authorization',
        'proxy_authorization',
        'proxyauthorization',
        'cookie',
        'cookies',
        'set_cookie',
        'setcookie',
        'session',
        'session_id',
        'sessionid',
        'api_key',
        'apikey',
        'x_api_key',
        'xapikey',
        'x_goog_api_key',
        'xgoogapikey',
        'password',
        'secret',
        'client_secret',
        'clientsecret',
        'key',
        'signature',
        'sig',
        'credential',
        'x_amz_signature',
        'xamzsignature',
        'x_amz_credential',
        'xamzcredential',
        'x_amz_security_token',
        'xamzsecuritytoken',
        'x_goog_signature',
        'xgoogsignature',
        'x_goog_credential',
        'xgoogcredential',
        'post_data',
        'postdata',
        'post_data_preview',
        'postdatapreview',
        'request_body',
        'requestbody',
        'response_body',
        'responsebody',
        'raw_response',
        'rawresponse',
        'document_text',
        'documenttext',
        'document_content',
        'documentcontent',
    ];

    public static function text(mixed $value, int $maxLength = 1000): ?string
    {
        if ($value === null) {
            return null;
        }

        if (is_array($value) || is_object($value)) {
            $value = json_encode($value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?: '';
        }

        $text = (string) $value;
        $text = self::redactUrls($text);
        $keys = self::keyPattern();

        $text = preg_replace('/\b(Bearer|Basic)\s+[A-Za-z0-9+\/_.=~-]+/i', '$1 '.self::REDACTED, $text) ?? $text;
        $text = preg_replace(
            '/("(?:'.$keys.')"\\s*:\\s*)"(?:\\\\.|[^"\\\\])*"/i',
            '$1"'.self::REDACTED.'"',
            $text,
        ) ?? $text;
        $text = preg_replace(
            "/('(?:".$keys.")'\\s*:\\s*)'(?:\\\\.|[^'\\\\])*'/i",
            '$1\''.self::REDACTED.'\'',
            $text,
        ) ?? $text;
        $text = preg_replace(
            '/([?&#;](?:'.$keys.')=)[^&#;\s"\']*/i',
            '$1'.self::REDACTED,
            $text,
        ) ?? $text;
        $text = preg_replace(
            '/(^|[\s;&])((?:'.$keys.')=)[^&;\s]*/im',
            '$1$2'.self::REDACTED,
            $text,
        ) ?? $text;
        $text = preg_replace(
            '/(\b(?:'.$keys.')\b\s*[:=]\s*)(?!\[REDACTED\])[^,\s;}\]]+/i',
            '$1'.self::REDACTED,
            $text,
        ) ?? $text;
        $text = preg_replace(
            '/(\b(?:Authorization|Proxy-Authorization|Cookie|Set-Cookie|X-API-Key|X-Goog-Api-Key)\b\s*:\s*)[^\r\n]+/i',
            '$1'.self::REDACTED,
            $text,
        ) ?? $text;
        $text = preg_replace(
            '/(descargarArchivo\(\s*[\'"])[^\'"]+([\'"]\s*\))/i',
            '$1'.self::REDACTED.'$2',
            $text,
        ) ?? $text;
        $text = preg_replace(
            '/\bC:\\\\Users\\\\[^\\\\\s]+(?:\\\\[^\r\n\t]*)?/i',
            '[LOCAL_PATH]',
            $text,
        ) ?? $text;
        $text = preg_replace(
            '/\bD:\\\\[^\r\n\t]*/i',
            '[LOCAL_PATH]',
            $text,
        ) ?? $text;

        return Str::limit($text, max(0, $maxLength), '');
    }

    public static function url(mixed $value, int $maxLength = 1000): ?string
    {
        if ($value === null) {
            return null;
        }

        $raw = trim((string) $value);
        $parts = parse_url($raw);

        if (! is_array($parts) || ! isset($parts['scheme'], $parts['host'])) {
            $withoutScheme = preg_replace('/^https?:\/\//i', '[INVALID_URL]/', $raw) ?? $raw;

            return self::text($withoutScheme, $maxLength);
        }

        $scheme = strtolower((string) $parts['scheme']);

        if (! in_array($scheme, ['http', 'https'], true)) {
            return '[UNSAFE_URL_SCHEME]';
        }

        $host = strtolower((string) $parts['host']);
        $port = isset($parts['port']) ? ':'.(int) $parts['port'] : '';
        $path = $parts['path'] ?? '';
        $query = self::redactQuery((string) ($parts['query'] ?? ''));
        $url = "{$scheme}://{$host}{$port}{$path}";

        if ($query !== '') {
            $url .= '?'.$query;
        }

        return Str::limit($url, max(0, $maxLength), '');
    }

    public static function context(
        array $context,
        int $maxStringLength = 500,
        int $maxDepth = 4,
        int $maxItems = 50,
    ): array {
        return self::sanitizeArray($context, $maxStringLength, $maxDepth, $maxItems, 0);
    }

    public static function exception(\Throwable $exception, int $maxLength = 500): array
    {
        $class = get_class($exception);
        $separator = strrpos($class, '\\');
        $type = $separator === false ? $class : substr($class, $separator + 1);
        $isDatabaseException = $exception instanceof \PDOException
            || str_ends_with($class, '\\QueryException');

        return [
            'type' => $type,
            'code' => is_int($exception->getCode()) || is_string($exception->getCode())
                ? Str::limit((string) $exception->getCode(), 32, '')
                : '',
            'message' => $isDatabaseException
                ? 'Error de base de datos; detalle omitido por seguridad.'
                : self::text($exception->getMessage(), $maxLength),
        ];
    }

    public static function payloadMetadata(?string $payload): array
    {
        $payload ??= '';

        return [
            'bytes' => strlen($payload),
            'sha256' => hash('sha256', $payload),
        ];
    }

    public static function basename(mixed $value, int $maxLength = 255): ?string
    {
        if ($value === null || trim((string) $value) === '') {
            return null;
        }

        $normalized = str_replace('\\', '/', (string) $value);

        return self::text(basename($normalized), $maxLength);
    }

    private static function sanitizeArray(
        array $values,
        int $maxStringLength,
        int $maxDepth,
        int $maxItems,
        int $depth,
    ): array {
        if ($depth >= $maxDepth) {
            return ['_truncated' => true];
        }

        $sanitized = [];
        $items = array_slice($values, 0, max(0, $maxItems), true);

        foreach ($items as $key => $value) {
            if (self::isSensitiveKey((string) $key)) {
                $sanitized[$key] = self::REDACTED;

                continue;
            }

            if (is_array($value)) {
                $sanitized[$key] = self::sanitizeArray(
                    $value,
                    $maxStringLength,
                    $maxDepth,
                    $maxItems,
                    $depth + 1,
                );

                continue;
            }

            if (is_object($value)) {
                $sanitized[$key] = self::sanitizeArray(
                    get_object_vars($value),
                    $maxStringLength,
                    $maxDepth,
                    $maxItems,
                    $depth + 1,
                );

                continue;
            }

            $sanitized[$key] = is_string($value)
                ? (self::looksLikeUrlKey((string) $key)
                    ? self::url($value, $maxStringLength)
                    : self::text($value, $maxStringLength))
                : $value;
        }

        if (count($values) > count($items)) {
            $sanitized['_truncated_items'] = count($values) - count($items);
        }

        return $sanitized;
    }

    private static function redactUrls(string $text): string
    {
        return preg_replace_callback(
            '~https?://[^\s<>"\']+~iu',
            static function (array $matches): string {
                $url = rtrim($matches[0], '.,);]');
                $suffix = substr($matches[0], strlen($url));

                return (self::url($url, 4000) ?? '').$suffix;
            },
            $text,
        ) ?? $text;
    }

    private static function redactQuery(string $query): string
    {
        if ($query === '') {
            return '';
        }

        return implode('&', array_map(static function (string $part): string {
            [$rawKey, $rawValue] = array_pad(explode('=', $part, 2), 2, '');
            $key = rawurldecode($rawKey);

            if (self::isSensitiveKey($key)) {
                return $rawKey.'='.rawurlencode(self::REDACTED);
            }

            $value = self::text(rawurldecode($rawValue), 1000) ?? '';

            return $rawKey.($rawValue !== '' ? '='.rawurlencode($value) : '');
        }, explode('&', $query)));
    }

    private static function isSensitiveKey(string $key): bool
    {
        $normalized = strtolower((string) preg_replace('/[^A-Za-z0-9]+/', '_', $key));
        $collapsed = str_replace('_', '', $normalized);

        return in_array($normalized, self::SENSITIVE_KEYS, true)
            || in_array($collapsed, self::SENSITIVE_KEYS, true);
    }

    private static function looksLikeUrlKey(string $key): bool
    {
        return preg_match('/(?:^|_)(?:url|uri|link)(?:$|_)/i', $key) === 1;
    }

    private static function keyPattern(): string
    {
        return '(?:access[_-]?token|refresh[_-]?token|download[_-]?token|auth(?:[_-]?token)?|token(?:archivo)?|authorization|proxy[_-]?authorization|api[_-]?key|apikey|x[_-]api[_-]key|x[_-]goog[_-]api[_-]key|key|signature|sig|credential|x[_-](?:amz[_-](?:signature|credential|security[_-]?token)|goog[_-](?:signature|credential))|cookie|set[_-]?cookie|session(?:[_-]?id)?|password|secret|client[_-]?secret|post[_-]?data(?:[_-]?preview)?|request[_-]?body|response[_-]?body|raw[_-]?response|document[_-]?(?:text|content))';
    }
}
