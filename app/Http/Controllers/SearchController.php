<?php

namespace App\Http\Controllers;

use App\Http\Requests\SearchRequest;
use App\Models\User;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function search(SearchRequest $request){
        $data = $request->validated();
        $users = User::where('name', 'LIKE', '%'.strtolower($data['txt']).'%')->get();
        $users = $users->map(function($user){
            return [ 
                'id' => $user->id,
                'name' => $user->name,
                'avatar' => url('media/avatars/'.$user->avatar),
            ];
        });
        return response()->json([
            'users' => $users], 200);
    }

}
