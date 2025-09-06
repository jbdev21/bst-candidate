<?php

namespace App\DTO;

class SpotPriceDTO
{
    public function __construct(
        public string $metal,
        public int $price_per_oz_cents,
        public string $as_of,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            metal: $data['metal'],
            price_per_oz_cents: $data['price_per_oz_cents'],
            as_of: $data['as_of'],
        );
    }

    public function toArray(): array
    {
        return [
            'metal' => $this->metal,
            'price_per_oz_cents' => $this->price_per_oz_cents,
            'as_of' => $this->as_of,
        ];
    }
}
