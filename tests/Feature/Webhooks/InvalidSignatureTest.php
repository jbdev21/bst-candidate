<?php

use App\Models\Product;
use App\Models\SpotPrice;
use App\Models\User;
use Illuminate\Support\Facades\Http;

it('rejects invalid signature and does not change order status', function () {
    $user = User::factory()->create();
    loggedUser($user);

    Product::factory()->create(['sku' => 'SILV1OZ', 'metal' => 'SILVER']);
    SpotPrice::factory()->create(['metal' => 'SILVER']);

    // Mock sufficient inventory
    Http::fake([
        '*mock-fulfillment/availability/*' => Http::response(['available_qty' => 10], 200),
    ]);

    // Create quote and order
    $quote = $this->postJson(route('api.quote.store'), ['sku' => 'SILV1OZ', 'qty' => 1])->json();
    $checkout = $this->withHeaders(['Idempotency-Key' => str()->uuid()])->postJson(route('api.checkout.store'), ['quote_id' => $quote['quote_id']])->json();

    $payload = json_encode(['payment_intent_id' => $checkout['payment_intent_id'], 'event' => 'payment_authorized']);
    $invalidSig = 'invalid_signature_here';

    // Store original order status
    $order = \App\Models\Order::where('payment_intent_id', $checkout['payment_intent_id'])->first();
    $originalStatus = $order->status;

    // Send webhook with invalid signature
    $response = $this->withHeaders(['X-Signature' => $invalidSig])
        ->postJson('/api/webhooks/payments', json_decode($payload, true));

    // Should return 400 with invalid_signature error
    $response->assertStatus(400)
        ->assertJson(['error' => 'invalid_signature']);

    // Order status should remain unchanged
    $order->refresh();
    expect($order->status)->toBe($originalStatus);
});

it('rejects webhook with missing signature header', function () {
    $user = User::factory()->create();
    loggedUser($user);

    Product::factory()->create(['sku' => 'SILV1OZ', 'metal' => 'SILVER']);
    SpotPrice::factory()->create(['metal' => 'SILVER']);

    // Mock sufficient inventory
    Http::fake([
        '*mock-fulfillment/availability/*' => Http::response(['available_qty' => 10], 200),
    ]);

    // Create quote and order
    $quote = $this->postJson(route('api.quote.store'), ['sku' => 'SILV1OZ', 'qty' => 1])->json();
    $checkout = $this->withHeaders(['Idempotency-Key' => str()->uuid()])->postJson(route('api.checkout.store'), ['quote_id' => $quote['quote_id']])->json();

    $payload = ['payment_intent_id' => $checkout['payment_intent_id'], 'event' => 'payment_authorized'];

    // Store original order status
    $order = \App\Models\Order::where('payment_intent_id', $checkout['payment_intent_id'])->first();
    $originalStatus = $order->status;

    // Send webhook without signature header
    $response = $this->postJson('/api/webhooks/payments', $payload);

    // Should return 400 with invalid_signature error
    $response->assertStatus(400)
        ->assertJson(['error' => 'invalid_signature']);

    // Order status should remain unchanged
    $order->refresh();
    expect($order->status)->toBe($originalStatus);
});

it('rejects webhook for unknown payment intent', function () {
    $payload = ['payment_intent_id' => 'unknown_intent_id', 'event' => 'payment_authorized'];
    $sig = hash_hmac('sha256', json_encode($payload), env('PAYMENT_WEBHOOK_SECRET'));

    $response = $this->withHeaders(['X-Signature' => $sig])
        ->postJson('/api/webhooks/payments', $payload);

    $response->assertStatus(400)
        ->assertJson(['error' => 'unknown_intent']);
});
