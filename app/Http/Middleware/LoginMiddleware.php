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
            /*if ($request->ajax()) {
                return response("Unauthorized.（未登录）", 401)->header("X-CSRF-TOKEN", csrf_token());
            } else {
                return redirect('/#/login');
            }*/
            //return responseToJson(1,'error','还没有登录');
            //return "</<script>window.location.href = '/#/login'</script>";
            return redirect('/#/login');
        }
    }
}
