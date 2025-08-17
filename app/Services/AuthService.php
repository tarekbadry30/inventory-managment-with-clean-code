<?php

namespace App\Services;

use App\Models\User;
use App\DTOs\Auth\LoginDTO;
use App\DTOs\Auth\RegisterDTO;
use App\Contracts\UserRepositoryInterface;
use App\Http\Resources\User\UserResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;

class AuthService
{
    public function __construct(
        private UserRepositoryInterface $userRepository
    ) {}

    /**
     * Register a new user.
     */
    public function register(RegisterDTO $dto): array
    {
        // Check if email already exists
        if ($this->userRepository->emailExists($dto->email)) {
            throw ValidationException::withMessages([
                'email' => ['The email has already been taken.']
            ]);
        }

        // Create user
        $user = $this->userRepository->create($dto);

        // Generate token
        $token = $this->createToken($user);

        return [
            'user' => new UserResource($user),
            'access_token' => $token,
            'token_type' => 'Bearer',
        ];
    }

    /**
     * Authenticate user and return token.
     */
    public function login(LoginDTO $dto): array
    {
        // Attempt authentication
        if (!Auth::attempt($dto->toArray())) {
            throw new AuthenticationException('The provided credentials are incorrect.');
        }

        // Get authenticated user
        $user = $this->userRepository->findByEmail($dto->email);

        if (!$user) {
            throw new AuthenticationException('User not found.');
        }

        // Generate token
        $token = $this->createToken($user);

        return [
            'user' => new UserResource($user),
            'access_token' => $token,
            'token_type' => 'Bearer',
        ];
    }

    /**
     * Logout user by revoking tokens.
     */
    public function logout(User $user): bool
    {
        return $this->revokeAllTokens($user);
    }

    /**
     * Get authenticated user data.
     */
    public function getAuthenticatedUser(User $user): array
    {
        return [
            'user' => new UserResource($user)
        ];
    }

    /**
     * Revoke all user tokens.
     */
    public function revokeAllTokens(User $user): bool
    {
        $user->tokens()->delete();
        return true;
    }

    /**
     * Create access token for user.
     */
    public function createToken(User $user, string $tokenName = 'auth_token'): string
    {
        return $user->createToken($tokenName)->plainTextToken;
    }
}
