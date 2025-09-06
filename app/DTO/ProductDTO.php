<?php

namespace App\DTO;

class ProductDTO
{
    public function __construct(
        public string $sku,
        public string $name,
        public string $metal,
        public int $weight_oz,
        public int $premium_cents,
        public bool $active,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            sku: $data['sku'],
            name: $data['name'],
            metal: $data['metal'],
            weight_oz: $data['weight_oz'],
            premium_cents: $data['premium_cents'],
            active: $data['active'],
        );
    }

    public function toArray(): array
    {
        return [
            'sku' => $this->sku,
            'name' => $this->name,
            'metal' => $this->metal,
            'weight_oz' => $this->weight_oz,
            'premium_cents' => $this->premium_cents,
            'active' => $this->active,
        ];
    }
}