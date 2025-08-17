<?php

namespace App\Repositories;

use App\Models\User;
use App\DTOs\Auth\RegisterDTO;
use Illuminate\Support\Facades\Hash;
use App\Contracts\UserRepositoryInterface;

class UserRepository implements UserRepositoryInterface
{
    /**
     * Find a user by ID.
     */
    public function findById(int $id): ?User
    {
        return User::find($id);
    }

    /**
     * Find a user by email.
     */
    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    /**
     * Create a new user.
     */
    public function create(RegisterDTO $data): User
    {
        return User::create((array)$data);
    }


    /**
     * Check if email exists.
     */
    public function emailExists(string $email): bool
    {
        return User::where('email', $email)->exists();
    }
}
