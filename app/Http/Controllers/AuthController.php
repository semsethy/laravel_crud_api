<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthController extends Controller
{
    // Register a new user
    public function register(Request $request)
    {
        $data = $request->validate([
            "name" => "required|string",
            "email" => "required|email|unique:users,email",
            "password" => "required",
        ]);

        // Hash the password before storing it
        $data['password'] = bcrypt($data['password']);

        User::create($data);

        return response()->json([
            "status" => true,
            "message" => "User registered successfully"
        ]);
    }

    // Login and issue a JWT token
    public function login(Request $request)
    {
        $request->validate([
            "email" => "required|email",
            "password" => "required"
        ]);

        // Check if the credentials are valid
        if (!$token = JWTAuth::attempt($request->only("email", "password"))) {
            return response()->json([
                "status" => false,
                "message" => "Invalid Credentials"
            ], 401);
        }

        // Return the JWT token
        return response()->json([
            "status" => true,
            "message" => "User Logged In",
            "token" => $token
        ]);
    }

    // Get the authenticated user's profile
    public function profile()
    {
        $user = JWTAuth::parseToken()->authenticate();

        return response()->json([
            "status" => true,
            "message" => "User profile data",
            "user" => $user
        ]);
    }

    // Logout and invalidate the token
    public function logout()
    {
        JWTAuth::invalidate(JWTAuth::getToken());

        return response()->json([
            "status" => true,
            "message" => "User logged out successfully"
        ]);
    }
}
