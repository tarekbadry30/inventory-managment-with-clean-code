<?php

namespace App\DTOs\Stock;

use App\Contracts\DTOInterface;

readonly class StockUpdateDTO implements DTOInterface
{
    public function __construct(
        public int $quantity,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            quantity: $data['quantity'],
        );
    }

    public function toArray(): array
    {
        return [
            'quantity' => $this->quantity,
        ];
    }
}
