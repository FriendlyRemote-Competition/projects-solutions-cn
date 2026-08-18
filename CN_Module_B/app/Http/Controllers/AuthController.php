<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    // login
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    function login(Request $request)
    {
        $email = $request->email;
        $password = $request->password;

        if (Auth::attempt(['email' => $email, 'password' => $password])) {
            if (!Auth::user()->is_active) {
                return response()->json(['message' => 'Invalid credentials'], 401);
            }
            $user = Auth::user();
            $token = Str::random(60);
            $user->token = $token;
            $user->save();

            return response()->json([
                "data" => [
                    "token" => $token,
                    "user" => [
                        "email" => $email,
                        "name" => $user->name,
                        "role" => $user->role,
                    ]
                ]
            ]);
        } else {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }
    }
}
