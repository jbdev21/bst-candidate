<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class FulfillmentInventoryService
{
    public function checkAvailability(string $sku, int $qty): bool
    {
        // mocked local endpoint
        $res = Http::acceptJson()->get(url("/api/mock-fulfillment/availability/{$sku}"));
        if (! $res->ok()) {
            return false;
        }

        return (int) $res->json('available_qty', 0) >= $qty;
    }
}
