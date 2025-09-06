<?php

namespace App\DTO;

class OrderDTO
{
    public function __construct(
        public string $user_id,
        public int $total_cents,
        public string $status,
        public string $payment_intent_id,
        public ?string $id = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            user_id: $data['user_id'],
            total_cents: $data['total_cents'],
            status: $data['status'],
            payment_intent_id: $data['payment_intent_id'],
            id: $data['id'] ?? null,
        );
    }

    public function toArray(): array
    {
        $data = [
            'user_id' => $this->user_id,
            'total_cents' => $this->total_cents,
            'status' => $this->status,
            'payment_intent_id' => $this->payment_intent_id,
        ];

        if ($this->id !== null) {
            $data['id'] = $this->id;
        }

        return $data;
    }
}
