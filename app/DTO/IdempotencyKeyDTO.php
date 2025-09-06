<?php

namespace App\DTO;

class IdempotencyKeyDTO
{
    public function __construct(
        public string $key,
        public string $purpose,
        public string $order_id,
        public string $created_at,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            key: $data['key'],
            purpose: $data['purpose'],
            order_id: $data['order_id'],
            created_at: $data['created_at'],
        );
    }

    public function toArray(): array
    {
        return [
            'key' => $this->key,
            'purpose' => $this->purpose,
            'order_id' => $this->order_id,
            'created_at' => $this->created_at,
        ];
    }
}