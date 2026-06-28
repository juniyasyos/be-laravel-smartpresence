<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Http\Requests\StoreAuthRequest;


class AuthController extends Controller
{
    /**
     * User Login
     *
     * Autentikasi pengguna menggunakan NIP dan password untuk mendapatkan Bearer Token.
     * 
     * @unauthenticated
     */
    public function login(StoreAuthRequest $request)
    {
        $request->validated();
        
        if (!Auth::attempt($request->only('nip', 'password'))) {
            return response()->json([
                'message' => 'NIP atau password salah'
            ], 401);
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user->status !== 'active') {
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
    
    /**
     * User Logout
     *
     * Menghapus (revoke) token yang saat ini digunakan, sehingga sesi pengguna berakhir.
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()?->delete();

        return response()->json([
            'message' => 'Logout berhasil'
        ]);
    }
}