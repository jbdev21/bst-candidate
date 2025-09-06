<?php

use App\Models\Product;
use App\Models\SpotPrice;
use App\Models\User;

test('uses integer cents for unit price', function () {
    // setting user
    test()->actingAs(User::factory()->create());

    $product = Product::factory()->create();
    SpotPrice::factory()->create(['metal' => $product->metal]);

    $quantity = 1;
    $res = test()->postJson(route('api.quote.store'), ['sku' => $product->sku, 'qty' => $quantity]);
    $res->assertOk()->assertJsonStructure(['quote_id', 'unit_price_cents', 'quote_expires_at']);
    expect($res['unit_price_cents'])->toBeInt();
});
