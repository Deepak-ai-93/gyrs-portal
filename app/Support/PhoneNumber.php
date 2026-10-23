<?php

namespace App\Support;

class PhoneNumber
{
    /**
     * Normalise an Indian mobile number to its 10-digit national form.
     *
     * Accepts spaces, dashes, a leading zero, or the +91 / 91 country code.
     */
    public static function national(?string $phone): ?string
    {
        if (! is_string($phone)) {
            return null;
        }

        $digits = preg_replace('/\D/', '', $phone) ?? '';

        if (strlen($digits) === 12 && str_starts_with($digits, '91')) {
            $digits = substr($digits, 2);
        } elseif (strlen($digits) === 11 && str_starts_with($digits, '0')) {
            $digits = substr($digits, 1);
        }

        if (strlen($digits) !== 10 || $digits[0] === '0') {
            return null;
        }

        return $digits;
    }

    /**
     * Normalise a number to E.164 for SMS delivery (for example +919876543210).
     */
    public static function e164(?string $phone): ?string
    {
        $national = self::national($phone);

        if ($national === null) {
            return null;
        }

        return (string) config('otp.default_country_code', '+91').$national;
    }

    /**
     * Mask a phone number for display, keeping only the last four digits.
     */
    public static function mask(?string $phone): string
    {
        $national = self::national($phone);

        if ($national === null) {
            return 'your mobile number';
        }

        return '••••••'.substr($national, -4);
    }
}
