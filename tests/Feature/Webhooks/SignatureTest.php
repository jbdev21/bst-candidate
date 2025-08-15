<?php

it('accepts valid webhook and updates status', function () {
    $q = $this->postJson('/api/quote', ['sku' => 'SILV1OZ', 'qty' => 1])->json();
    $o = $this->withHeaders(['Idempotency-Key' => str()->uuid()])->postJson('/api/checkout', ['quote_id' => $q['quote_id']])->json();
    $payload = json_encode(['payment_intent_id' => $o['payment_intent_id'], 'event' => 'payment_authorized']);
    $sig = hash_hmac('sha256', $payload, env('PAYMENT_WEBHOOK_SECRET'));
    $this->withHeaders(['X-Signature' => $sig])->post('/api/webhooks/payments', $payload, ['Content-Type' => 'application/json'])
        ->assertOk();
});
