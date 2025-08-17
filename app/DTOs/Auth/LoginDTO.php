<?php

namespace App\DTOs\Auth;

use App\Contracts\DTOInterface;
use Illuminate\Foundation\Http\FormRequest;

readonly class LoginDTO implements DTOInterface
{
    public function __construct(
        public string $email,
        public string $password,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            email: $data['email'],
            password: $data['password'],
        );
    }

    public static function fromRequest(FormRequest $request): self
    {
        $validated = $request->validated();
        return self::fromArray($validated);
    }

    public function toArray(): array
    {
        return [
            'email' => $this->email,
            'password' => $this->password,
        ];
    }
}
