<?php

namespace App\Services;

use App\Models\Post;
use App\Models\PostComment;
use App\Models\PostLike;
use App\Models\User;
use App\Models\UserRelation;

class FeedService
{
    public function postListMoreInformations($postList, $currentUserId){
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
}
