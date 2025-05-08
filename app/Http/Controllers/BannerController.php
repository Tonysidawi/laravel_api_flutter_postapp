<?php

namespace App\Http\Controllers;

use App\Http\Requests\BannerRequest;
use App\Http\Requests\UpdateBannerRequest;
use App\Http\Resources\BannerResource;
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
                'data' => new BannerResource($banners),
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
        try {
            $fields = $request->validated();
            $fields['user_id'] = $request->user()->id;

            $banner = $request->user()->banners()->create($fields);

            return response()->json([
                'success' => true,
                'data' =>new BannerResource($banner ->load('posts')),
                'message' => 'Banner created successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Banner creation failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function show(Banner $banner)
    {
        try {
            $banner->load(['posts.user']);

            return response()->json([
                'success' => true,
                'data' => new BannerResource($banner),
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
        try {
            Gate::authorize('modify', $banner);
            $fields = $request->validated();



            $banner->update($fields);
            return ["success" => true, "data" =>new BannerResource($banner)];
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Banner update failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Banner $banner)
    {
        try {
            Gate::authorize('modify', $banner);
            $banner->delete();
            return ["success" => true, "message" => "Banner deleted"];
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Banner delete failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
