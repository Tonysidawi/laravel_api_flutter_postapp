<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Models\Post;

use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Gate;

class PostController extends Controller implements HasMiddleware
{
    public static function middleware() {
        return [new Middleware('auth:sanctum', except: ['index', 'show'])];
    }
    public function index()
    {
        return Post::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PostRequest $request)
    {

        // the rrequest fields are being called from th PostRequest class
    //    $fields = $request->validate([
    //         'title' => 'required|max:255',
    //         'body' => 'required',
    //         'user_id' => 'required',
    //         'banner_id' => 'required',
    //    ]);

      $fields = $request->validated();

      $post =  $request->user()->posts()->create($fields);
       return ["success" => true,$post];
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        return ["success" => true,$post];
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePostRequest $request, Post $post)
    {

        Gate::authorize('modify', $post);
        $fields = $request->validated();

      

      $post -> update($fields);
       return ["success" => true,"data" => $post];
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {

        Gate::authorize('modify', $post);
        $post->delete();
        return ["success" => true, "message" => "Post deleted"];
    }
}
