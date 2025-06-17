<?php

namespace App\Console\Commands;
use Illuminate\Support\Facades\DB;
use Illuminate\Console\Command;
use App\Models\daytro;
use App\Models\PhongTro;
use App\Models\KhachThue;
use App\Models\hopdong;
use App\Models\hoadon;
use App\Models\QuanLy;
use App\Models\ChuTro;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
class SendMonthlySummary extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'monthly:send-summary'; // Lệch chạy hàm gửi Email thống kê cuối tháng

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Gửi email thống kê cuối tháng';

    /**
     * Execute the console command.
     */
    
     
public function handle()
{
    $chutros = ChuTro::where('send_monthly', true)->get();

    foreach ($chutros as $chutro) {
        $chutro_id = $chutro->id;

        // 1. Lấy số lượng Dãy Trọ
        $daytroCount = DayTro::where('chutro_id', $chutro_id)->count();

        // 2. Lấy các Dãy Trọ -> Phòng Trọ
        $daytros = DayTro::where('chutro_id', $chutro_id)->get();
        $phongtroCount = PhongTro::whereIn('daytro_id', $daytros->pluck('id'))->count();

        // 3. Hợp Đồng: thông qua mối quan hệ gián tiếp
        $hopdongCount = HopDong::whereHas('khachthue_phongtro.phongtro.daytro', function ($query) use ($chutro_id) {
            $query->where('chutro_id', $chutro_id);
        })->count();

        // 4. Hóa đơn và Tổng tiền
        $hoadonQuery = Hoadon::whereHas('hopdong.khachthue_phongtro.phongtro.daytro', function ($query) use ($chutro_id) {
            $query->where('chutro_id', $chutro_id);
        });

        $hoadonCount = $hoadonQuery->count();
        $hoadonSum = $hoadonQuery->where('status', 1)->sum('tongtien');

        // Form Email hiện thị ra cho ChuTro đọc
        $message = "Xin chào {$chutro->hoten},\n\n";
        $message .= "Thống kê cuối tháng:\n";
        $message .= "- Số dãy trọ: {$daytroCount}\n";
        $message .= "- Số phòng trọ: {$phongtroCount}\n";
        $message .= "- Số hợp đồng: {$hopdongCount}\n";
        $message .= "- Số hóa đơn: {$hoadonCount}\n";
        $message .= "- Tổng tiền đã thu: " . number_format($hoadonSum, 0, ',', '.') . " VND\n\n";
        $message .= "Trân trọng,\nHệ thống Quản lý Phòng Trọ";
        // 5. Gửi Email
        Mail::raw($message, function ($mail) use ($chutro) {
            $mail->to($chutro->email)
                 ->subject('Thống kê cuối tháng');
        });

       
    }

    $this->info('🎉 Đã gửi thống kê cho tất cả các chủ trọ bật tính năng gửi mail.');
}
}
