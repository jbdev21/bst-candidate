<?php

namespace App\DTO;

class PriceQuoteDTO
{
    public function __construct(
        public string $id,
        public string $sku,
        public int $unit_price_cents,
        public int $qty,
        public string $quote_expires_at
    ) {}
}
