<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\ChuTro;
class CheckLogin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */

    // Kiểm tra khi truy cập vào URL của các route khác có login hay chưa trách hacker truy cập mà chưa login bằng URL
    public function handle(Request $request, Closure $next): Response
    {
       

        // dd(session('chutro_name'));
        // Kiểm tra xem thông tin trong session có tồn tại không
        if (!session('chutro_id') || !session('chutro_name')) 
        {
            // Nếu không tồn tại, chuyển hướng đến trang đăng nhập
            return redirect()->route('auth.showLoginForm')->withErrors(['message' => 'Vui lòng đăng nhập trước!']);
        }

        return $next($request);
    }
}
