<?php

namespace App\Http\Controllers;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Models\Post;
use App\Models\PostLike;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PostController extends Controller
{
    public function like($id){
        try {
            $post = Post::findOrFail($id);
            $userId = auth()->id();
            $postLikeQuery = PostLike::where('post_id', $id)
                                    ->where('user_id', $userId);
            $isLiked = $postLikeQuery->exists();

            if($isLiked){
                $postLikeQuery->delete();
                $isLiked = false;
            }else{
                $newPostLike = new PostLike();
                $newPostLike->post_id = $id;
                $newPostLike->user_id = auth()->id();
                $newPostLike->created_at = now();
                $newPostLike->save();
                $isLiked = true;
            }
            $post['isLike'] = $isLiked;
            $post['likeCount'] = PostLike::where('post_id', $id)->count();
            if($post['type'] === 'photo'){
                $post['body'] = url('media/uploads/'.$post['body']);
            }
            return response()->json($post, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'post não encontrado'], 404);
        }
    }

}
