<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\ApiResponser;
use App\Mail\UserRegistered;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class UserApiController extends Controller
{
    use ApiResponser;

    public function __construct()
    {

    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('email', 'password');

        $token = Auth::attempt($credentials);

        if (! $token) {
            return $this->error(__('apis.login.unauthorized'), 401);
        }

        $user = Auth::user();

        if ($user->verified_user == 0) {
            return $this->error(__('apis.user.not_verified'), 200);
        }

        return $this->success(
            [
                'status' => 'success',
                'user' => $user,
                'authorisation' => [
                    'token' => $token,
                    'type' => 'bearer',
                ],
                'todo_list' => $user->todos,
            ],
            __('apis.login.success')
        );

    }

    public function register(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255',
            // 'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'password_confirmation' => 'required|same:password|min:8',
        ]);

        if ($validator->fails()) {
            return $this->error('', 401, $validator->errors());
        }

        $user = User::create([
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'verification_token' => Str::random(50),
            'verified_user' => 0,
        ]);

        Mail::to($request->email)->send(new UserRegistered($user));

        return $this->success(
            [
                'status' => 'success',
                'message' => 'User created successfully',
                'user' => $user,
            ]
        );
    }

    public function logout()
    {
        $token = JWTAuth::getToken();
        JWTAuth::invalidate($token);

        return $this->success([
            'status' => 'success',
            'message' => 'Successfully logged out',
        ]);

    }

    public function refresh()
    {
        return $this->success([
            'status' => 'success',
            'user' => Auth::user(),
            'authorisation' => [
                'token' => Auth::refresh(),
                'type' => 'bearer',
            ],
            'message' => __('apis.token.new'),
        ]);

    }
}
