<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Redis;

class PostsController extends Controller
{
    public function index()
    {
        try {
            $posts = Post::orderBy('created_at', 'desc')
                ->with('user:id,name,image')
                ->withCount('comments', 'likes')
                ->with('likes', function ($like) {
                    return $like->where('user_id', auth()->user()->id)->select('id', 'user_id', 'post_id')->get();
                })
                ->get();


            return response()->json(['posts' => $posts]);
        } catch (\Throwable $throwable) {
            return $throwable->getMessage();
        }
    }

    // get single post from
    public function show($id)
    { 
        return response([
            'post' => Post::where('id', $id)->withCount('comments', 'likes')->get(),
        ], 200);
    }

    public function store(Request $request)
    {
        try {
            $attrs = $request->validate([
                'body' => 'required|string',
            ]);

            $image = $this->saveImage($request->image, 'posts');

            $post = Post::create([
                'body' => $attrs['body'],
                'user_id' => auth()->user()->id,
                'image' => $image,
            ]);

            $get_post = json_decode(Post::find($post->id));

            return response([
                'message' => 'Post created successfully.',
                'post' => $get_post,
            ], 200);
        } catch (\Throwable $throwable) {
            return  response()->json($throwable->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $post = Post::find($id);

        if (!$post) {

            return response([
                'message' => 'post not found',
            ], 403);
        }
        if ($post->user_id !== auth()->user()->id) {
            return response([
                'message' => 'Permission Denied.',
            ], 403);
        }

        $attrs = $request->validate([
            'body' => 'required|string',
        ]);

        $post->body = $attrs['body'];
        $post->update();

        return response([
            'message' => 'post updated.',
            'post' => $post,
        ], 200);
    }

    public function destroy($id)
    {
        $post = Post::find($id);

        if (!$post) {
            return response([
                'message' => 'Post not founc.',
            ], 403);
        }

        if ($post->user_id !== auth()->user()->id) {
            return response(['message' => 'Permission denied.'], 403);
        }

        $post->comments()->delete();
        $post->likes()->delete();
        $post->delete();

        return response([
            'message' => 'post deleted.',
        ], 200);
    }
}
