<?php

namespace App\Http\Middleware;
use Illuminate\Support\Facades\Auth;
use Closure;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;

class JWT extends BaseMiddleware 
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
           JWTAuth::parseToken()->authenticate(); //check if token is valid
            $response=$next($request);
            if ($request->user()) {
            $response->header('userstatus',$request->user()->status);
            }
           return  $response;
        }
        catch(\Exception $e){
            if ($e instanceof TokenExpiredException) {
                $refreshed = JWTAuth::refresh(JWTAuth::getToken());
                $user = JWTAuth::setToken($refreshed)->toUser();
                header('Authorization: Bearer ' . $refreshed);
            }else if($e instanceof TokenInvalidException){
                 return response()->json(['success'=>false,'message'=>'token invalid'],401);
            }else{
                return response()->json(['success'=>false,'message'=>'token not found'],401);
            }
            Auth::login($user, false);

            return  $next($request);
        }
       
        
    }
}
