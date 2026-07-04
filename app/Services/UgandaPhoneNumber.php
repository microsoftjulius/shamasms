<?php

namespace App\Services;

class UgandaPhoneNumber
{
    private const PROVIDERS = [
        '70' => 'Airtel',
        '74' => 'Airtel',
        '75' => 'Airtel',
        '76' => 'MTN',
        '77' => 'MTN',
        '78' => 'MTN',
        '79' => 'MTN',
        '71' => 'UTel',
        '72' => 'Lycamobile',
        '73' => 'Other',
    ];

    public function normalize(string $input): ?array
    {
        $digits = preg_replace('/\D+/', '', $input);

        if ($digits === null || $digits === '') {
            return null;
        }

        if (str_starts_with($digits, '256')) {
            $national = substr($digits, 3);
        } elseif (str_starts_with($digits, '0')) {
            $national = substr($digits, 1);
        } else {
            $national = $digits;
        }

        if (! preg_match('/^7\d{8}$/', $national)) {
            return null;
        }

        $prefix = substr($national, 0, 2);
        $provider = self::PROVIDERS[$prefix] ?? null;

        if ($provider === null) {
            return null;
        }

        return [
            'phone' => '256'.$national,
            'display' => '+256'.$national,
            'provider' => $provider,
            'prefix' => $prefix,
        ];
    }

    public function providerFor(string $phone): string
    {
        $normalized = $this->normalize($phone);

        return $normalized['provider'] ?? 'Unknown';
    }
}
