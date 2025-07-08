<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Actions\UserAction;

class UserController extends Controller
{
    protected UserAction $userAction;
    public function __construct(UserAction $userAction){
        $this->userAction = $userAction;
    }
    public function create(StoreUserRequest $request){
        $data = $request->validated();
            try {
            $result = $this->userAction->create($data);

            return response()->json([
                'success' => true,
                'data' => $result['user'],
                'token' => $result['token'],
                'token_type' => 'bearer',
                'expires_in' => auth()->factory()->getTTL(),
            ], 201);
        } catch (\RuntimeException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(UpdateUserRequest $request){
        $user = $request->user();
        $data = $request->validated();
        // Atualiza campos opcionais, se estiverem presentes
    if (isset($data['name'])) {
        $user->name = $data['name'];
    }

    if (isset($data['email'])) {
        $user->email = $data['email'];
    }

    if (isset($data['birthdate'])) {
        $user->birthdate = $data['birthdate'];
    }

    if (isset($data['password'])) {
        $user->password = Hash::make($data['password']);
    }

    $user->save();
    return response()->json([
        'success' => true,
        'message' => 'Usuário atualizado com sucesso.',
        'data' => $user,
    ], 200);
    }
}
