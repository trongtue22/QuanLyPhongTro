<?php

namespace App\Http\Controllers;
use App\Models\dichvu;
use App\Models\DayTro;
use App\Models\PhongTro;
use App\Models\quanly;
use App\Models\khachthue_phongtro;

use Illuminate\Http\Request;

class DichVuController extends Controller
{
    
    public function view(Request $request)
    {
        $chutro_id = session('chutro_id');
    
        $daytros = collect(); // Khởi tạo rỗng để tránh lỗi
        $dichvu = null;
        $selectedDayTroId = $request->get('daytro_id');
    
        if (session()->has('user_type')) {
            // Nếu là quản lý
            $quanly_id = $chutro_id; // Vì session('chutro_id') là id của quản lý trong trường hợp này
            $daytros = DayTro::where('quanly_id', $quanly_id)->get();
    
            // Lấy chutro_id thực sự từ quanly để lưu vào bảng dịch vụ
            $quanly = QuanLy::find($quanly_id);
            $chutro_id = $quanly?->chutro_id;
    
        } else {
            // Nếu là chủ trọ
            $daytros = DayTro::where('chutro_id', $chutro_id)->get();
        }
    
        // Nếu đã chọn 1 dãy trọ cụ thể
        if ($daytros->count() > 0 && $selectedDayTroId) {
            // Chỉ cho phép chọn những dãy trọ nằm trong danh sách được phân quyền
            if ($daytros->pluck('id')->contains($selectedDayTroId)) {
                $dichvu = DichVu::where('daytro_id', $selectedDayTroId)
                    ->orderByDesc('id')
                    ->first();
    
                // Nếu chưa có dịch vụ thì tạo mặc định
                if (!$dichvu) {
                    $dichvu = DichVu::create([
                        'dien' => 0,
                        'nuoc' => 0,
                        'wifi' => 0,
                        'guixe' => 0,
                        'rac' => 0,
                        'chutro_id' => $chutro_id,
                        'daytro_id' => $selectedDayTroId,
                    ]);
                }
            }
        }
        
        return view('pages.dichvu', compact('daytros', 'dichvu'));
    }

    public function storeNew(Request $request)
    {
        $chutro_id = session('chutro_id');
    
        $message = [
            'required' => 'không được bỏ trống',
            'numeric'  => 'Chỉ được chứa số', 
            'min'      => 'Không được chứa số âm'
        ];
    
        $data = $request->validate([
            'dien' => 'required|numeric|min:0',  
            'nuoc' => 'required|numeric|min:0',
            'wifi' => 'required|numeric|min:0',
            'guixe' => 'required|numeric|min:0',
            'rac' => 'required|numeric|min:0',
        ], $message);
        
        // 🌟 q **Tạo một bản ghi mới thay vì cập nhật**
        $dichvu = DichVu::create(array_merge($data, [
            'chutro_id' => $chutro_id,
            'daytro_id' => $request->daytro_id
        ]));
    
        // Lấy tất cả các dãy trọ của chủ trọ
        $daytros = DayTro::where('chutro_id', $chutro_id)->pluck('id');
    
        // Lấy tất cả các phòng trọ thuộc các dãy trọ của chủ trọ
        $phongtros = PhongTro::whereIn('daytro_id', $daytros)->pluck('id');
    
        // 🌟 **Cập nhật dịch vụ mới cho tất cả phòng trọ**
        khachthue_phongtro::whereIn('phongtro_id', $phongtros)
            ->update(['dichvu_id' => $dichvu->id]);
    
        flash()->option('position', 'top-center')->timeout(2000)->success('Đã thêm dịch vụ mới thành công!');
    
        return redirect()->route('DichVu.view');
    }

}
