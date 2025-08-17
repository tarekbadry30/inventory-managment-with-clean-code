<?php

namespace App\Http\Controllers\API;

use App\DTOs\Auth\LoginDTO;
use Illuminate\Http\Request;
use App\Services\AuthService;
use Illuminate\Http\Response;
use App\DTOs\Auth\RegisterDTO;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{

    public function __construct(
        private AuthService $authService
    ) {}

    /**
     * Register a new user and return an access token.
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']); // Hash the password
        $dto = RegisterDTO::fromRequest($request);
        $result = $this->authService->register($dto);

        return sendSuccessResponse(
            $result,
            'User registered successfully',
            Response::HTTP_CREATED
        );
    }

    /**
     * Login user and return an access token.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $dto = LoginDTO::fromRequest($request);
        $result = $this->authService->login($dto);

        return sendSuccessResponse(
            $result,
            'Login successful'
        );
    }

    /**
     * Logout user (revoke the token).
     */
    public function logout(Request $request): JsonResponse
    {
        $this->authService->logout($request->user());

        return sendSuccessResponse(
            [],
            'Logged out successfully'
        );
    }

    /**
     * Get the authenticated user.
     */
    public function user(Request $request): JsonResponse
    {
        $user = $this->authService->getAuthenticatedUser($request->user());

        return $this->sendSuccessResponse(
            ['user' => $user],
            'User retrieved successfully'
        );
    }
}
