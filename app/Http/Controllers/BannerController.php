<?php

namespace App\Http\Controllers;

use App\Http\Requests\BannerRequest;
use App\Http\Requests\UpdateBannerRequest;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Gate;

class BannerController extends Controller implements HasMiddleware
{
    public static function middleware()
    {
        return [new Middleware('auth:sanctum', except: ['index', 'show'])];
    }
    public function index()
    {
        try {
            $banners = Banner::with(['posts.user'])->latest()->get();
            
            return response()->json([
                'success' => true,
                'data' => $banners,
                'message' => 'Banners retrieved successfully'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve banners',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    

    /**
     * Store a newly created resource in storage.
     */
   
public function store(BannerRequest $request)
{
    $fields = $request->validated();
    $fields['user_id'] = $request->user()->id;

    $banner = $request->user()->banners()->create($fields);

    return [
        "success" => true,
        'data' => $banner->load('posts')
    ];
}
    /**
     * Display the specified resource.
     */
    public function show(Banner $banner)
    {
        try {
            $banner->load([
                'posts' => function ($query) {
                    $query->with('user')
                         ->latest(); 
                },
              
            ]);
    
            return response()->json([
                'success' => true,
                'data' => $banner,
                'message' => 'Banner retrieved successfully'
            ], 200);
    
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve banner',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBannerRequest $request, Banner $banner)
    {

        Gate::authorize('modify', $banner);
        $fields = $request->validated();



        $banner->update($fields);
        return ["success" => true, "data" => $banner];
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Banner $banner)
    {

        Gate::authorize('modify', $banner);
        $banner->delete();
        return ["success" => true, "message" => "Banner deleted"];
    }
}