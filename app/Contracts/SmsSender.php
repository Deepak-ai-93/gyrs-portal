<?php

namespace App\Contracts;

interface SmsSender
{
    /**
     * Deliver a plain-text SMS to an E.164 phone number.
     *
     * @throws \RuntimeException when the provider rejects or cannot deliver the message
     */
    public function send(string $to, string $message): void;
}
