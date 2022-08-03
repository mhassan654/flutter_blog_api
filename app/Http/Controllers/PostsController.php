<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class PostsController extends Controller
{
    public function index()
    {
        return response([
            'posts' =>Post::orderBy('created_at','desc')
            ->with('user:id,name,image')
            ->withCount('comments','likes')
            ->with('likes', function($like) {
                return $like->where('user_id',auth()->user()->id)->select('id','user_id','post_id')->get();
            })
            ->get()
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
        // $validator = Validator::make(request()->all(), [
        //     'invoiceId' => 'required|integer',
        // ]);

        // if ($validator->fails()) {
        //     return $this->customFailResponseMessage($validator->messages(), 200);
        // }

        $attrs = $request->validate([
            'body' => 'required|string'
        ]);

        $image = $this->saveImage($request->image, 'posts');

        $post = Post::create([
            'body' => $attrs['body'],
            'user_id'=>auth()->user()->id,
            'image' => $image
        ]);

        return response([
            'message' =>'Post created successfully.',
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
        $post->delete();

        return response([
            'message' =>'post deleted.'
        ],200);
    }
}
