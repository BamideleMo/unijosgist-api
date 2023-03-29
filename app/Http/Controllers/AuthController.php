<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\StoreUserRequest;
use App\Models\User;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    use HttpResponses;

    public function login(LoginUserRequest $request)
    {
        $request->validated($request->all());

        if (!Auth::attempt($request->only(['phone', 'password']))) 
        {
            return $this->error('', 'Phone Number NOT subscribed. Please close this then subscribe.', 401);
        }

        $user = User::where('phone', $request->phone)->first();

        return $this->success(
            [
                'user' => $user,
                'token' => $user->createToken('Api Token of ' . $user->name)->plainTextToken
            ]
        );
    }

    public function generate_uuser_id()
    {
        $generated_id = Str::random(9);

        if (User::where('uuser_id', $generated_id)->first()) {
            $this->generate_uuser_id();
        } else {
            return $generated_id;
        }
    }

    public function register(StoreUserRequest $request)
    {
        $request->validated($request->all());

        if (User::where('phone', $request->phone)->first()) {
            return $this->error('duplicate', "Phone Number already subscribed. Please close this then Sign In.", 409);
        }

        $uuser_id = $this->generate_uuser_id();

        $user = User::create([
            'uuser_id' => $uuser_id,
            'name' => $request->name,
            'phone' => $request->phone,
            'role' => "subscriber",
            'password' => Hash::make($request->password),
        ]);

        return $this->success(
            [
                'user' => $user,
                'token' => $user->createToken('API Token of ' . $user->name)->plainTextToken
            ]
        );
    }

    public function logout()
    {
        Auth::user()->currentAccessToken()->delete();
        return $this->success([
            'message' => 'You are now logged out'
        ]);
    }
}
