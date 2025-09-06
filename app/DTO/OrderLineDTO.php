<?php

namespace App\DTO;

class OrderLineDTO
{
    public function __construct(
        public string $order_id,
        public string $sku,
        public int $qty,
        public int $unit_price_cents,
        public int $subtotal_cents,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            order_id: $data['order_id'],
            sku: $data['sku'],
            qty: $data['qty'],
            unit_price_cents: $data['unit_price_cents'],
            subtotal_cents: $data['subtotal_cents'],
        );
    }

    public function toArray(): array
    {
        return [
            'order_id' => $this->order_id,
            'sku' => $this->sku,
            'qty' => $this->qty,
            'unit_price_cents' => $this->unit_price_cents,
            'subtotal_cents' => $this->subtotal_cents,
        ];
    }
}
