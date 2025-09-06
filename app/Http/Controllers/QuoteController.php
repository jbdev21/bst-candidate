<?php

namespace App\Http\Controllers;

use App\Http\Requests\QuoteRequest;
use App\Models\Product;
use App\Services\PricingService;
use Inertia\Inertia;

class QuoteController extends Controller
{
    public function __construct(private PricingService $pricing) {}


    public function demo(){
        $products = Product::all();
        return Inertia::render('QuoteDemo', ['products' => $products]);
    }

    public function store(QuoteRequest $request)
    {
        $quote = $this->pricing->quote($request->sku, (int) $request->qty, (int) env('PRICE_TOLERANCE_BPS', 50));

        return response()->json([
            'quote_id' => $quote->id,
            'unit_price_cents' => $quote->unit_price_cents,
            'quote_expires_at' => $quote->quote_expires_at->utc()->toISOString(),
        ]);
    }
}
