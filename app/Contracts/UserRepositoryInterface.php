<?php

namespace App\Contracts;

use App\Models\User;
use App\DTOs\Auth\RegisterDTO;

interface UserRepositoryInterface
{
    /**
     * Find a user by ID.
     */
    public function findById(int $id): ?User;

    /**
     * Find a user by email.
     */
    public function findByEmail(string $email): ?User;

    /**
     * Create a new user.
     */
    public function create(RegisterDTO $data): User;


    /**
     * Check if email exists.
     */
    public function emailExists(string $email): bool;
}
