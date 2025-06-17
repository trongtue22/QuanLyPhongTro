<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HoaDon;
use App\Models\HopDong;
use Barryvdh\DomPDF\Facade\Pdf; // Thêm dòng này để sử dụng PDF
use App\Models\DayTro;
use App\Models\PhongTro;
use App\Models\KhachThue;
use App\Models\QuanLy;
use App\Models\DichVu;
use App\Models\ChuTro;
use Carbon\Carbon;
class PDFController extends Controller
{
    //
    public function exportPDF($id)
    {
        $chutro_id = session('chutro_id');

        $hoadon = HoaDon::whereHas('hopdong.khachthue_phongtro.phongtro.daytro', function ($query) {
                $query->where('chutro_id', session('chutro_id'));
            })
            ->with([
                'hopdong.khachthue_phongtro.khachthue',
                'hopdong.khachthue_phongtro.phongtro.daytro',
                'dichvus'
            ])
            ->findOrFail($id);
            
        // Lấy danh sách dịch vụ mới nhất theo từng dãy trọ
        $dichvu = DichVu::where('chutro_id', $chutro_id)
            ->orderByDesc('id')
            ->get()
            ->unique('daytro_id')
            ->keyBy('daytro_id');

        // Gắn dịch vụ tương ứng dãy trọ vào hóa đơn
        $daytroId = optional($hoadon->hopdong?->khachthue_phongtro?->phongtro?->daytro)->id;
        $hoadon->dv = $dichvu[$daytroId] ?? null;

        $pdf = PDF::loadView('modals.previewHoaDon', [
            'hoadon' => $hoadon,
            'is_pdf' => true
        ])->setPaper('A4', 'portrait');

        return $pdf->download("HoaDon_{$hoadon->id}.pdf");
    }





    public function HopDongPdf($id)
    {
        // Lấy hợp đồng từ database
        $hopdong = HopDong::whereHas('khachthue_phongtro.phongtro.daytro', function ($query) {
            $query->where('chutro_id', session('chutro_id')); // Lọc theo chủ trọ hiện tại
        })
        ->with([
            'khachthue_phongtro.khachthue',             // Lấy thông tin khách thuê
            'khachthue_phongtro.phongtro.daytro',       // Lấy thông tin phòng trọ và dãy trọ
        ])
        ->findOrFail($id);

        // Load view và truyền dữ liệu vào file Blade cần xuất PDF
        $pdf = PDF::loadView('modals.previewHopDong', [
            'hopdong' => $hopdong,
            'is_pdf' => true
        ])->setOptions([
            'defaultFont' => 'DejaVu Sans'
        ]);

        // Xuất file PDF về máy người dùng
        return $pdf->download("HopDong_{$hopdong->id}.pdf");
    }

    public function downloadPDF(Request $request)
    {
        $chutro_id = session('chutro_id');
        $condition = session()->has('user_type') ? 'quanly_id' : 'chutro_id';
        
        // Thống kê
        $daytroCount = DayTro::where($condition, $chutro_id)->count();
        
        $daytros = DayTro::where($condition, $chutro_id)->get();
        $phongtroCount = PhongTro::whereIn('daytro_id', $daytros->pluck('id'))->count();
        
        // Thống kê khách thuê
        if (session()->has('user_type')) {
            $quanly = QuanLy::findOrFail($chutro_id);
            $chutro_ID = $quanly->chutro_id;
            $khachthueCount = KhachThue::where('chutro_id', $chutro_ID)->count();
        } else {
            $khachthueCount = KhachThue::where('chutro_id', $chutro_id)->count();
        }
    
        $hopdongCount = HopDong::whereHas('khachthue_phongtro.phongtro.daytro', function ($q) use ($condition, $chutro_id) {
            $q->where($condition, $chutro_id);
        })->count();
    
        $hoadonCount = HoaDon::whereHas('hopdong.khachthue_phongtro.phongtro.daytro', function ($q) use ($condition, $chutro_id) {
            $q->where($condition, $chutro_id);
        })->count();
    
        $hoadonSum = HoaDon::whereHas('hopdong.khachthue_phongtro.phongtro.daytro', function ($q) use ($condition, $chutro_id) {
            $q->where($condition, $chutro_id);
        })->where('status', 1)->sum('tongtien');
    
        $paidCount = HoaDon::where('status', 1)
            ->whereHas('hopdong.khachthue_phongtro.phongtro.daytro', function ($q) use ($condition, $chutro_id) {
                $q->where($condition, $chutro_id);
            })->count();
        
        $unpaidCount = HoaDon::where('status', 0)
            ->whereHas('hopdong.khachthue_phongtro.phongtro.daytro', function ($q) use ($condition, $chutro_id) {
                $q->where($condition, $chutro_id);
            })->count();
        
            $months = collect(range(1, 12));
            $selectedYear = $request->input('year', now()->year);
            $hoadonByMonth = Hoadon::where('status', 1)
            ->whereYear('created_at', $selectedYear)
            ->whereHas('hopdong.khachthue_phongtro.phongtro.daytro', function ($query) use ($condition, $chutro_id) {
                $query->where($condition, $chutro_id);
            })
            ->selectRaw('MONTH(created_at) as month, SUM(tongtien) as total_income')
            ->groupByRaw('MONTH(created_at)')
            ->pluck('total_income', 'month');
        
            $incomeDataByMonth = $months->map(function ($month) use ($hoadonByMonth) {
                return $hoadonByMonth->get($month, 0);
            })->values();  
        
        $pdf = PDF::loadView('modals.pdfDashboard', [
            'daytroCount' => $daytroCount,
            'phongtroCount' => $phongtroCount,
            'khachthueCount' => $khachthueCount,
            'hopdongCount' => $hopdongCount,
            'hoadonCount' => $hoadonCount,
            'hoadonSum' => $hoadonSum,
            'paidCount' => $paidCount,
            'unpaidCount' => $unpaidCount,
            'months' => $months,
            'incomeDataByMonth' => $incomeDataByMonth,
        ])->setOptions([
            'defaultFont' => 'DejaVu Sans',
            
        ]);

    return $pdf->download('ThongKeDashboard.pdf');
}


}
