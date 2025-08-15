<?php

namespace App\Http\Controllers;

use App\Http\Requests\QuoteRequest;
use App\Services\PricingService;

class QuoteController extends Controller
{
    public function __construct(private PricingService $pricing) {}

    public function store(QuoteRequest $req)
    {
        $quote = $this->pricing->quote($req->sku, (int) $req->qty, (int) env('PRICE_TOLERANCE_BPS', 50));

        return response()->json([
        ]); // TODO: Return quote
    }
}
