<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use JWTAuth;
use Exception;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;

class JwtMiddleware extends BaseMiddleware
{

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
        } catch (Exception $e) {
            if ($e instanceof TokenInvalidException){
                return response()->json([
                    'success' => false,
                    'message' => 'Token is Invalid',
                ], Response::HTTP_UNAUTHORIZED);
            }else if ($e instanceof TokenExpiredException){
                return response()->json([
                    'success' => false,
                    'message' => 'Token is Expired'
                ], Response::HTTP_UNAUTHORIZED);
            }else{
                return response()->json([
                    'success' => false,
                    'message' => 'Authorization Token not found'
                ], Response::HTTP_UNAUTHORIZED);
            }
        }
        return $next($request);
    }
}
