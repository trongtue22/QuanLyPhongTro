<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\dichvu;
use App\Models\quanly;
use App\Models\DayTro;
use App\Models\KhachThue_PhongTro;
use App\Models\PhongTro;

class DichVuApiController extends Controller
{
    //
    public function view()
    {

        $chutro_id = auth()->guard('quanly')->check()
            ? auth()->guard('quanly')->id()
            : auth()->guard('chutro')->id();
    
        $dichvu = DichVu::where('chutro_id', $chutro_id)->first();
    
        // Nếu trong table dichvu không tồn tại Data gì hết
        if (!$dichvu) {
            // Thiết lập các chỉ số dịch vụ = 0 => Đưa object này vào DB
            $dichvu = DichVu::create([
                'dien' => 0,
                'nuoc' => 0,
                'wifi' => 0,
                'guixe' => 0,
                'rac' => 0,
                'chutro_id' => $chutro_id,
            ]);
        }
    
        return response()->json([
            'success' => true,
            'data' => $dichvu,
        ]);
    }

    public function update(Request $request, $id)
    {
        $chutro_id = auth()->guard('quanly')->check()
            ? auth()->guard('quanly')->id()
            : auth()->guard('chutro')->id();

        $dichvu = DichVu::findOrFail($id);

        $message = [
            'required' => 'Không được bỏ trống',
            'numeric' => 'Chỉ được chứa số',
            'min' => 'Không được chứa số âm',
        ];

        $data = $request->validate([
            'dien' => 'required|numeric|min:0',
            'nuoc' => 'required|numeric|min:0',
            'wifi' => 'required|numeric|min:0',
            'guixe' => 'required|numeric|min:0',
            'rac' => 'required|numeric|min:0',
        ], $message);

        // Cập nhật dữ liệu dịch vụ
        $dichvu->update($data);

        // Lấy tất cả các dãy trọ của chủ trọ
        $daytros = DayTro::where('chutro_id', $chutro_id)->pluck('id');

        // Lấy tất cả các phòng trọ thuộc các dãy trọ
        $phongtros = PhongTro::whereIn('daytro_id', $daytros)->pluck('id');

        // Cập nhật `dichvu_id` trong bảng pivot
        KhachThue_PhongTro::whereIn('phongtro_id', $phongtros)
            ->update(['dichvu_id' => $dichvu->id]);

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật dịch vụ thành công!',
            'data' => $dichvu,
        ]);
    }

    
}   
