<?php

namespace App\Services;

use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    // Connexion utilisateur
    public function login(array $credentials): ?string
    {
        $token = JWTAuth::attempt($credentials);
        
        return $token ?: null; // Retourne null si échec
    }

    // Inscription utilisateur
    public function register(array $data): ?User
    {
        if (User::where('email', $data['email'])->exists()) {
            return null; // Retourne null si l'email existe
        }

        return User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
    }

    // Déconnexion (invalidate token)
    public function logout(): void
    {
        JWTAuth::invalidate(JWTAuth::getToken());
    }

    // Rafraichissement du token
    public function refresh(): string
    {
        return JWTAuth::refresh(JWTAuth::getToken());
    }

    // Utilisateur authentifié
    public function me(): ?User
    {
        try {
            return JWTAuth::parseToken()->authenticate();
        } catch (\Exception $e) {
            return null;
        }
    }
}