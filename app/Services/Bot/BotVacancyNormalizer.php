<?php

namespace App\Services\Bot;

use Carbon\Carbon;
use Illuminate\Support\Str;

class BotVacancyNormalizer
{
    public function normalize(string $title, string $description, array $analysis = [], array $rawData = []): array
    {
        $expiration = $this->resolveExpiration(
            title: $title,
            description: $description,
            geminiDate: $analysis['expiration_date'] ?? null,
        );

        $location = $this->resolveLocation(
            title: $title,
            description: $description,
            geminiDepartment: $analysis['department'] ?? null,
            geminiLocation: $analysis['location'] ?? null,
            rawData: $rawData,
        );

        $salary = $this->resolveSalary(
            title: $title,
            description: $description,
            geminiSalary: $analysis['salary'] ?? null,
        );

        return [
            'expiration_date' => $expiration['value'],
            'expiration_source' => $expiration['source'],
            'expiration_detected_text' => $expiration['detected_text'],
            'department' => $location['department'],
            'location' => $location['location'],
            'municipality' => $location['municipality'],
            'location_source' => $location['source'],
            'location_detected_text' => $location['detected_text'],
            'salary' => $salary['value'],
            'salary_source' => $salary['source'],
            'salary_detected_text' => $salary['detected_text'],
        ];
    }

    public function expirationForStorage(?string $value): string
    {
        $date = $this->parseDate((string) $value);

        if (!$date) {
            $date = now()->addDays(15);
        }

        return $date->setTime(23, 59, 0)->format('Y-m-d H:i:s');
    }

    public function salaryForStorage(mixed $value): int
    {
        $salary = $this->parseSalaryValue($value);

        return is_int($salary) ? $salary : 0;
    }

    private function resolveExpiration(string $title, string $description, mixed $geminiDate): array
    {
        foreach ([
            'title' => $title,
            'description' => $description,
            'gemini' => (string) $geminiDate,
        ] as $source => $text) {
            $match = $this->detectDate($text);

            if ($match) {
                return [
                    'value' => $match['date']->setTime(23, 59, 0)->format('Y-m-d H:i:s'),
                    'source' => $source,
                    'detected_text' => $match['text'],
                ];
            }
        }

        return [
            'value' => now()->addDays(15)->setTime(23, 59, 0)->format('Y-m-d H:i:s'),
            'source' => 'default',
            'detected_text' => null,
        ];
    }

    private function resolveLocation(
        string $title,
        string $description,
        mixed $geminiDepartment,
        mixed $geminiLocation,
        array $rawData,
    ): array {
        foreach ([
            'title' => $title,
            'description' => $description,
        ] as $source => $text) {
            $detected = $this->detectNamedLocation($text);

            if ($detected) {
                return [
                    ...$detected,
                    'source' => $source,
                ];
            }
        }

        $gemini = $this->locationFromValues($geminiDepartment, $geminiLocation, 'gemini');
        if ($gemini) {
            return $gemini;
        }

        $rss = $this->locationFromValues(
            $rawData['rss_department'] ?? $rawData['department'] ?? null,
            $rawData['rss_location'] ?? $rawData['location'] ?? null,
            'rss',
        );
        if ($rss) {
            return $rss;
        }

        $prefix = $this->detectTitlePrefixLocation($title);
        if ($prefix) {
            return $prefix;
        }

        return [
            'department' => 'No especificado',
            'location' => 'No especificado',
            'municipality' => null,
            'source' => 'default',
            'detected_text' => null,
        ];
    }

    private function resolveSalary(string $title, string $description, mixed $geminiSalary): array
    {
        foreach ([
            'title' => $title,
            'description' => $description,
            'gemini' => (string) $geminiSalary,
        ] as $source => $text) {
            $salary = $this->detectSalary($text);

            if ($salary) {
                return [
                    'value' => $salary['value'],
                    'source' => $source,
                    'detected_text' => $salary['text'],
                ];
            }
        }

        return [
            'value' => 0,
            'source' => 'default_zero',
            'detected_text' => null,
        ];
    }

    private function detectDate(?string $text): ?array
    {
        $text = $this->cleanText((string) $text);

        if ($text === '' || $this->isEmptyValue($text)) {
            return null;
        }

        $patterns = [
            '/\b(\d{4})-(\d{1,2})-(\d{1,2})\b/u',
            '/\b(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{2,4})\b/u',
        ];

        foreach ($patterns as $index => $pattern) {
            if (preg_match($pattern, $text, $matches)) {
                try {
                    $date = $index === 0
                        ? Carbon::create((int) $matches[1], (int) $matches[2], (int) $matches[3])
                        : Carbon::create($this->normalizeYear((int) $matches[3]), (int) $matches[2], (int) $matches[1]);

                    return ['date' => $date, 'text' => $matches[0]];
                } catch (\Throwable) {
                    continue;
                }
            }
        }

        $ascii = $this->normalizeForDate($text);
        $months = $this->months();

        if (preg_match('/\b(\d{1,2})\s+de\s+([a-z]+)(?:\s+de\s+(\d{4}))?\b/u', $ascii, $matches)) {
            $month = $months[$matches[2]] ?? null;

            if ($month) {
                try {
                    $year = isset($matches[3]) && $matches[3] !== ''
                        ? (int) $matches[3]
                        : now()->year;

                    return [
                        'date' => Carbon::create($year, $month, (int) $matches[1]),
                        'text' => $matches[0],
                    ];
                } catch (\Throwable) {
                    return null;
                }
            }
        }

        return null;
    }

    private function parseDate(string $value): ?Carbon
    {
        $detected = $this->detectDate($value);

        if ($detected) {
            return $detected['date'];
        }

        try {
            return Carbon::parse($value);
        } catch (\Throwable) {
            return null;
        }
    }

    private function detectNamedLocation(?string $text): ?array
    {
        $normalized = $this->normalizeText((string) $text);

        if ($normalized === '') {
            return null;
        }

        foreach (config('bolivia_municipalities', []) as $municipality => $department) {
            $needle = $this->normalizeText((string) $municipality);

            if ($needle !== '' && preg_match('/\b' . preg_quote($needle, '/') . '\b/', $normalized)) {
                $municipalityName = Str::headline($needle);

                return [
                    'department' => (string) $department,
                    'location' => $municipalityName,
                    'municipality' => [
                        'municipality' => $municipalityName,
                        'department' => (string) $department,
                        'is_main_city' => $this->isMainCity($municipalityName),
                    ],
                    'detected_text' => $municipality,
                ];
            }
        }

        foreach ($this->departments() as $needle => $label) {
            if (preg_match('/\b' . preg_quote($needle, '/') . '\b/', $normalized)) {
                return [
                    'department' => $label,
                    'location' => $label,
                    'municipality' => $this->mainCityMunicipality($label),
                    'detected_text' => $label,
                ];
            }
        }

        return null;
    }

    private function detectTitlePrefixLocation(string $title): ?array
    {
        $title = $this->cleanText($title);

        if (!preg_match('/^\s*([A-Za-z]{2,3})\s*[-–—]\s*/u', $title, $matches)) {
            return null;
        }

        $prefix = strtoupper($matches[1]);
        $map = [
            'CB' => ['Cochabamba', 'Cochabamba'],
            'CBB' => ['Cochabamba', 'Cochabamba'],
            'CBA' => ['Cochabamba', 'Cochabamba'],
            'LP' => ['La Paz', 'La Paz'],
            'LPZ' => ['La Paz', 'La Paz'],
            'SC' => ['Santa Cruz', 'Santa Cruz'],
            'SCZ' => ['Santa Cruz', 'Santa Cruz'],
            'SCR' => ['Santa Cruz', 'Santa Cruz'],
            'OR' => ['Oruro', 'Oruro'],
            'ORU' => ['Oruro', 'Oruro'],
            'PT' => ['Potosí', 'Potosí'],
            'POT' => ['Potosí', 'Potosí'],
            'CH' => ['Chuquisaca', 'Chuquisaca'],
            'CHQ' => ['Chuquisaca', 'Chuquisaca'],
            'SRE' => ['Chuquisaca', 'Sucre'],
            'TJ' => ['Tarija', 'Tarija'],
            'TJA' => ['Tarija', 'Tarija'],
            'BN' => ['Beni', 'Beni'],
            'BEN' => ['Beni', 'Beni'],
            'PD' => ['Pando', 'Pando'],
            'PDO' => ['Pando', 'Pando'],
            'PND' => ['Pando', 'Pando'],
        ];

        if (!isset($map[$prefix])) {
            return null;
        }

        [$department, $location] = $map[$prefix];

        return [
            'department' => $department,
            'location' => $location,
            'municipality' => $this->mainCityMunicipality($location, $department),
            'source' => 'title_prefix',
            'detected_text' => $matches[0],
        ];
    }

    private function locationFromValues(mixed $department, mixed $location, string $source): ?array
    {
        $department = $this->cleanText((string) $department);
        $location = $this->cleanText((string) $location);

        if ($this->isEmptyValue($department)) {
            $department = '';
        }

        if ($this->isEmptyValue($location)) {
            $location = '';
        }

        if ($department === '' && $location !== '') {
            $detected = $this->detectNamedLocation($location);
            $department = $detected['department'] ?? '';
        }

        if ($department === '' && $location === '') {
            return null;
        }

        $department = $department !== '' ? $department : $location;
        $location = $location !== '' ? $location : $department;

        return [
            'department' => $department,
            'location' => $location,
            'municipality' => $this->mainCityMunicipality($location, $department),
            'source' => $source,
            'detected_text' => trim($department . ' ' . $location),
        ];
    }

    private function detectSalary(?string $text): ?array
    {
        $text = $this->cleanText((string) $text);

        if ($text === '' || $this->isEmptyValue($text)) {
            return null;
        }

        if (preg_match('/\b(?:bs\.?|bob)\s*([0-9][0-9\.,\s]*)\b/iu', $text, $matches)
            || preg_match('/\b([0-9][0-9\.,\s]*)\s*(?:bs\.?|bob)\b/iu', $text, $matches)) {
            $number = preg_replace('/[^\d]/', '', $matches[1]);

            return $number !== '' ? ['value' => (int) $number, 'text' => $matches[0]] : null;
        }

        $parsed = $this->parseSalaryValue($text);

        if (is_int($parsed) && $parsed > 0) {
            return ['value' => $parsed, 'text' => $text];
        }

        return null;
    }

    private function parseSalaryValue(mixed $salary): int|string
    {
        if (is_int($salary) || is_float($salary)) {
            return (int) $salary;
        }

        $salary = $this->cleanText((string) $salary);

        if ($salary === '' || $this->isEmptyValue($salary)) {
            return 0;
        }

        if (preg_match('/^\s*[0-9][0-9\.,\s]*\s*$/', $salary)) {
            $number = preg_replace('/[^\d]/', '', $salary);

            return $number !== '' ? (int) $number : 0;
        }

        return $salary;
    }

    private function isEmptyValue(string $value): bool
    {
        return in_array($this->normalizeText($value), [
            '',
            '0',
            'null',
            'no especificado',
            'sueldo no declarado',
            'sueldo no declarado por la institucion',
            'no declarado',
            'sin sueldo',
            'sin ubicacion',
        ], true);
    }

    private function normalizeYear(int $year): int
    {
        return $year < 100 ? 2000 + $year : $year;
    }

    private function months(): array
    {
        return [
            'enero' => 1,
            'febrero' => 2,
            'marzo' => 3,
            'abril' => 4,
            'mayo' => 5,
            'junio' => 6,
            'julio' => 7,
            'agosto' => 8,
            'septiembre' => 9,
            'setiembre' => 9,
            'octubre' => 10,
            'noviembre' => 11,
            'diciembre' => 12,
        ];
    }

    private function departments(): array
    {
        return [
            'la paz' => 'La Paz',
            'santa cruz' => 'Santa Cruz',
            'cochabamba' => 'Cochabamba',
            'oruro' => 'Oruro',
            'potosi' => 'Potosí',
            'tarija' => 'Tarija',
            'chuquisaca' => 'Chuquisaca',
            'sucre' => 'Chuquisaca',
            'beni' => 'Beni',
            'pando' => 'Pando',
        ];
    }

    private function mainCityMunicipality(string $location, ?string $department = null): ?array
    {
        $department = $department ?: ($this->departments()[$this->normalizeText($location)] ?? null);

        if (!$department) {
            return null;
        }

        return [
            'municipality' => $location,
            'department' => $department,
            'is_main_city' => $this->isMainCity($location),
        ];
    }

    private function isMainCity(string $location): bool
    {
        return in_array($this->normalizeText($location), [
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

    private function cleanText(string $text): string
    {
        $text = html_entity_decode(strip_tags($text), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = str_replace(["\u{2013}", "\u{2014}", "\u{00A0}"], ['-', '-', ' '], $text);

        return trim(preg_replace('/\s+/u', ' ', $text) ?: '');
    }

    private function normalizeForDate(string $value): string
    {
        return Str::of($value)
            ->ascii()
            ->lower()
            ->replaceMatches('/[^a-z0-9\s]+/', ' ')
            ->squish()
            ->toString();
    }

    private function normalizeText(string $value): string
    {
        return Str::of($value)
            ->ascii()
            ->lower()
            ->replaceMatches('/[^a-z0-9]+/', ' ')
            ->squish()
            ->toString();
    }
}
