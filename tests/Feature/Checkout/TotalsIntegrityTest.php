<?php

use App\Models\Order;
use App\Models\OrderLine;
use App\Models\PriceQuote;
use App\Models\Product;
use App\Models\SpotPrice;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

it('ensures order total equals sum of order line subtotals', function () {
    $user = User::factory()->create();
    loggedUser($user);

    Product::factory()->create(['sku' => 'GOLD1OZ', 'metal' => 'GOLD']);
    SpotPrice::factory()->create(['metal' => 'GOLD']);

    // Mock sufficient inventory
    Http::fake([
        '*mock-fulfillment/availability/*' => Http::response(['available_qty' => 10], 200),
    ]);

    $q = $this->postJson(route('api.quote.store'), ['sku' => 'GOLD1OZ', 'qty' => 3])->json();
    $r = $this->withHeaders(['Idempotency-Key' => str()->uuid()])->postJson(route('api.checkout.store'), ['quote_id' => $q['quote_id']]);
    
    $r->assertSuccessful();
    $orderId = $r->json('order_id');
    
    $order = Order::findOrFail($orderId);
    $orderLines = OrderLine::where('order_id', $orderId)->get();
    
    $calculatedTotal = $orderLines->sum('subtotal_cents');
    
    expect($order->total_cents)->toBe($calculatedTotal);
});

it('ensures order line subtotal equals unit price times quantity', function () {
    $user = User::factory()->create();
    loggedUser($user);

    Product::factory()->create(['sku' => 'GOLD1OZ', 'metal' => 'GOLD']);
    SpotPrice::factory()->create(['metal' => 'GOLD']);

    // Mock sufficient inventory
    Http::fake([
        '*mock-fulfillment/availability/*' => Http::response(['available_qty' => 10], 200),
    ]);

    $q = $this->postJson(route('api.quote.store'), ['sku' => 'GOLD1OZ', 'qty' => 5])->json();
    $r = $this->withHeaders(['Idempotency-Key' => str()->uuid()])->postJson(route('api.checkout.store'), ['quote_id' => $q['quote_id']]);
    
    $r->assertSuccessful();
    $orderId = $r->json('order_id');
    
    $orderLines = OrderLine::where('order_id', $orderId)->get();
    
    foreach ($orderLines as $line) {
        $calculatedSubtotal = $line->unit_price_cents * $line->qty;
        expect($line->subtotal_cents)->toBe($calculatedSubtotal);
    }
});

it('ensures totals integrity with multiple line items', function () {
    $user = User::factory()->create();
    loggedUser($user);

    Product::factory()->create(['sku' => 'GOLD1OZ', 'metal' => 'GOLD']);
    Product::factory()->create(['sku' => 'SILVER1OZ', 'metal' => 'SILVER']);
    SpotPrice::factory()->create(['metal' => 'GOLD']);
    SpotPrice::factory()->create(['metal' => 'SILVER']);

    // Mock sufficient inventory
    Http::fake([
        '*mock-fulfillment/availability/*' => Http::response(['available_qty' => 10], 200),
    ]);

    // Create multiple quotes and orders
    $goldQuote = $this->postJson(route('api.quote.store'), ['sku' => 'GOLD1OZ', 'qty' => 2])->json();
    $silverQuote = $this->postJson(route('api.quote.store'), ['sku' => 'SILVER1OZ', 'qty' => 3])->json();
    
    $goldOrder = $this->withHeaders(['Idempotency-Key' => str()->uuid()])->postJson(route('api.checkout.store'), ['quote_id' => $goldQuote['quote_id']]);
    $silverOrder = $this->withHeaders(['Idempotency-Key' => str()->uuid()])->postJson(route('api.checkout.store'), ['quote_id' => $silverQuote['quote_id']]);
    
    $goldOrder->assertSuccessful();
    $silverOrder->assertSuccessful();
    
    // Verify both orders
    $orders = Order::whereIn('id', [$goldOrder->json('order_id'), $silverOrder->json('order_id')])->get();
    
    foreach ($orders as $order) {
        $orderLines = OrderLine::where('order_id', $order->id)->get();
        
        // Check that order total equals sum of line subtotals
        $calculatedTotal = $orderLines->sum('subtotal_cents');
        expect($order->total_cents)->toBe($calculatedTotal);
        
        // Check that each line subtotal equals unit price times quantity
        foreach ($orderLines as $line) {
            $calculatedSubtotal = $line->unit_price_cents * $line->qty;
            expect($line->subtotal_cents)->toBe($calculatedSubtotal);
        }
    }
});

it('handles single item order totals correctly', function () {
    $user = User::factory()->create();
    loggedUser($user);

    Product::factory()->create(['sku' => 'GOLD1OZ', 'metal' => 'GOLD']);
    SpotPrice::factory()->create(['metal' => 'GOLD']);

    // Mock sufficient inventory
    Http::fake([
        '*mock-fulfillment/availability/*' => Http::response(['available_qty' => 10], 200),
    ]);

    $q = $this->postJson(route('api.quote.store'), ['sku' => 'GOLD1OZ', 'qty' => 1])->json();
    $r = $this->withHeaders(['Idempotency-Key' => str()->uuid()])->postJson(route('api.checkout.store'), ['quote_id' => $q['quote_id']]);
    
    $r->assertSuccessful();
    $orderId = $r->json('order_id');
    
    $order = Order::findOrFail($orderId);
    $orderLine = OrderLine::where('order_id', $orderId)->first();
    
    // For single item, order total should equal the single line subtotal
    expect($order->total_cents)->toBe($orderLine->subtotal_cents);
    
    // And line subtotal should equal unit price (qty = 1)
    expect($orderLine->subtotal_cents)->toBe($orderLine->unit_price_cents);
});