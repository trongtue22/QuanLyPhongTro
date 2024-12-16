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
    
    public function view()
    {
        $chutro_id = session('chutro_id');

        if (session()->has('user_type')) 
        {
            $quanly = quanly::where('id', $chutro_id)->first();
            $chutro_id = $quanly->chutro_id; // Lấy ra chutro_id dựa trên id của quanly 
        }
        
        $dichvu = dichvu::where('chutro_id', $chutro_id)->first();
        
        // Nếu nó null tức là user chưa từng truy cập lần nào => Đây là lần đầu

        // Nếu trong table dichvu không tồn tại Data gì hết         
        if(!$dichvu)
        {
        
         // Thiết lập các chỉ số dịch vụ = 0 => Đưa object này vào DB 
         $dichvu = dichvu::create([
            'dien' => 0,
            'nuoc' => 0,
            'wifi' => 0,
            'guixe' => 0,
            'rac' => 0,
            'chutro_id' => $chutro_id,
            ]);
        }

      
      return view('pages.dichvu', compact('dichvu')); // Note the quotes around 'dichvu'
    }


    public function update(Request $request, $id)
    {
        
        $dichvu  = dichvu::findOrFail($id);

        $message = [
            'required' => 'không được bỏ trống',
            'numeric'  => 'Chỉ được chứa số', 
            'min'    => 'Không được chứa số âm'
        ];

        $data = $request->validate([
            'dien' => 'required|numeric|min:0',  // Must be a number, not less than 0
            'nuoc' => 'required|numeric|min:0',
            'wifi' => 'required|numeric|min:0',
            'guixe' => 'required|numeric|min:0',
            'rac' => 'required|numeric|min:0',
            // Add other fields if necessary
        ], $message);

       
        // Update data vô table riêng
        $dichvu->update($data);

        // Update data vào table pivot khớp với từng phòng trọ và khách thuê (áp dùng cho toàn bộ)
        $chutro_id = session('chutro_id');

        // Lấy tất cả các dãy trọ của chủ trọ
        $daytros = DayTro::where('chutro_id', $chutro_id)->pluck('id');

         // Lấy tất cả các phòng trọ thuộc các dãy trọ của chủ trọ
        $phongtros = PhongTro::whereIn('daytro_id', $daytros)->pluck('id');

        // Cập nhật dichvu_id cho tất cả các bản ghi trong bảng pivot khachthue_phongtro
        khachthue_phongtro::whereIn('phongtro_id', $phongtros)
        ->update(['dichvu_id' => $dichvu->id]);
 
 
        flash()->option('position', 'top-center')->timeout(2000)->success('Đã cập nhật hợp đồng thành công!');

        return redirect()->route('DichVu.view');
        
    }
}
