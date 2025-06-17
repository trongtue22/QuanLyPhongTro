<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckChuTroLogin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!session('khachthue_id')) {
            return redirect()->route('khachthue.login')
                             ->withErrors(['message' => 'Vui lòng đăng nhập với tư cách khách thuê!']);
        }
        
        return $next($request);
    }
}
