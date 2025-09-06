<?php

use App\Models\Product;
use App\Models\SpotPrice;
use App\Models\User;

it('rejects when tolerance breached', function () {
    $user = User::factory()->create();
    loggedUser($user);

    $product = Product::factory()->create(['sku' => 'GOLD1OZ', 'metal' => 'GOLD']);
    SpotPrice::factory()->create(['metal' => 'GOLD']);

    $res = $this->postJson(route('api.quote.store'), ['sku' => 'GOLD1OZ', 'qty' => 1])->json();
    // move spot significantly
    SpotPrice::where('metal', 'GOLD')->update(['price_per_oz_cents' => SpotPrice::where('metal', 'GOLD')->value('price_per_oz_cents') * 2]);
    $r = $this->withHeaders(['Idempotency-Key' => str()->uuid()])->postJson(route('api.checkout.store'), ['quote_id' => $res['quote_id']]);
    $r->assertStatus(409)->assertJson(['error' => 'REQUOTE_REQUIRED']);
});
