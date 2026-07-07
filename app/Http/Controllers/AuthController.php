<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Password;

class AuthController extends Controller{
public function register(Request $request){
    if (User::where('email', $request->email)->exists()) {
        return response()->json([
        'error' => 'An account with this email already exists.'
    ], 409);
    }
    $request->validate([
        'name' => ['required', 'string'],
        'email' => ['required', 'email'],
        'password' => [
            'required',
            'confirmed',
            'min:8',
        ],
    ]);
    $user = User::create([
        'name' => $request->name,
        'email' =>$request->email,
        'password'=>Hash::make($request->password),
        'role'=>'user'
        ]);
        $token = auth('api')->login($user);
    return response()->json([
        'message'=> 'Account created successfully.'
    ],201);
}

public function login(Request $request){
    $credentials = $request->only('email', 'password');
    $token = auth('api')->attempt($credentials);

    if (!$token) {
        return response()->json([
            'error' => 'Incorrect email or password.',
        ], 401);
    }
    return response()->json([
        'message' => 'Login successful.',
        'access_token'   => $token,
    ]);
}

public function me()
{
    $user = auth('api')->user();
    return response()->json([
        'success' => true,
        'user'    => $user,
    ]);
}

public function logout(){
    auth('api')->logout();
    return response()->json([
        'message' => 'User Logged out Successfully',
    ]);
}

public function refresh(){
    $token = auth('api')->refresh();

    return response()->json([
        'message' => 'Token refreshed Successfully',
        'token'   => $token,
    ]);
}

public function forgetPassword(Request $request){
    $request->validate(['email'=> ['required','email']]);
    $status = Password::sendResetLink($request->only('email'));
    
    if (!User::where('email',$request->email)->exists()){
        return response()->json([
            'error' => "No account found with this email."
        ],404);
    }

$key = 'forgot-password:' . $request->email;

    if (RateLimiter::tooManyAttempts($key, 3)) {
        return response()->json([
            'error' => 'Too many password reset attempts. Your account has been temporarily locked for security reasons. Please try again later.'], 423);
}

    RateLimiter::hit($key, 3600);

    if ($status==Password::RESET_LINK_SENT){
        return response()->json([
        'message'=> "We've sent a verification link to your email."],200);
    }
    return response()->json([
        "message"=>$status],422);
}

public function resetPassword(Request $request)
{
    $request->validate([
        'email' => ['required', 'email'],
        'token' => ['required'],
        'password' => ['required', 'confirmed', 'min:8'],
    ]);

    $status = Password::reset(
        $request->only('email', 'password', 'token', 'password_confirmation'),
        function (User $user, string $password) {
            $user->update([
                'password' => Hash::make($password)
            ]);
        }
    );

    if ($status === Password::PASSWORD_RESET) {

        $key = 'forgot-password:' . $request->email;
        RateLimiter::clear($key);

        return response()->json([
            'message' => 'Password reset successfully. Please log in with your new password.'
        ], 200);
    }

    return response()->json([
        'message' => 'Incorrect Data Entered.'
    ], 422);
}
}