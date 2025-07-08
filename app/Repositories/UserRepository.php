<?php
namespace App\Repositories;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Exceptions\DatabaseException;
class UserRepository
{
    /**
     * Cria um novo usuário no banco de dados dentro de uma transação.
     *
     * @param array $data Dados para criação do usuário
     * @return User
     * @throws DatabaseException
     */
    public function create(array $data): User{
        try {
            return DB::transaction(function () use ($data) {
                $user = new User();
                $user->name = $data['name'];
                $user->email = $data['email'];
                $user->password = Hash::make($data['password']);
                $user->birthdate = $data['birthdate'];
                $user->save();
                return $user;
            });
        } catch (\Throwable $e) {
            Log::error('Erro ao criar usuário: ' . $e->getMessage());
            throw new DatabaseException('Erro ao criar usuário: ' . $e->getMessage());
        }
    }

}
