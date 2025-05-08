<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Http\Resources\PostResource;
use App\Models\Post;

use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Gate;

class PostController extends Controller implements HasMiddleware
{
    public static function middleware()
    {
        return [new Middleware('auth:sanctum', except: ['index', 'show'])];
    }
    public function index()
    {
        try {
            return Post::all();
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve posts',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PostRequest $request)
    {
        try {
            // the rrequest fields are being called from th PostRequest class
            //    $fields = $request->validate([
            //         'title' => 'required|max:255',
            //         'body' => 'required',
            //         'user_id' => 'required',
            //         'banner_id' => 'required',
            //    ]);

            $fields = $request->validated();

            $post =  $request->user()->posts()->create($fields);
            return ["success" => true, new PostResource($post)];
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Post creation failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        try {
          
    
            return response()->json([
                'success' => true,
                'data' => new PostResource($post),
                'message' => 'Post retrieved successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve post',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePostRequest $request, Post $post)
    {

        try {
            Gate::authorize('modify', new PostResource($post));
            $fields = $request->validated();



            $post->update($fields);
            return ["success" => true, "data" => $post];
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Post update failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {

        try {
            Gate::authorize('modify', $post);
            $post->delete();
            return ["success" => true, "message" => "Post deleted"];
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Post delete failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
