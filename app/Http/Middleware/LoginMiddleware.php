<?php

namespace App\Http\Middleware;

use Closure;

class LoginMiddleware
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
        if(get_session_user_id()){
            return $next($request);
        } else {
            if($request->ajax()){
                return response('没有权限',401)->header('X-SCRF-TOKEN',csrf_token());
            }else {
                return redirect('/#/login');
            }
        }
    }
}
