<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class PostsController extends Controller
{
    public function index()
    {
        return response([
            'posts' =>Post::orderBy('created_at','desc')->with('title', 'user:id,name,image')->withCount('comments','likes')->get()
        ],200);
    }

    // get single post from
    public function show($id)
    {
        return response([
            'post'=>Post::where('id', $id)->withCount('comments','likes')->get()
        ],200);
    }

    public function store(Request $request)
    {
        $attrs = $request->validate([
            'body' => 'required|string'
        ]);

        $post = Post::create([
            'body' => $attrs['body'],
            'user_id'=>auth()->user()->id
        ]);

        return response([
            'message' =>'post created.',
            'post'=>$post
        ],200);
    }

    public function update(Request $request)
    {
        $post = Post::find($id);

        if (!$post) {

           return response([
               'message' => 'post not found'
           ],403);
        }

        if ($post->user_id !== auth()->user()->id) {
            return response([
                'message' => 'Permission Denied.'
            ],403);
        }

        $attrs = $request->validate([
            'body' => 'required|string'
        ]);

        $post= Post::update([
            'body' => $attrs['body']
        ]);

        return response([
            'message' =>'post updated.',
            'post'=>$post
        ],200);
    }

    public function destroy($id)
    {
        $post = Post::find($id);

        if (!$post) {
            return response([
                'message'=>'Post not founc.'
            ],403);
        }

        if ($post->user_id !== auth()->user()->id) {
             return response(['message'=>'Permission denied.'],403);
        }

        $post->comments()->delete();
        $post->likes()->delete();

        return response([
            'message' =>'post deleted.',
            'post'=>$post
        ],200);
    }
}