<?php

namespace App\Services\Auth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\UnauthorizedException;
use Symfony\Component\HttpFoundation\Response;

final class AuthService
{
    public function login(array $attributes): array
    {
        if (!Auth::attempt($attributes)) {
            throw new UnauthorizedException('Пользователь не авторизован', Response::HTTP_UNAUTHORIZED);
        }

        $token = Auth::user()->createToken('auth_token')->plainTextToken;

        return [
            'token' => $token,
        ];
    }

    public function logout(): array
    {
        Auth::user()->tokens->each(fn($token) => $token->delete());

        return [
            'message' => 'Tokens Revoked'
        ];
    }

    public function getAuthenticatedUser(): array
    {
        return [
            'user' => Auth::user(),
        ];
    }
}
