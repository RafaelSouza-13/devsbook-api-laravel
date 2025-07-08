<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;

class AuthService
{
    /**
     * Tenta autenticar usuário e gerar token JWT.
     *
     * @param string $email
     * @param string $password
     * @return string Token JWT
     *
     * @throws \RuntimeException Se falhar gerar token
     */
    public function attemptLogin(string $email, string $password): string
    {
        $token = Auth::attempt([
            'email' => $email,
            'password' => $password,
        ]);

        if (!$token) {
            throw new \RuntimeException('Erro ao gerar token após login');
        }

        return $token;
    }
}
