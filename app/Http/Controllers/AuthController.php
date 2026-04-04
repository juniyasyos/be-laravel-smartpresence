<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Http\Requests\storeAuthRequest;


class AuthController extends Controller
{
    public function login(storeAuthRequest $request)
    {
        $request->validated();
        
        $user = User::where('username', $request->username)->first();

        if (!$user || $user->password !== $request->password) {
            return response()->json([
                'message' => 'Username atau password salah'
            ], 401);
        }

        if (!$user->is_active) {
            return response()->json([
                'message' => 'User tidak aktif'
            ], 403);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login berhasil',
            'token' => $token,
            'user' => $user
        ]);
    }
    
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()?->delete();

        return response()->json([
            'message' => 'Logout berhasil'
        ]);
    }
}