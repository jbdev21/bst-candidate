<?php

namespace App\Services;

use App\Models\PriceQuote;
use App\Models\Product;
use App\Models\SpotPrice;

class PricingService
{
    public function quote(string $sku, int $qty, int $toleranceBps = 50)
    {
        $product = Product::where('sku', $sku)->firstOrFail();
        $spot = SpotPrice::where('metal', $product->metal)->orderByDesc('as_of')->firstOrFail();

        $quote = PriceQuote::create([
            'user_id' => 1, // seeded test user, hardcoded for now.
            'sku' => $sku,
            'unit_price_cents' => 0, // TODO: Calculate unit price Must be an integer.
            'qty' => $qty,
            'quote_expires_at' => now(), // TODO: Calculate quote expires at
            'basis_spot_cents' => $spot->price_per_oz_cents,
            'basis_version' => $spot->id,
            'tolerance_bps' => $toleranceBps,
        ]);

        return null;
    }
}
