<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckoutRequest;
use App\Services\CheckoutService;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    public function __construct(private CheckoutService $checkout) {}

    public function store(CheckoutRequest $req)
    {
        $key = request()->header('Idempotency-Key') ?? str()->uuid()->toString();
        $success = $this->checkout->beginCheckout($req->quote_id, $key);

        return response()->json([
            'success' => $success,
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
