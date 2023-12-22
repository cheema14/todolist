<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;

class VerificationController extends Controller
{
    public function verify_token($token)
    {
        $user = User::where('verification_token', $token)->firstOrFail();
        $user->update(['verified_user' => 1, 'verification_token' => null, 'email_verified_at' => Carbon::now()]);

        if ($user) {
            return response()->json([
                'status' => 'success',
                'message' => 'You have successfully verified your email address. Please use this email to login',
            ]);
        }

        return response()->json([
            'status' => 'fail',
            'message' => 'Something went wrong! Please try again.',
        ]);
    }
}
