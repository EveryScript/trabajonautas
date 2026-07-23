<?php

namespace App\Support;

final class TlsVerification
{
    public static function option(string $service, bool $verifySsl, ?string $caBundle): string|bool
    {
        $caBundle = trim((string) $caBundle);

        if ($caBundle !== '') {
            $resolvedPath = realpath($caBundle);

            if ($resolvedPath === false || ! is_file($resolvedPath) || ! is_readable($resolvedPath)) {
                throw new \RuntimeException("El CA bundle configurado para {$service} no es legible.");
            }

            return $resolvedPath;
        }

        if (! $verifySsl && ! app()->environment('local', 'testing')) {
            throw new \RuntimeException(
                "La verificacion TLS de {$service} solo puede desactivarse en entornos local o testing.",
            );
        }

        return $verifySsl;
    }
}
