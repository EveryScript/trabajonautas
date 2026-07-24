<?php

namespace App\Support;

use Closure;
use Illuminate\Support\Facades\Cache;

final class SicoesProgressCache
{
    private const LOCK_SECONDS = 15;

    private const LOCK_WAIT_SECONDS = 5;

    public static function key(string $date): string
    {
        return 'sicoes:progress:'.str_replace(['/', '\\', ' '], '-', $date);
    }

    /**
     * Actualiza el progreso bajo el mismo lock para que validar el run_id y
     * escribir sean una sola operacion. Retornar null conserva el valor actual.
     *
     * @param  Closure(array): ?array  $transform
     */
    public static function update(string $date, Closure $transform): bool
    {
        return Cache::lock(self::lockKey($date), self::LOCK_SECONDS)
            ->block(self::LOCK_WAIT_SECONDS, function () use ($date, $transform): bool {
                $key = self::key($date);
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

    public static function replace(string $date, array $progress): bool
    {
        return self::update($date, static fn (): array => $progress);
    }

    private static function lockKey(string $date): string
    {
        return self::key($date).':lock';
    }
}
