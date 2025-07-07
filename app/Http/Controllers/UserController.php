<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    private $loggedUser;

    public function create(UserRequest $request){
        $data = $request->validated();
        $user = new User();
        $hash = password_hash($data['password'], PASSWORD_DEFAULT);
        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->password = $hash;
        $user->birthdate = $data['birthdate'];
        $user->save();
        $token = auth()->attempt([
            'email' => $data['email'],
            'password' => $data['password'],
        ]);
        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao gerar token após cadastro',
            ], 500);
        }
        return response()->json([
            'success' => true,
            'data' => $user,
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL(),
        ], 201);
    }
}
