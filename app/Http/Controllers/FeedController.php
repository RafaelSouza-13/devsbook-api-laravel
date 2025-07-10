<?php

namespace App\Http\Controllers;

use App\Http\Requests\FeedRequest;
use App\Models\Post;
use App\Models\PostComment;
use App\Models\PostLike;
use App\Models\User;
use App\Models\UserRelation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Laravel\Facades\Image;

class FeedController extends Controller
{
    public function index(){
        $clients = Post::paginate(2);
        $perPage = 2;
        $userList = UserRelation::where('user_from', auth()->user()->id)->get();
        // Lista de usurios que segue incluindo o proprio usuario
        $usersId = [];
        foreach($userList as $userItem){
            $usersId[] = $userItem['user_to'];
        }
        $usersId[] = auth()->user()->id;
        // Pegar os posts ordenado por data
        $postList = Post::whereIn('user_id', $usersId)
            ->orderBy('created_at', 'desc')->paginate(2);
        $posts = $this->postListMoreInformations($postList, auth()->user()->id);
        return response()->json($posts, 200);
    }

    private function postListMoreInformations($postList, $currentUserId){
        $postList->getCollection()->transform(function ($postItem) use ($currentUserId) {
            $postItem->mine = $postItem->user_id == $currentUserId;

            // preencher informações do usuário
            $userInfo = User::find($postItem->user_id);
            $userInfo->avatar = url('media/avatars/' . $userInfo->avatar);
            $userInfo->cover = url('media/covers/' . $userInfo->cover);
            $postItem->user = $userInfo;

            // likes
            $postItem->likeCount = PostLike::where('post_id', $postItem->id)->count();
            $postItem->liked = PostLike::where('post_id', $postItem->id)
                                ->where('user_id', $currentUserId)
                                ->exists();

            // comentários
            $comments = PostComment::where('post_id', $postItem->id)->get();
            foreach ($comments as $comment) {
                $user = User::find($comment->user_id);
                $user->avatar = url('media/avatars/' . $user->avatar);
                $user->cover = url('media/covers/' . $user->cover);
                $comment->user = $user;
            }
            $postItem->comments = $comments;

            return $postItem;
        });

        return $postList;
    }


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

    public function userFeed($id = null){
        if ($id === null) {
            $id = auth()->id();
        }
        $postList = Post::where('user_id', $id)
            ->orderBy('created_at', 'desc')->paginate(2);
        $posts = $this->postListMoreInformations($postList, $id);
        return response()->json($posts, 200);
    }
}
