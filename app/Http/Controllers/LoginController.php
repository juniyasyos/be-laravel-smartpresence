<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAuthRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Exception;

class LoginController extends Controller
{

    public function Auth(StoreAuthRequest $request)
    {
        try{
            $result = $request->validated();
            $user = User::where('username', $result['username'])->first();
            if (!$user || !Hash::check($result['password'], $user->password)) {
                return response()->json(['message' => 'Invalid username or password'], 401);
            }
            return response()->json([
                'message' => 'Authentication successful',
                'data' => $user,
            ], 200);
        }catch(Exception $e){
            return response()->json(['message' => 'An error occurred while authenticating', 'error' => $e->getMessage()], 500);
        }
    }

    public function logout()
    {
        // Implement logout functionality if needed
    }
}