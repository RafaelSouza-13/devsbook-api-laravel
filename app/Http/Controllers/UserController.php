<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Requests\UpdateAvatarRequest;
use App\Http\Requests\UpdateCoverRequest;
use Intervention\Image\Laravel\Facades\Image;

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

    public function updateAvatar(UpdateAvatarRequest $request){
        $request->validated();
        $user = $request->user();

        // if ($request->hasFile('avatar'))
        $image = $request->file('avatar');
        $filename = md5(time().rand(0, 9999)).'.jpg';
        $destPath = public_path('/media/avatars');
        $img = Image::read($image->path())->resize(200, 200)->save($destPath.'/'.$filename);
        $user->avatar = $filename;
        $user->save();
        return response()->json([
            'success' => true,
            'message' => 'Avatar atualizado com sucesso.',
            'url' => url('media/avatars/'.$filename),
        ], 200);
    }

    public function updateCover(UpdateCoverRequest $request){
        $request->validated();
        $user = $request->user();

        // if ($request->hasFile('avatar'))
        $image = $request->file('cover');
        $filename = md5(time().rand(0, 9999)).'.jpg';
        $destPath = public_path('/media/covers');
        $img = Image::read($image->path())->resize(850, 310)->save($destPath.'/'.$filename);
        $user->avatar = $filename;
        $user->save();
        return response()->json([
            'success' => true,
            'message' => 'Cover atualizado com sucesso.',
            'url' => url('media/covers/'.$filename),
        ], 200);
    }
}
