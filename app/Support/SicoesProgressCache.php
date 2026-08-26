<?php

namespace App\Support;

use Closure;
use Illuminate\Support\Facades\Cache;

final class SicoesProgressCache
{
    private const LOCK_SECONDS = 15;

    private const LOCK_WAIT_SECONDS = 5;

    public static function key(string $date, string $sourceType = 'consulting_services'): string
    {
        $suffix = $sourceType === 'consulting_services' ? '' : ':personnel';

        return 'sicoes:progress:'.str_replace(['/', '\\', ' '], '-', $date).$suffix;
    }

    /**
     * Actualiza el progreso bajo el mismo lock para que validar el run_id y
     * escribir sean una sola operacion. Retornar null conserva el valor actual.
     *
     * @param  Closure(array): ?array  $transform
     */
    public static function update(string $date, Closure $transform, string $sourceType = 'consulting_services'): bool
    {
        return Cache::lock(self::lockKey($date, $sourceType), self::LOCK_SECONDS)
            ->block(self::LOCK_WAIT_SECONDS, function () use ($date, $transform, $sourceType): bool {
                $key = self::key($date, $sourceType);
                $cached = Cache::get($key, []);
                $current = is_array($cached) ? $cached : [];
                $next = $transform($current);

                if ($next === null) {
                    return false;
                }

                if (! Cache::put($key, $next, now()->addDay())) {
                    throw new \RuntimeException('No se pudo persistir el progreso SICOES en cache.');
                }

                return true;
            });
    }

    public static function replace(string $date, array $progress, string $sourceType = 'consulting_services'): bool
    {
        return self::update($date, static fn (): array => $progress, $sourceType);
    }

    private static function lockKey(string $date, string $sourceType): string
    {
        return self::key($date, $sourceType).':lock';
    }
}
