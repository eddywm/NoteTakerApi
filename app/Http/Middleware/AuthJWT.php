<?php

namespace App\Http\Middleware;

use Closure;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;


class AuthJWT
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        try{
            if (! $user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(
                    [
                        'error' => 'User not found',
                        "meta" => [
                            "status" =>  "USER_NOT_FOUND"
                        ]
                    ], 404);
            }

        }catch(TokenExpiredException $e) {
                return response()->json([
                    'error' => 'The token has expired',
                    "meta" => [
                        "status" =>  "TOKEN_HAS_EXPIRED"
                        ]
                ], $e->getStatusCode());


        }catch(TokenInvalidException $e) {
                return response()->json(
                    [
                        'error' => 'The token is invalid',
                        "meta" => [
                            "status" =>  "INVALID_TOKEN"
                        ]
                    ]
                    , $e->getStatusCode());
        }catch (JWTException $e){
                return response()->json(
                    [
                        'error' => 'The token is required',
                        "meta" => [
                            "status" =>  "TOKEN_NOT_FOUND"
                        ]
                    ], $e->getStatusCode()
                );
            }

        return $next($request);
    }
}
