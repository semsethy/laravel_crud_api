<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth; // Import Auth facade

class AuthController extends Controller
{
    public function register(Request $request){
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

    public function login(Request $request){
        $request->validate([
            "email" => "required|email",
            "password" => "required"
        ]);

        // Attempt to log the user in
        if (!Auth::attempt($request->only("email", "password"))) {
            return response()->json([
                "status" => false,
                "message" => "Invalid Credentials"
            ], 401);
        }

        // Generate API token
        $user = Auth::user();
        $token = $user->createToken("myToken")->plainTextToken;

        return response()->json([
            "status" => true,
            "message" => "User Logged In",
            "token" => $token
        ]);
    }

    public function profile(){
        // Check if the user is authenticated
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                "status" => false,
                "message" => "Unauthorized"
            ], 401);
        }

        return response()->json([
            "status" => true,
            "message" => "User profile data",
            "user" => $user
        ]);
    }

    public function logout(){
        Auth::logout();

        return response()->json([
            "status" => true,
            "message" => "User logged out successfully"
        ]);
    }
}
