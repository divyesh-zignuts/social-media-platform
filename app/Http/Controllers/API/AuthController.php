<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $this->validate($request, [
            'email'    => 'required|email|exists:users',
            'password' => 'required|min:6|string',
        ], [
            'email.required'    => 'The email is required.',
            'email.email'       => 'Please enter a valid email address.',
            'email.exists'      => 'The specified email does not exist in our records.',
        ]);
        // Extract email and password from the request
        $credential = $request->only('email', 'password');

        // Extract email and password from the request
        $user = User::where('email', $credential['email'])->first();

        // Check if the user exists and the provided password matches
        if ($user && Hash::check($credential['password'], $user->password)) {

            // Attempt authentication using the credentials
            if (Auth::attempt($credential)) {

                // Create a token for the authenticated user
                $token = $user->createToken('AuthToken')->plainTextToken;

                $user->update(['api_token' => $token]);

                // Return success response with user and token information
                return ok(__('strings.success', ['name' => 'User login']), [
                    'user'  => $user,
                    'token' => $token
                ]);
            }
        }

        // Return error response for invalid credentials or user not found
        return error(__('strings.invalid_credentials'), [], 'loginCase');
    }

    public function logout()
    {
        Auth::user()->tokens()->delete();
        return ok(__('strings.success', ['name' => 'Logout']));
    }
}
