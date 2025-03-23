<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ChangePasswordRequest;
use App\Http\Requests\Auth\UpdateProfileRequest;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function index()
    {
        return $this->response(UserResource::make(Auth::user()));
    }

    public function update(UpdateProfileRequest $request)
    {
        $user = Auth::user();

        $user->update($request->validated());

        if ($request->hasFile('picture')) {
            $user->uploadPicture($request->file('picture'));
        }

        return $this->response(UserResource::make($user), 'Profile updated successfully');
    }

    public function changePassword(ChangePasswordRequest $request)
    {
        $user = Auth::user();

        if (!password_verify($request->current_password, $user->password)) {
            return $this->response(null, 'Current password is incorrect', 400);
        }

        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        return $this->response(null, 'Password changed successfully');
    }
}
