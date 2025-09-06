<?php

use App\Models\PriceQuote;
use App\Models\Product;
use App\Models\SpotPrice;
use App\Models\User;
use Illuminate\Support\Carbon;

it('requires re-quote after expiry', function () {
    $user = User::factory()->create();
    loggedUser($user);

    $product = Product::factory()->create(['sku' => 'SILV1OZ', 'metal' => 'SILVER']);
    SpotPrice::factory()->create(['metal' => 'SILVER']);

    $response = $this->postJson(route('api.quote.store'), ['sku' => 'SILV1OZ', 'qty' => 2]);
    $response->assertOk();
    $res = $response->json();

    expect($res)->toHaveKey('quote_id');

    // force expiry
    PriceQuote::where('id', $res['quote_id'])->update(['quote_expires_at' => Carbon::now('UTC')->subMinute()]);
    $r = $this->withHeaders(['Idempotency-Key' => str()->uuid()])->postJson(route('api.checkout.store'), ['quote_id' => $res['quote_id']]);
    $r->assertStatus(409)->assertJson(['error' => 'REQUOTE_REQUIRED']);
});
