<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckoutRequest;
use App\Services\CheckoutService;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    public function __construct(private CheckoutService $checkout) {}

    public function store(CheckoutRequest $request)
    {
        $validated = $request->validated();
        $key = $validated['headers']['Idempotency-Key'];
        $result = $this->checkout->beginCheckout($validated['quote_id'], $key);

        if (isset($result['error'])) {
            return response()->json([
                'error' => $result['error'],
            ], $result['status']);
        }

        return response()->json([
            'success' => $result['success'],
            'order_id' => $result['order_id'] ?? null,
            'payment_intent_id' => $result['payment_intent_id'] ?? null,
        ]);
    }

    // Mock fulfillment API
    public function mockAvailability(string $sku)
    {
        $stock = cache()->get("stock:$sku", 10);

        return response()->json(['available_qty' => (int) $stock]);
    }

    public function setMockAvailability(Request $r)
    {
        $sku = $r->input('sku');
        $qty = (int) $r->input('available_qty', 10);
        cache()->put("stock:$sku", $qty, now()->addHour());

        return response()->json(['ok' => true]);
    }
}
