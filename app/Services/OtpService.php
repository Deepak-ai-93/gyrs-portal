<?php

namespace App\Services;

use App\Contracts\SmsSender;
use App\Models\OtpCode;
use App\Models\User;
use App\Support\PhoneNumber;
use Illuminate\Support\Facades\Hash;
use Throwable;

class OtpService
{
    public const PURPOSE_LOGIN = 'login';

    public const PURPOSE_VERIFY_PHONE = 'verify_phone';

    public function __construct(private readonly SmsSender $sms) {}

    /**
     * Issue a fresh code and deliver it by SMS.
     *
     * Returns false when a code was sent recently and the cooldown is still
     * active, so callers keep the response generic.
     */
    public function request(User $user, string $purpose, ?string $ip = null): bool
    {
        $phone = PhoneNumber::e164($user->phone);

        if ($phone === null) {
            return false;
        }

        $cooldown = max(0, (int) config('otp.resend_seconds', 60));
        $recent = OtpCode::query()
            ->where('phone', $phone)
            ->where('purpose', $purpose)
            ->whereNull('consumed_at')
            ->latest()
            ->first();

        if ($recent !== null && $recent->created_at?->greaterThan(now()->subSeconds($cooldown))) {
            return false;
        }

        OtpCode::query()
            ->where('phone', $phone)
            ->where('purpose', $purpose)
            ->where('created_at', '<', now()->subDay())
            ->delete();

        $code = $this->generateCode();

        $otp = OtpCode::query()->create([
            'phone' => $phone,
            'purpose' => $purpose,
            'code_hash' => Hash::make($code),
            'expires_at' => now()->addMinutes(max(1, (int) config('otp.ttl_minutes', 10))),
            'ip_address' => $ip,
        ]);

        try {
            $this->sms->send($phone, $this->message($code));
        } catch (Throwable $exception) {
            // Do not leave an undeliverable code behind: the candidate can
            // retry immediately once delivery is working again.
            $otp->delete();

            throw $exception;
        }

        return true;
    }

    /**
     * Check a submitted code for the user's mobile number and consume it when
     * it is valid. A code can only be used once.
     */
    public function verify(User $user, string $code, string $purpose): bool
    {
        $phone = PhoneNumber::e164($user->phone);

        if ($phone === null) {
            return false;
        }

        $otp = OtpCode::query()
            ->where('phone', $phone)
            ->where('purpose', $purpose)
            ->whereNull('consumed_at')
            ->latest()
            ->first();

        if ($otp === null || $otp->isExpired()) {
            return false;
        }

        $maxAttempts = max(1, (int) config('otp.max_attempts', 5));

        if ($otp->attempts >= $maxAttempts) {
            return false;
        }

        $otp->increment('attempts');

        if (! Hash::check($code, $otp->code_hash)) {
            return false;
        }

        $otp->forceFill(['consumed_at' => now()])->save();

        return true;
    }

    private function generateCode(): string
    {
        $length = min(8, max(4, (int) config('otp.code_length', 6)));
        $max = (10 ** $length) - 1;

        return str_pad((string) random_int(0, $max), $length, '0', STR_PAD_LEFT);
    }

    private function message(string $code): string
    {
        return str_replace(
            [':code', ':minutes'],
            [$code, (string) max(1, (int) config('otp.ttl_minutes', 10))],
            (string) config('otp.message'),
        );
    }
}
