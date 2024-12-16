<?php

namespace App\Http\Controllers\Api;
use App\Models\daytro;
use App\Models\phongtro;
use App\Models\khachthue;
use App\Models\hopdong;
use App\Models\hoadon;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardApiController extends Controller
{
    //

    public function view()
    {   
    
        // Lấy ID của chủ trọ từ session hoặc guard
        $chutro_id = auth()->guard('chutro')->user()->id;  // Hoặc dùng auth()->guard('chutro')->user()->id nếu dùng guard.

        // Thống kê dãy trọ
        $daytroCount = DayTro::where('chutro_id', $chutro_id)->count();

        // Thống kê phòng trọ thuộc các dãy trọ
        $daytros = DayTro::where('chutro_id', $chutro_id)->get();
        $phongtroCount = PhongTro::whereIn('daytro_id', $daytros->pluck('id'))->with('daytro')->count();

        // Thống kê khách thuê có trong tất các phòng trọ
        $khachthueCount = KhachThue::where('chutro_id', $chutro_id)->count();

        // Thống kê hợp đồng
        $hopdongCount = HopDong::whereHas('khachthue_phongtro.phongtro.daytro', function ($query) use ($chutro_id) {
            $query->where('chutro_id', $chutro_id);
        })->count();

        // Thông kê hóa đơn
        $hoadonCount = Hoadon::whereHas('hopdong.khachthue_phongtro.phongtro.daytro', function ($query) use ($chutro_id) {
            $query->where('daytro.chutro_id', $chutro_id);
        })->count();

        $hoadonSum = Hoadon::whereHas('hopdong.khachthue_phongtro.phongtro.daytro', function ($query) use ($chutro_id) {
            $query->where('daytro.chutro_id', $chutro_id);
        })
        ->where('status', 1) // Lọc theo status = 1
        ->sum('tongtien');

        $currentYear  = Carbon::now()->year;

        $years = collect(range($currentYear, $currentYear + 4));

        // Truy vấn tổng thu nhập theo tháng trong năm hiện tại
        $hoadonByYear = DB::table('hoadon')
            ->select(DB::raw('YEAR(created_at) as year, SUM(tongtien) as total_income'))
            ->where('status', 1) // Lọc theo status = 1
            ->groupBy(DB::raw('YEAR(created_at)'))
            ->pluck('total_income', 'year');   

        // Tạo dữ liệu thu nhập theo từng năm (nếu không có dữ liệu cho năm đó thì giá trị sẽ là 0)
        $incomeDataByYear = $years->map(function ($year) use ($hoadonByYear) {
            return $hoadonByYear->get($year, 0);
        })->values();

        // Trả về dữ liệu dưới dạng JSON
        return response()->json([
            'success' => true,
            'message' => 'Thông tin thống kê!',
            'data' => [
                'daytroCount' => $daytroCount,
                'phongtroCount' => $phongtroCount,
                'khachthueCount' => $khachthueCount,
                'hopdongCount' => $hopdongCount,
                'hoadonCount' => $hoadonCount,
                'hoadonSum' => $hoadonSum,
                'years' => $years,
                'incomeDataByYear' => $incomeDataByYear,
            ]
        ], 200);

    }

    
}
