<?php

namespace App\Http\Middleware;

use App\Http\Traits\GeneralTrait;
use Closure;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthenticateJWT
{
    use GeneralTrait;

    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle($request, Closure $next)
    {

        try {

            $user = JWTAuth::parseToken()->authenticate();

        } catch (\Exception $e) {

            if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException) {

                return $this->responseMessage(401, false, 'Invalid token');

            } elseif ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException) {

                return $this->responseMessage(401, false, 'Token expired');

            } else {

                return $this->responseMessage(401, false, 'Token not found');

            }
        }

        if (!$user) {
            return $this->responseMessage(401, false, 'Unauthorized');
        }

        return $next($request);

    }
}
