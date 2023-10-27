<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthRequest;
use App\Http\Traits\GeneralTrait;

class AuthController extends Controller
{
    use GeneralTrait;

    public function login(AuthRequest $request)
    {

        $credentials = $request->only(['email', 'password']);

        if (!$token = auth('api')->attempt($credentials)) {
            return $this->responseMessage(401, false, 'Unauthorized');
        }

        $user = auth('api')->user();
        $user->access_token = $token;
        $user->token_type = 'bearer';
        $user->expires_in = auth()->factory()->getTTL() * 60;

        return $this->responseMessage(200, true, 'تم تسجيل الدخول بنجاح', $user);

    }

    public function logout()
    {
        auth()->logout();
        return $this->responseMessage(200, true, 'تم تسجيل الخروج بنجاح');
    }
}
