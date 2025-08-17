<?php

namespace App\DTOs\Auth;

use App\Contracts\DTOInterface;
use App\Enums\UserRoleEnum;
use Illuminate\Foundation\Http\FormRequest;

readonly class RegisterDTO implements DTOInterface
{
    public function __construct(
        public string $name,
        public string $email,
        public string $password,
        public ?string $role = UserRoleEnum::USER->value, // Default to USER role if not provided
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            email: $data['email'],
            password: $data['password'],
            role: $data['role'] ?? UserRoleEnum::USER->value, // Default to USER role if not provided
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
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password,
            'role' => $this->role,
        ];
    }
}
