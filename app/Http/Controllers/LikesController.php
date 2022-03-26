<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LikesController extends Controller
{
    public function likeOrUnlike($id)
    {
        $post = Post::find($id);

        if (!$post) {
            return response([
                'message'=>'Post not founc.'
            ],403);
        }

        $like = $post->likes()->where('user_id', auth()->user()->id)->first();

        if (!$like) {
            Like::create([
                'post_id' => $id,
                'user_id' => auth()->user()->id
            ]);
        }

        return response([
            'message' =>'liked',
            'post'=>$post
        ],200);

    }
}
