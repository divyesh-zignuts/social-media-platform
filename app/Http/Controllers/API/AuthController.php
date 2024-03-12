<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use App\Notifications\WelcomeNotification;

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

        if ($user->status === 'A') {
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
        } else {
            // Return error
            return error(__('strings.inactive'), [], 'loginCase');
        }


        // Return error response for invalid credentials or user not found
        return error(__('strings.invalid_credentials'), [], 'loginCase');
    }

    public function register(Request $request)
    {
        $this->validate($request, [
            'first_name'        => 'required|string',
            'last_name'         => 'required|string',
            'email'             => 'required|email|unique:users',
            'password'          => 'required|string|min:6',
            'phone'             => 'required|numeric',
            'profile_image_url' => 'nullable|string',
        ]);

        $user = User::create([
            'first_name'    => $request->first_name,
            'last_name'     => $request->last_name,
            'role'          => config('constant.USER_ROLE.user'),
            'email'         => $request->email,
            'password'      => Hash::make($request->password),
            'phone'         => $request->phone,
        ]);

        // Sending a welcome email to the newly registered user
        Notification::send($user, new WelcomeNotification($user));

        // Return success message
        return ok(__('strings.success', ['name' => 'Registration']), ['user' => $user]);
    }

    public function logout()
    {
        Auth::user()->tokens()->delete();
        return ok(__('strings.success', ['name' => 'Logout']));
    }
}
