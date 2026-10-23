<?php

namespace App\Services\Sms;

use App\Contracts\SmsSender;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class TwilioSmsSender implements SmsSender
{
    public function send(string $to, string $message): void
    {
        $accountSid = (string) config('services.twilio.account_sid');
        $authToken = (string) config('services.twilio.auth_token');
        $from = (string) config('services.twilio.from');
        $messagingServiceSid = (string) config('services.twilio.messaging_service_sid');

        if ($accountSid === '' || $authToken === '') {
            throw new RuntimeException('Twilio credentials are not configured. Set TWILIO_ACCOUNT_SID and TWILIO_AUTH_TOKEN.');
        }

        if ($from === '' && $messagingServiceSid === '') {
            throw new RuntimeException('Set TWILIO_FROM_NUMBER or TWILIO_MESSAGING_SERVICE_SID to send SMS.');
        }

        $payload = [
            'To' => $to,
            'Body' => $message,
        ];

        if ($from !== '') {
            $payload['From'] = $from;
        } else {
            $payload['MessagingServiceSid'] = $messagingServiceSid;
        }

        $response = Http::asForm()
            ->withBasicAuth($accountSid, $authToken)
            ->timeout((int) config('services.twilio.timeout', 10))
            ->post("https://api.twilio.com/2010-04-01/Accounts/{$accountSid}/Messages.json", $payload);

        if ($response->failed()) {
            throw new RuntimeException(sprintf(
                'Twilio rejected the SMS (%s): %s',
                $response->status(),
                $response->json('message') ?? 'unknown error',
            ));
        }
    }
}
