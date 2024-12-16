<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpParser\Node\Expr\FuncCall;
use App\Models\daytro;
use App\Models\PhongTro;
use App\Models\KhachThue;
use App\Models\hopdong;
use App\Models\hoadon;
use App\Models\QuanLy;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashBoardController extends Controller
{
    // Thống kê theo chủ trọ 
    public function view(Request $request)
    {   
        // Thống kê dãy trọ 
        $chutro_id = session('chutro_id'); // Biến đa hình vừa là quản lý vừa có thể là chủ trọ 

        $condition = session()->has('user_type') ? 'quanly_id' : 'chutro_id';

        $daytroCount = DayTro::where($condition, $chutro_id)->count();

        // Thống kê phòng trọ thuộc các dãy trọ
        $daytros = DayTro::where($condition, $chutro_id)->get();
        
        $phongtroCount = PhongTro::whereIn('daytro_id', $daytros->pluck('id'))->with('daytro')->count();

        // Thống kê khách thuê có trong tất các phòng trọ
        if(session()->has('user_type')) 
        {
            $quanly = quanly::where('id', $chutro_id)->first();
            $chutro_ID = $quanly->chutro_id; // Lấy ra chutro_id dựa trên id của quanly 
            $khachthueCount = KhachThue::where('chutro_id', $chutro_ID)->count();
        }else
        {
            $khachthueCount = KhachThue::where('chutro_id', $chutro_id)->count();
        }
        
        // Thống kê hợp đồng (phương pháp truy xuất xa)
        $hopdongCount = HopDong::whereHas('khachthue_phongtro.phongtro.daytro', function ($query) use ($condition, $chutro_id) 
        {
            $query->where($condition, $chutro_id);
        })->count();

        // Thống kế hóa đơn 
        $hoadonCount = Hoadon::whereHas('hopdong.khachthue_phongtro.phongtro.daytro', function ($query) use ($condition, $chutro_id) {
            $query->where($condition, $chutro_id);
        })->count();


       
        $hoadonSum = Hoadon::whereHas('hopdong.khachthue_phongtro.phongtro.daytro', function ($query) use ($condition, $chutro_id) {
            $query->where($condition, $chutro_id);
        })
        ->where('status', 1) // Thêm điều kiện lọc theo status = 1 cua HoaDon (nen phai boc ben ngoai do ap dung t/c do cho Model HoaDon)
        ->sum('tongtien');

        // Thống kê dashboard 
        
        // Lấy năm hiện tại hoặc năm từ request => User chọn    
        $selectedYear = $request->input('year', now()->year);
        // dd($selectedYear);
        
        // Dãy năm cho user chọn
        // $years = range(Carbon::now()->year, Carbon::now()->year + 4);
        
         // Các năm hiển thị trong danh sách cần dc chọn
         $years = range(2024, Carbon::now()->year);
        
       

        $hoadonByMonth = DB::table('hoadon')
        ->select(DB::raw('MONTH(created_at) as month, SUM(tongtien) as total_income'))
        ->where('status', 1) // Chỉ lấy những hóa đơn có status = 1
        ->whereYear('created_at', $selectedYear) // Lọc theo năm hiện tại
        ->groupBy(DB::raw('MONTH(created_at)'))
        ->pluck('total_income', 'month');

        // Danh sách các năm và dữ liệu thu nhập cho từng năm
        // $years = $hoadonByYear->keys();
        $months = collect(range(1, 12)); // Tạo danh sách từ tháng 1 đến tháng 12

        // Tạo dữ liệu thu nhập tương ứng với các năm (nếu không có dữ liệu cho năm đó thì giá trị sẽ là 0)
        // $incomeDataByYear = $years->map(function ($year) use ($hoadonByYear) {
        //     return $hoadonByYear->get($year, 0);
        // })->values();

        $incomeDataByMonth = $months->map(function ($month) use ($hoadonByMonth) 
        {
            return $hoadonByMonth->get($month, 0); // Lấy tổng thu nhập hoặc mặc định là 0 nếu ko có data theo năm/tháng đó 
        })->values();

        // Nếu có request trả ra ajax thì bắt lấy và trả về ngược data về cho ajax ở view update lại
        if ($request->ajax()) 
        {
            return response()->json([
                'incomeDataByMonth' => $incomeDataByMonth,
                'selectedYear' => $selectedYear // Trả về năm mới đã chọn từ user cho view, để update cho năm hiện tại ở view  
            ]);
        }
    
        
        return view('pages.dashboard', compact(
            'daytroCount',
            'phongtroCount',
            'khachthueCount',
            'hopdongCount',
            'hoadonCount',
            'hoadonSum',
            'months',
            'years',
            'incomeDataByMonth',
            'selectedYear'
        ));   
    }

   


}
