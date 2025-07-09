<?php

namespace App\Http\Controllers;

use App\Http\Requests\FeedRequest;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Laravel\Facades\Image;

class FeedController extends Controller
{
    public function create(FeedRequest $request){
        $data = $request->validated();
        $post = new Post();
        $post->user_id = auth()->user()->id;
        if(isset($data['body'])){
            $post->type = $data['type'];
            $body = $data['body'];
        }elseif($request->file('photo')){
            $image = $request->file('photo');
            $post->type = $data['type'];
            $filename = md5(time().rand(0, 9999)).'.jpg';
            $destPath = public_path('/media/uploads');
            $img = Image::read($image->path())->resize(800, null, function($constrant){
                $constrant->aspectRatio();
            })->save($destPath.'/'.$filename);
            $body = $filename;
        }
        $post->created_at = date('Y-m-d H:i:s');
        $post->body = $body;
        $post->save();
        return response()->json([
            'success' => true,
            'message' => 'Post inserido com sucesso.',
            'id_post' => $post->id,
        ], 200);
    }
}
