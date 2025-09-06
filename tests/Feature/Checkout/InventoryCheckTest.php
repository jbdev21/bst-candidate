<?php

use App\Models\Product;
use App\Models\SpotPrice;
use App\Models\User;
use Illuminate\Support\Facades\Http;

it('fails when fulfillment inventory is insufficient', function () {
    $user = User::factory()->create();
    loggedUser($user);

    Product::factory()->create(['sku' => 'GOLD1OZ', 'metal' => 'GOLD']);
    SpotPrice::factory()->create(['metal' => 'GOLD']);

    // Mock insufficient inventory
    Http::fake([
        '*mock-fulfillment/availability/*' => Http::response(['available_qty' => 0], 200),
    ]);

    $q = $this->postJson(route('api.quote.store'), ['sku' => 'GOLD1OZ', 'qty' => 1])->json();
    $r = $this->withHeaders(['Idempotency-Key' => str()->uuid()])->postJson(route('api.checkout.store'), ['quote_id' => $q['quote_id']]);
    $r->assertStatus(409)->assertJson(['error' => 'OUT_OF_STOCK']);
});
