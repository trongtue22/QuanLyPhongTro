<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SuCoPhongTro;
use App\Models\PhongTro;
use App\Models\daytro;
class SuCoPhongTroController extends Controller
{
    //
    public function create(Request $request, $phongtroId)
    {
        $loai_suco = $request->input('loai_suco');
        $motas = $request->input('mota');

        if (!$loai_suco || !$motas || count($loai_suco) !== count($motas)) {
            return back()->with('error', 'Thiếu dữ liệu hoặc dữ liệu không hợp lệ.');
        }

        foreach ($loai_suco as $index => $loai) 
        {
         SuCoPhongTro::create([
        'phongtro_id' => $phongtroId,
        'loai_su_co' => $loai, // Đúng tên cột
        'mo_ta' => $motas[$index],
    ]);
    }

        return redirect()->back();
    }   


    public function SuaChua()
    {
        $user_id = session('chutro_id'); // cả chủ trọ lẫn quản lý đều dùng biến này
        
        // Kiểm tra xem là quản lý hay chủ trọ
        if (session()->has('user_type')) {
            // Nếu là quản lý: lọc theo `quanly_id`
            $daytros = DayTro::where('quanly_id', $user_id)
                ->whereHas('phongtros.sucophongtro')
                ->get();
        } else {
            // Nếu là chủ trọ: lọc theo `chutro_id`
            $daytros = DayTro::where('chutro_id', $user_id)
                ->whereHas('phongtros.sucophongtro')
                ->get();
        }

        // Lấy toàn bộ sự cố phòng trọ kèm theo mối quan hệ
        $sucophongtros = SuCoPhongTro::with(['phongtro.daytro'])->latest()->get();

        return view('pages.goiy', compact('sucophongtros', 'daytros'));
    }


    

    public function tonghop(Request $request, $phongtro_id)
    {
        
         // 1. Xóa (hoàn tất)
        if ($request->has('hoantat_ids')) 
        {
            SuCoPhongTro::whereIn('id', $request->hoantat_ids)->delete();
        }

        // 2. Cập nhật
        if ($request->has('loai_suco_update')) 
        {
            foreach ($request->loai_suco_update as $id => $loai) {
                $suco = SuCoPhongTro::find($id);
            if ($suco) 
            {
                $suco->loai_su_co = $loai;
                $suco->mo_ta = $request->mota_update[$id] ?? $suco->mo_ta;
                $suco->save();
            }
        }
    }

        // 3. Thêm mới
        if ($request->has('loai_suco_moi')) 
        {
            foreach ($request->loai_suco_moi as $index => $loai) {
            SuCoPhongTro::create([
                'phongtro_id' => $phongtro_id,
                'loai_su_co' => $loai,
                'mo_ta' => $request->mota_moi[$index] ?? '',
            ]);
        }
    }

        return redirect()->back();
    }
}
