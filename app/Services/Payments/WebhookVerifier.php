<?php

namespace App\Services\Payments;

class WebhookVerifier
{
    public function verify(string $secret, string $payload, string $signature): bool
    {
        $mac = hash_hmac('sha256', $payload, $secret);

        return hash_equals($mac, $signature);
    }
}
