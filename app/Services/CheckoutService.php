<?php

namespace App\Services;

class CheckoutService
{
    public function __construct(private FulfillmentInventoryService $inventory) {}

    public function beginCheckout(string $quoteId, string $idempotencyKey)
    {
        return true;

    }
}
