<?php

namespace App\Contracts;

interface DTOInterface
{
    public static function fromArray(array $data): self;
    public function toArray(): array;
}


