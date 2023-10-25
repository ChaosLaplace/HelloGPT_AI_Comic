<?php

namespace App\Http\Middleware;

use Closure;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Illuminate\Support\Facades\Auth;
// 注意，我們要繼承的是 jwt 的 BaseMiddleware
class RefreshToken extends BaseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     *
     * @throws \Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // 檢查此次請求中是否帶有 token，如果沒有則拋出異常。
        $this->checkForToken($request);
        // 使用 try 包裹，以捕捉 token 過期所拋出的 TokenExpiredException 異常
        try {
            // 偵測使用者的登入狀態，如果正常則透過
            if ( $this->auth->parseToken()->authenticate() ) {
                return $next($request);
            }
            throw new UnauthorizedHttpException( 'jwt-auth', '未登入' );
        } catch ( TokenExpiredException $exception ) {
            // 這裡捕獲了 token 過期所拋出的 TokenExpiredException 異常，我們在這裡需要做的是刷新該用戶的 token 並將它添加到響應頭中
            try {
                // 刷新用戶的 token
                $token = $this->auth->parseToken()->refresh();
                // 使用一次性登入以保證此請求的成功
                Auth::guard('api')->onceUsingId($this->auth->manager()->getPayloadFactory()->buildClaimsCollection()->toPlainArray()['sub']);
            } catch ( JWTException $exception ) {
                // 如果捕獲到此異常，即代表 refresh 也過期了，用戶無法刷新令牌，需要重新登入。
                throw new UnauthorizedHttpException( 'jwt-auth', $exception->getMessage() );
            }
        }
        // 在回應頭中返回新的 token
        return $this->setAuthenticationHeader( $next($request), $token );
    }
}
