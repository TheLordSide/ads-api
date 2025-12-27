<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AuthService;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\Controller;
use App\Http\Resources\ErrorResource;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Inscription
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:255',
            'email'    => 'required|email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return ErrorResource::throwError('Validation error', 422);
        }

        $user = $this->authService->register($validator->validated());

        if (!$user) {
            return ErrorResource::throwError('Email already exists', 409);
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'User registered successfully',
            'data'    => $user,
        ], 201);
    }

    /**
     * Connexion
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return ErrorResource::throwError('Validation error', 422);
        }

        $token = $this->authService->login($validator->validated());

        if (!$token) {
            return ErrorResource::throwError('Invalid credentials', 401);
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Login successful',
            'data'    => [
                'access_token' => $token,
                'token_type'   => 'bearer',
                'expires_in'   => JWTAuth::factory()->getTTL() * 60,
            ],
        ]);
    }

    /**
     * Utilisateur connecté
     */
    public function me()
    {
        $user = $this->authService->me();

        if (!$user) {
            return ErrorResource::throwError('Unauthenticated', 401);
        }

        return response()->json([
            'status' => 'success',
            'data'   => $user,
        ]);
    }

    /**
     * Déconnexion
     */
    public function logout()
    {
        try {
            $this->authService->logout();

            return response()->json([
                'status'  => 'success',
                'message' => 'Successfully logged out',
            ]);
        } catch (\Exception $e) {
            return ErrorResource::throwError('Error logging out', 500);
        }
    }

    /**
     * Rafraîchir le token
     */
    public function refresh()
    {
        try {
            $token = $this->authService->refresh();

            return response()->json([
                'status' => 'success',
                'data'   => [
                    'access_token' => $token,
                    'token_type'   => 'bearer',
                    'expires_in'   => JWTAuth::factory()->getTTL() * 60,
                ],
            ]);
        } catch (\Exception $e) {
            return ErrorResource::throwError('Error refreshing token', 401);
        }
    }
}