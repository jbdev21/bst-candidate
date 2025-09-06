<?php

use App\Models\Product;
use App\Models\SpotPrice;
use App\Models\User;
use Illuminate\Support\Facades\Http;

it('is idempotent on checkout', function () {
    $user = User::factory()->create();
    loggedUser($user);

    Product::factory()->create(['sku' => 'SILV10OZ', 'metal' => 'SILVER']);
    SpotPrice::factory()->create(['metal' => 'SILVER']);

    // Mock the fulfillment API response
    Http::fake([
        '*mock-fulfillment/availability/*' => Http::response(['available_qty' => 10], 200),
    ]);

    $q = $this->postJson(route('api.quote.store'), ['sku' => 'SILV10OZ', 'qty' => 1])->json();
    $key = (string) str()->uuid();
    $a = $this->withHeaders(['Idempotency-Key' => $key])->postJson(route('api.checkout.store'), ['quote_id' => $q['quote_id']]);
    $b = $this->withHeaders(['Idempotency-Key' => $key])->postJson(route('api.checkout.store'), ['quote_id' => $q['quote_id']]);
    $a->assertOk();
    $b->assertOk();
    expect($a['order_id'])->toBe($b['order_id']);
});
