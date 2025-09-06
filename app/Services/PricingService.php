<?php

namespace App\Services;

use App\DTO\PriceQuoteDTO;
use App\Models\PriceQuote;
use App\Models\Product;
use App\Models\SpotPrice;
use Illuminate\Support\Facades\Auth;

class PricingService
{
    public function quote(string $sku, int $qty, int $toleranceBps = 50)
    {
        $product = Product::where('sku', $sku)->firstOrFail();
        $spot = SpotPrice::where('metal', $product->metal)->orderByDesc('as_of')->firstOrFail();
        $unit_price_cents = (int) $spot->price_per_oz_cents * (int) $product->weight_oz + (int) $product->premium_cents;

        $priceQuoteDTO = PriceQuoteDTO::fromArray([
            'user_id' => Auth::user()->id,
            'sku' => $sku,
            'unit_price_cents' => $unit_price_cents,
            'qty' => $qty,
            'quote_expires_at' => now()->addMinutes(5),
            'basis_spot_cents' => $spot->price_per_oz_cents,
            'basis_version' => $spot->id,
            'tolerance_bps' => $toleranceBps,
        ]);

        $quote = PriceQuote::create($priceQuoteDTO->toArray());

        return $quote;
    }
}
