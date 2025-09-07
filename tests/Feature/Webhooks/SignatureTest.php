<?php

use App\Models\Product;
use App\Models\SpotPrice;
use App\Models\User;
use Illuminate\Support\Facades\Http;

it('accepts valid webhook and updates order status to authorized', function () {
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

    // Prepare webhook payload for payment_authorized event
    $payload = json_encode(['payment_intent_id' => $checkout['payment_intent_id'], 'event' => 'payment_authorized']);
    $sig = hash_hmac('sha256', $payload, config('webhook.payment_webhook_secret'));

    // Send webhook with valid signature
    $response = $this->withHeaders(['X-Signature' => $sig, 'Content-Type' => 'application/json'])
        ->postJson('/api/webhooks/payments', json_decode($payload, true));

    $response->assertOk();

    // Verify order status updated to authorized
    $order = \App\Models\Order::where('payment_intent_id', $checkout['payment_intent_id'])->first();
    expect($order->status)->toBe('authorized');
});

it('handles payment_captured transition from authorized status', function () {
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

    // First, authorize the payment
    $authorizePayload = json_encode(['payment_intent_id' => $checkout['payment_intent_id'], 'event' => 'payment_authorized']);
    $authorizeSig = hash_hmac('sha256', $authorizePayload, config('webhook.payment_webhook_secret'));

    $this->withHeaders(['X-Signature' => $authorizeSig, 'Content-Type' => 'application/json'])
        ->postJson('/api/webhooks/payments', json_decode($authorizePayload, true))
        ->assertOk();

    // Then capture the payment
    $capturePayload = json_encode(['payment_intent_id' => $checkout['payment_intent_id'], 'event' => 'payment_captured']);
    $captureSig = hash_hmac('sha256', $capturePayload, config('webhook.payment_webhook_secret'));

    $response = $this->withHeaders(['X-Signature' => $captureSig, 'Content-Type' => 'application/json'])
        ->postJson('/api/webhooks/payments', json_decode($capturePayload, true));

    $response->assertOk();

    // Verify order status updated to captured
    $order = \App\Models\Order::where('payment_intent_id', $checkout['payment_intent_id'])->first();
    expect($order->status)->toBe('captured');
});

it('rejects payment_captured when order is not authorized', function () {
    $user = User::factory()->create();
    loggedUser($user);

    $product = Product::factory()->create(['sku' => 'SILV1OZ', 'metal' => 'SILVER']);
    SpotPrice::factory()->create(['metal' => 'SILVER']);

    // Mock sufficient inventory
    Http::fake([
        '*mock-fulfillment/availability/*' => Http::response(['available_qty' => 10], 200),
    ]);

    // Create quote and order (status will be 'pending')
    $quote = $this->postJson(route('api.quote.store'), ['sku' => 'SILV1OZ', 'qty' => 1])->json();
    $checkout = $this->withHeaders(['Idempotency-Key' => str()->uuid()])->postJson(route('api.checkout.store'), ['quote_id' => $quote['quote_id']])->json();

    // Try to capture payment without authorization
    $capturePayload = json_encode(['payment_intent_id' => $checkout['payment_intent_id'], 'event' => 'payment_captured']);
    $captureSig = hash_hmac('sha256', $capturePayload, config('webhook.payment_webhook_secret'));

    $response = $this->withHeaders(['X-Signature' => $captureSig, 'Content-Type' => 'application/json'])
        ->postJson('/api/webhooks/payments', json_decode($capturePayload, true));

    $response->assertOk(); // Webhook still responds OK even if no state change

    // Verify order status remains pending (no state change)
    $order = \App\Models\Order::where('payment_intent_id', $checkout['payment_intent_id'])->first();
    expect($order->status)->toBe('pending');
});
