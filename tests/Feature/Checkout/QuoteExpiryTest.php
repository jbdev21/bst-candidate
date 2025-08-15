<?php

use App\Models\PriceQuote;
use Illuminate\Support\Carbon;

it('requires re-quote after expiry', function () {
    $res = $this->postJson('/api/quote', ['sku' => 'SILV1OZ', 'qty' => 2])->json();
    // force expiry
    PriceQuote::where('id', $res['quote_id'])->update(['quote_expires_at' => Carbon::now('UTC')->subMinute()]);
    $r = $this->withHeaders(['Idempotency-Key' => str()->uuid()])->postJson('/api/checkout', ['quote_id' => $res['quote_id']]);
    $r->assertStatus(409)->assertJson(['error' => 'REQUOTE_REQUIRED']);
});
