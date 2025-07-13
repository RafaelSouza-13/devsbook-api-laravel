<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Requests\UpdateAvatarRequest;
use App\Http\Requests\UpdateCoverRequest;
use Intervention\Image\Laravel\Facades\Image;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Hash;
use App\Actions\UserAction;
use App\Models\Post;
use App\Models\UserRelation;
use Carbon\Carbon;
use Nette\Utils\DateTime;

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

    public function read($id = null){
        if ($id === null) {
            $id = auth()->id();
        }
        try {
            $info = User::findOrFail($id);
            $info['avatar'] = url('media/avatars/'.$info['avatar']);
            $info['cover'] = url('media/avatars/'.$info['cover']);
            $info['me'] = $info['id'] == auth()->id() ? true : false;
            $dateFrom = new DateTime($info['birthdate']);
            $today = Carbon::now();
            $info['age'] = $today->diff($dateFrom)->y;

            $info['followers'] = UserRelation::where('user_to', $info['id'])->count();
            $info['following'] = UserRelation::where('user_from', $info['id'])->count();
            $info['photoCount'] = Post::where('user_id', $info['id'])
                ->where('type', 'photo')->count();

            $hasRelation = UserRelation::where('user_from', auth()->id())->where('user_to', $info['id'])->count();
            $info['isFollowing'] = $hasRelation > 0 ? true : false;            
            return response()->json($info, 200);
            
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'usuário não encontrado'], 404);
        }
        
    }

    public function follow($id){
        if ($id === null) {
            $id = auth()->id();
        }
        try {
            $user = User::findOrFail($id);
            if($user['id'] == auth()->id()){
                return response()->json([
                    'error' => 'Você não pode seguir a si mesmo.'
                ], 400);
            }
            $relation = UserRelation::where('user_from', auth()->id())->where('user_to', $id)->exists();
            if($relation){
                $relation->delete();
            }else{
                $newRelation = new UserRelation();
                $newRelation->user_from = auth()->id();
                $newRelation->user_to = $id;
                $newRelation->save();
            }
                  
            return response()->json($user, 200);
            
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'usuário não encontrado'], 404);
        }
    }

    public function followers($id){
        if ($id === null) {
            $id = auth()->id();
        }
        try {
            $user = User::findOrFail($id);
            
            $followers = UserRelation::where('user_to', $id)->get();
            $following = UserRelation::where('user_from', $id)->get();
            $followersIds = collect($followers)->pluck('user_from');
            $followingIds = collect($following)->pluck('user_to');
            $followersUsers = User::whereIn('id', $followersIds)->get()->keyBy('id');
            $followingUsers = User::whereIn('id', $followingIds)->get()->keyBy('id');
            $array['followers'] = [];
            $array['following'] = [];

            foreach($followers as $follower){
                $user = $followersUsers[$follower['user_from']];
                $array['followers'][] = [
                    'id' => $user['id'],
                    'name' => $user['name'],
                    'avatar' => url('media/avatars/'.$user->avatar),
                ];
            }

            foreach($following as $follower){
                $user = $followingUsers[$follower['user_to']];
                $array['following'][] = [
                    'id' => $user['id'],
                    'name' => $user['name'],
                    'avatar' => url('media/avatars/'.$user->avatar),
                ];
            }
            return response()->json($array, 200);
            
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'usuário não encontrado'], 404);
        }
    }

    public function photos(){
        
    }
}
