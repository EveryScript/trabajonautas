<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class StoragePath
{
    public static function normalizePublicPath(?string $path): ?string
    {
        $path = trim((string) $path);

        if ($path === '') {
            return null;
        }

        if (Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }

        $path = str_replace('\\', '/', $path);
        $marker = 'storage/app/public/';
        $markerPosition = stripos($path, $marker);

        if ($markerPosition !== false) {
            $path = substr($path, $markerPosition + strlen($marker));
        }

        $path = ltrim($path, '/');

        foreach (['public/storage/', 'storage/app/public/', 'app/public/', 'public/', 'storage/'] as $prefix) {
            if (Str::startsWith($path, $prefix)) {
                $path = Str::after($path, $prefix);
            }
        }

        return trim($path, '/') ?: null;
    }

    public static function exists(?string $path): bool
    {
        $path = self::normalizePublicPath($path);

        if (!$path || Str::startsWith($path, ['http://', 'https://'])) {
            return false;
        }

        return Storage::disk('public')->exists($path);
    }

    public static function url(?string $path): ?string
    {
        $path = self::normalizePublicPath($path);

        if (!$path) {
            return null;
        }

        if (Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }

        return asset('storage/' . $path);
    }
}
