<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    // Register a new user
    public function register(RegisterRequest $request)
    {

        $user = User::create([
            'first_name' => $request->first_name,
            'second_name' => $request->second_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'picture' => $fileName ?? null,
        ]);

        if ($request->hasFile('picture')) {
            $user->uploadPicture($request->file('picture'));
        }

        return $this->response(UserResource::make($user), 'User registered successfully', 201);
    }

    // Login a user
    public function login(LoginRequest $request)
    {

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Invalid credentials.'],
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        $data = [
            'user' => UserResource::make($user),
            'token' => $token,
            'token_type' => 'Bearer',
        ];

        return $this->response($data, 'User logged in successfully');
    }

    // Forgot password (Send reset link)
    public function forgotPassword(ForgotPasswordRequest $request)
    {
        $otp = rand(100000, 999999);

        $user = User::where('email', $request->email)->first();

        $user->update([
            'otp' => $otp,
            'otp_expires_at' => Carbon::now()->addMinutes(10)
        ]);

        Mail::raw("Your password reset OTP is: $otp. It expires in 10 minutes.", function ($message) use ($user) {
            $message->to($user->email)
                ->subject('Password Reset OTP');
        });

        return $this->response(message: 'Password reset link sent on your email id.');
    }

    // Reset password
    public function resetPassword(ResetPasswordRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        if ((int) $user->otp !== $request->otp || Carbon::now()->greaterThan($user->otp_expires_at)) {
            return $this->response(message: 'Invalid or expired OTP.', status: 400);
        }

        // Reset password
        $user->update([
            'password' => Hash::make($request->password),
            'otp' => null,
            'otp_expires_at' => null,
        ]);

        return $this->response(message: 'Password reset successfully');
    }

    // Logout and revoke all tokens
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return $this->response(message: 'User logged out successfully');
    }

    // Delete user account
    public function deleteAccount(Request $request)
    {
        $user = User::findOrFail(Auth::id());
        $user->delete();
        return $this->response(message: 'User account deleted successfully');
    }
}
