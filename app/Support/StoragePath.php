<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class StoragePath
{
    private const PUBLIC_MARKER = 'storage/app/public/';

    private const PUBLIC_PREFIXES = [
        'public/storage/',
        'storage/app/public/',
        'app/public/',
        'public/',
        'storage/',
    ];

    public static function normalizePublicPath(?string $path): ?string
    {
        $path = trim((string) $path);

        if ($path === '' || self::containsControlCharacters($path)) {
            return null;
        }

        $path = str_replace('\\', '/', $path);

        if (
            Str::startsWith($path, '//')
            || preg_match('#^[a-z][a-z0-9+.-]*://#i', $path)
            || str_contains($path, '?')
            || str_contains($path, '#')
        ) {
            return null;
        }

        $markerPosition = stripos($path, self::PUBLIC_MARKER);
        $hasPublicMarker = $markerPosition !== false;

        if ($hasPublicMarker) {
            $path = substr($path, $markerPosition + strlen(self::PUBLIC_MARKER));
        } elseif (preg_match('#^[a-z]:/#i', $path)) {
            return null;
        } elseif (Str::startsWith($path, '/')) {
            $candidate = ltrim($path, '/');

            if (! self::startsWithPublicPrefix($candidate)) {
                return null;
            }

            $path = $candidate;
        }

        $path = ltrim($path, '/');

        do {
            $prefixRemoved = false;

            foreach (self::PUBLIC_PREFIXES as $prefix) {
                if (stripos($path, $prefix) === 0) {
                    $path = ltrim(substr($path, strlen($prefix)), '/');
                    $prefixRemoved = true;
                    break;
                }
            }
        } while ($prefixRemoved);

        $path = preg_replace('#/+#', '/', trim($path, '/')) ?: '';

        if ($path === '' || self::isUnsafeRelativePath($path)) {
            return null;
        }

        return $path;
    }

    public static function exists(?string $path): bool
    {
        $path = self::normalizePublicPath($path);

        if (! $path) {
            return false;
        }

        return Storage::disk('public')->exists($path);
    }

    public static function url(?string $path): ?string
    {
        $path = self::normalizePublicPath($path);

        if (! $path) {
            return null;
        }

        return asset('storage/'.$path);
    }

    private static function startsWithPublicPrefix(string $path): bool
    {
        foreach (self::PUBLIC_PREFIXES as $prefix) {
            if (stripos($path, $prefix) === 0) {
                return true;
            }
        }

        return false;
    }

    private static function isUnsafeRelativePath(string $path): bool
    {
        $decoded = $path;

        for ($attempt = 0; $attempt < 3; $attempt++) {
            $next = rawurldecode($decoded);

            if ($next === $decoded) {
                break;
            }

            $decoded = $next;
        }

        $decoded = str_replace('\\', '/', $decoded);

        if (
            substr_count($decoded, '/') !== substr_count($path, '/')
            || self::containsControlCharacters($decoded)
            || str_contains($decoded, '?')
            || str_contains($decoded, '#')
            || str_contains($decoded, ':')
            || Str::startsWith($decoded, '/')
        ) {
            return true;
        }

        foreach (explode('/', $decoded) as $segment) {
            if ($segment === '.' || $segment === '..' || $segment === '') {
                return true;
            }
        }

        return false;
    }

    private static function containsControlCharacters(string $value): bool
    {
        return preg_match('/[\x00-\x1F\x7F]/', $value) === 1;
    }
}
