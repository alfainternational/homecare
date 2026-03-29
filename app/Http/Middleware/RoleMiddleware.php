<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle(Request $request,Closure $next,string ...$roles){
        if(!Auth::check()||!in_array(Auth::user()->role,$roles)){
            abort(403,'غير مصرح لك بالوصول إلى هذه الصفحة.');
        }
        return $next($request);
    }
}
