<?php

namespace App\Services\Sms;

use App\Contracts\SmsSender;
use Illuminate\Support\Facades\Log;

/**
 * Local development and test driver. It never contacts an SMS provider and
 * writes the full message (including the OTP) to the application log.
 */
class LogSmsSender implements SmsSender
{
    public function send(string $to, string $message): void
    {
        Log::info('[SMS:log] '.$to.' — '.$message);
    }
}
