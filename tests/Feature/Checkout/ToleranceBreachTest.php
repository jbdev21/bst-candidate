<?php

use App\Models\SpotPrice;

it('rejects when tolerance breached', function () {
    $res = $this->postJson('/api/quote', ['sku' => 'GOLD1OZ', 'qty' => 1])->json();
    // move spot significantly
    SpotPrice::where('metal', 'GOLD')->update(['price_per_oz_cents' => SpotPrice::where('metal', 'GOLD')->value('price_per_oz_cents') * 2]);
    $r = $this->withHeaders(['Idempotency-Key' => str()->uuid()])->postJson('/api/checkout', ['quote_id' => $res['quote_id']]);
    $r->assertStatus(409)->assertJson(['error' => 'REQUOTE_REQUIRED']);
});
