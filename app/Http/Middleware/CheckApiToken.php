<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
class CheckApiToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        
        // Nếu chưa có token thì thông báo lỗi 401 
        if (Auth::guard('chutro')->check()) {
           return $next($request);
        }

        // Ko đi vào đây được 
        if (Auth::guard('quanly')->check()) {
           
            return $next($request);
        }

        return response()->json(['message' => 'Vui lòng đăng nhập trước!'], 401);

        
    }
}
