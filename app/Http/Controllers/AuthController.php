<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthRequestLogin;
use App\Http\Requests\AuthRequestRegister;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Container\Attributes\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(AuthRequestRegister $request)
    {
        try {
            $fields = $request->validated();
            $user = User::create(
                $fields
            );

            $token = $user->createToken('auth_token')->plainTextToken;
            return ["success" => true, "token" => $token, "user" =>new UserResource($user)];
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Registration failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function login(AuthRequestLogin $request)
    {
        try {
            $request->validated();

            $user = User::where('email', $request->email)->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid credentials'
                ], 401); // 401 Unauthorized
            }
            $user->tokens()->delete();

            $token = $user->createToken('auth_token', ['*']);

            return response()->json([
                'success' => true,
                'access_token' => $token->plainTextToken,
                'token_type' => 'Bearer',
                'user' => new UserResource($user)
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Login failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        try {
            $request->user()->tokens()->delete();

            return ["success" => true, "message" => "Logged out"];
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Logout failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
