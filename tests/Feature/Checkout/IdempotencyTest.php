<?php

it('is idempotent on checkout', function () {
    $q = $this->postJson('/api/quote', ['sku' => 'SILV10OZ', 'qty' => 1])->json();
    $key = (string) str()->uuid();
    $a = $this->withHeaders(['Idempotency-Key' => $key])->postJson('/api/checkout', ['quote_id' => $q['quote_id']]);
    $b = $this->withHeaders(['Idempotency-Key' => $key])->postJson('/api/checkout', ['quote_id' => $q['quote_id']]);
    $a->assertOk();
    $b->assertOk();
    expect($a['order_id'])->toBe($b['order_id']);
});
