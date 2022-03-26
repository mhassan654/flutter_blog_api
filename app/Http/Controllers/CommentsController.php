<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Comment;
use Illuminate\Http\Request;

class CommentsController extends Controller
{
    public function index($id)
    {
        $post = Post::find($id);

        if (!$post) {

            return response([
                'message' => 'post not found'
            ],403);
         }

        return response([
            'post'=>$post->comments()->with('user:id,name,image')->get()
        ],200);
    }

    public function store(Request $request,$id)
    {
        $post = Post::find($id);

         if (!$post) {

           return response([
               'message' => 'post not found'
           ],403);
        }

        $attrs = $request->validate([
            'comment' => 'required|string'
        ]);

        $post =Comment::create([
            'comment' => $attrs['comment'],
            'post_id'=>$id,
            'user_id'=>auth()->user()->id
        ]);

        return response([
            'message' =>'comment created.'
        ],200);
    }

    public function update(Request $request,$id)
    {
        $comment = Comment::find($id);

         if (!$comment) {

           return response([
               'message' => 'comment not found'
           ],403);
        }

        if ($comment->user_id !== auth()->user()->id) {
            return response([
                'message' => 'Permission Denied.'
            ],403);
        }

        $attrs = $request->validate([
            'comment' => 'required|string'
        ]);

        $comment =Comment::update([
            'comment' => $attrs['comment']
        ]);

        return response([
            'message' =>'comment updated.'
        ],200);
    }

    public function destroy($id)
    {
        $comment = Comment::find($id);

        if (!$comment) {
            return response([
                'message'=>'Comment not founc.'
            ],403);
        }

        if ($comment->user_id !== auth()->user()->id) {
             return response(['message'=>'Permission denied.'],403);
        }

        $comment->delete();
        $post->likes()->delete();

        return response([
            'message' =>'comment deleted.',
            'post'=>$post
        ],200);
    }
}
