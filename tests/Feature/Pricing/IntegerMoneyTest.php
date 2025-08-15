<?php

test('uses integer cents for unit price', function () {
    $res = test()->postJson('/api/quote', ['sku' => 'GOLD1OZ', 'qty' => 1]);
    $res->assertOk()->assertJsonStructure(['quote_id', 'unit_price_cents', 'quote_expires_at']);
    expect($res['unit_price_cents'])->toBeInt();
});
