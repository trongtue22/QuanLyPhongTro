<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\dichvu;
use App\Models\DayTro;
use App\Models\PhongTro;
use App\Models\khachthue_phongtro;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PivotDichVu
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
            // Lấy chủ trọ id 
        $chutro_id = session('chutro_id');
    
        // Lấy tất cả các dãy trọ thuộc về chủ trọ
        $daytros = DayTro::where('chutro_id', $chutro_id)->pluck('id');
    
        // Lấy tất cả các phòng trọ thuộc các dãy trọ của chủ trọ
        $phongtros = PhongTro::whereIn('daytro_id', $daytros)->pluck('id');
    
        // Kiểm tra xem có bất kỳ phòng trọ nào trong khachthue_phongtro có dichvu_id là null không
        $missingDichVu = khachthue_phongtro::whereIn('phongtro_id', $phongtros)
            ->whereNull('dichvu_id')
            ->exists(); // Sử dụng exists() để kiểm tra xem có bản ghi nào thỏa mãn không
    
        if ($missingDichVu) {
            // Nếu có phòng trọ nào mà dichvu_id là null
            // Lấy dịch vụ từ bảng dichvu
            $dichvu = DichVu::where('chutro_id', $chutro_id)->first();
    
            if ($dichvu) { // Nếu tồn tại dịch vụ trong bảng dichvu
                // Cập nhật dichvu_id vào trong bảng khachthue_phongtro
                khachthue_phongtro::whereIn('phongtro_id', $phongtros)
                    ->whereNull('dichvu_id') // Chỉ cập nhật những bản ghi mà dichvu_id là null
                    ->update(['dichvu_id' => $dichvu->id]);
            }
        }
    
    

        return $next($request);
    }
}
