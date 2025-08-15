<?php

it('fails when fulfillment inventory is insufficient', function () {
    $this->postJson('/api/mock-fulfillment/availability', ['sku' => 'GOLD1OZ', 'available_qty' => 0])->assertOk();
    $q = $this->postJson('/api/quote', ['sku' => 'GOLD1OZ', 'qty' => 1])->json();
    $r = $this->withHeaders(['Idempotency-Key' => str()->uuid()])->postJson('/api/checkout', ['quote_id' => $q['quote_id']]);
    $r->assertStatus(409)->assertJson(['error' => 'OUT_OF_STOCK']);
});
