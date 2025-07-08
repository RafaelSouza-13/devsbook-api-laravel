<?php
namespace App\Actions;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Services\AuthService;
use App\Repositories\UserRepository;

class UserAction
{
    protected UserRepository $repository;
    protected AuthService $authService;

    public function __construct(UserRepository $repository, AuthService $authService)
    {
        $this->repository = $repository;
        $this->authService = $authService;
    }
    /**
     * Cria usuário e retorna usuário e token JWT.
     *
     * @param array $data Dados do usuário
     * @return array{user: User, token: string}
     */
    public function create(array $data): array
    {
        $user = $this->repository->create($data);

        $token = $this->authService->attemptLogin($data['email'], $data['password']);

        return [
            'user' => $user,
            'token' => $token,
        ];
    }
}
