<?php

namespace App\Http\Controllers;

use App\Http\Requests\QuoteRequest;
use App\Services\PricingService;

class QuoteController extends Controller
{
    public function __construct(private PricingService $pricing) {}

    public function store(QuoteRequest $request)
    {
        $quote = $this->pricing->quote($request->sku, (int) $request->qty, (int) env('PRICE_TOLERANCE_BPS', 50));

        return response()->json([
            'quote_id' => $quote->id,
            'unit_price_cents' => $quote->unit_price_cents,
            'quote_expires_at' => $quote->quote_expires_at->format('Y-m-d H:i:s'),
        ]);
    }
}
