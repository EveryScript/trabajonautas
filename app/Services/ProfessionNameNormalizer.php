<?php

namespace App\Services;

use Illuminate\Support\Str;

class ProfessionNameNormalizer
{
    public function normalize(string $value): string
    {
        $value = html_entity_decode(strip_tags($value), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $value = Str::of($value)
            ->ascii()
            ->lower()
            ->replace('&', ' y ')
            ->replaceMatches('/[^\pL\pN]+/u', ' ')
            ->replaceMatches('/\s+/u', ' ')
            ->trim()
            ->toString();

        return preg_replace('/\s+e\s+/u', ' y ', $value) ?: $value;
    }

    public function variants(string $value): array
    {
        $base = $this->normalize($value);
        $variants = collect([$base]);

        $withoutDegree = preg_replace(
            '/^(?:licenciatura|licenciado|licenciada|lic)\s+(?:en\s+)?/u',
            '',
            $base,
        );
        if (is_string($withoutDegree) && $withoutDegree !== '') {
            $variants->push($withoutDegree);
        }

        if (preg_match('/^(?:ing|ingeniero|ingeniera)\s+(?:en\s+|de\s+)?(.+)$/u', $base, $matches)) {
            $descriptor = $this->feminizeEngineeringDescriptor($matches[1]);
            $variants->push('ingenieria '.$descriptor);
        }

        $safeGenderVariants = [
            '/^trabajador(?:a)?\s+social$/u' => 'trabajo social',
            '/^administrador(?:a)?\s+de\s+empresas$/u' => 'administracion de empresas',
            '/^psicolog[oa]$/u' => 'psicologia',
        ];
        foreach ($safeGenderVariants as $pattern => $replacement) {
            if (preg_match($pattern, $base)) {
                $variants->push($replacement);
            }
        }

        $withoutProfessionalPrefix = preg_replace(
            '/^(?:doctor|doctora|dr|dra|tecnico|tecnica|tec)\s+(?:en\s+)?/u',
            '',
            $base,
        );
        if (is_string($withoutProfessionalPrefix) && $withoutProfessionalPrefix !== '') {
            $variants->push($withoutProfessionalPrefix);
        }

        return $variants
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    public function similarity(string $left, string $right): float
    {
        $left = $this->normalize($left);
        $right = $this->normalize($right);

        if ($left === '' || $right === '') {
            return 0.0;
        }

        if ($left === $right) {
            return 1.0;
        }

        $maxLength = max(strlen($left), strlen($right));
        if ($maxLength === 0) {
            return 0.0;
        }

        $levenshtein = 1 - (levenshtein($left, $right) / $maxLength);
        $leftTokens = collect(explode(' ', $left))->filter()->unique();
        $rightTokens = collect(explode(' ', $right))->filter()->unique();
        $union = $leftTokens->merge($rightTokens)->unique()->count();
        $intersection = $leftTokens->intersect($rightTokens)->count();
        $jaccard = $union > 0 ? $intersection / $union : 0.0;

        return round(max(0.0, min(1.0, ($levenshtein * 0.65) + ($jaccard * 0.35))), 4);
    }

    private function feminizeEngineeringDescriptor(string $descriptor): string
    {
        $words = preg_split('/\s+/u', trim($descriptor)) ?: [];
        $lastIndex = array_key_last($words);

        if ($lastIndex !== null && preg_match('/ero$/u', $words[$lastIndex])) {
            $words[$lastIndex] = preg_replace('/ero$/u', 'era', $words[$lastIndex]) ?: $words[$lastIndex];
        }

        return implode(' ', $words);
    }
}
