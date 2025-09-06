<?php

namespace App\DTO;

class PriceQuoteDTO
{
    public function __construct(
        public string $user_id,
        public string $sku,
        public int $unit_price_cents,
        public int $qty,
        public string $quote_expires_at,
        public string $basis_spot_cents,
        public int $basis_version,
        public int $tolerance_bps,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            user_id: $data['user_id'],
            sku: $data['sku'],
            unit_price_cents: $data['unit_price_cents'],
            qty: $data['qty'],
            quote_expires_at: $data['quote_expires_at'],
            basis_spot_cents: $data['basis_spot_cents'],
            basis_version: $data['basis_version'],
            tolerance_bps: $data['tolerance_bps'],
        );
    }

    public function toArray(): array
    {
        return [
            'user_id' => $this->user_id,
            'sku' => $this->sku,
            'unit_price_cents' => $this->unit_price_cents,
            'qty' => $this->qty,
            'quote_expires_at' => $this->quote_expires_at,
            'basis_spot_cents' => $this->basis_spot_cents,
            'basis_version' => $this->basis_version,
            'tolerance_bps' => $this->tolerance_bps,
        ];
    }
}
