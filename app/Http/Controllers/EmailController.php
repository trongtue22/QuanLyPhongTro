<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Mail;
use App\Mail\DemoMail;
use Illuminate\Http\Request;
use App\Models\QuanLy;
use App\Models\ChuTro;
use PhpParser\Node\Expr\FuncCall;

class EmailController extends Controller
{
    public function showForm()
    {

        $chutro_id = session('chutro_id');

        $quanLy = QuanLy::with('chutro')->find($chutro_id);
        
        return view('pages.lienhe',compact('quanLy'));
    }




    
    public function sendLienHe(Request $request)
    {
        
        // Validate form
        $request->validate([
            'noidung' => 'required|string|max:1000',
        ]);
    
        // Lấy thông tin quản lý đang đăng nhập
       
        $chutro_id = session('chutro_id'); // hoặc session('id') tùy bạn lưu gì
       
        $quanLy = QuanLy::with('chutro')->find($chutro_id);
        
        
        $chuTro = $quanLy->chutro;
        
        // Gửi email
        Mail::raw("Quản lý {$quanLy->ho_ten} gửi liên hệ:\n\n" . $request->noidung, function ($message) use ($chuTro) 
        {
            $message->to($chuTro->email)
                    ->subject('Liên hệ từ quản lý');
        });
        
        flash()->option('position', 'top-center')->timeout(2000)->success('Đã gửi email liên hệ đến chủ trọ thành công!');

        return redirect()->back();
    }
    
    public function toggleAutoEmail(Request $request)
    {
        
        $chutro = ChuTro::find(session('chutro_id'));

        // Nếu checkbox được tick, request sẽ có giá trị 'on'
        $chutro->send_monthly = $request->has('auto_email');
        $chutro->save();

        return back()->with('status', 'Cập nhật trạng thái gửi email thống kê thành công.');
    }


    public function view()
    {
        $chutro_id = session('chutro_id'); // Chủ trọ hiện tại

        // Lấy danh sách các quản lý liên quan đến chủ trọ này
        $quanLys = QuanLy::where('chutro_id', session('chutro_id'))->get();

       
        return view('pages.lienheChuTro', compact('quanLys'));
    }


    public function sendEmail(Request $request)
    {
        $request->validate([
            'quanly_id' => 'required|exists:quan_ly,id',
            'noidung' => 'required|string|max:1000',
        ], [
            'quanly_id.required' => 'Vui lòng chọn quản lý để liên hệ.',
            'quanly_id.exists' => 'Quản lý không tồn tại.',
            'noidung.required' => 'Vui lòng nhập nội dung liên hệ.',
            'noidung.max' => 'Nội dung không được vượt quá 1000 ký tự.',
        ]);
        // Lấy thông tin quản lý từ ID
        $quanly = QuanLy::findOrFail($request->quanly_id);

        Mail::raw($request->noidung, function ($mail) use ($quanly) {
            $mail->to($quanly->email)
                 ->subject('Liên hệ từ chủ trọ');
        });

        flash()->option('position', 'top-center')->timeout(2000)->success('Đã gửi email liên hệ đến chủ trọ thành công!');

        return redirect()->back()->withInput($request->only('quanly_id'));
    }












    //
    public function sendWelcomeEmail()
    {
       
        $title = 'Welcome to the laracoding.com example email';
        $body = 'Thank you for participating!';
        
        // Hàm này sẽ gọi tới chức năng thực hiện gửi Email 
        // to(): người các đoạn tin kia tối người nhận có tên là email gì 
        Mail::to('trongtue22@gmail.com')->send(new DemoMail($title, $body));
        //   dd(1);
        dd("Email sent successfully!");
    }
}
