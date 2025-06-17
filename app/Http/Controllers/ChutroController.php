<?php

namespace App\Http\Controllers;

use App\Models\ChuTro;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
class ChutroController extends Controller
{
    //


    public function view()
    {
        $chutro_id = session('chutro_id');

        $chutro = ChuTro::findorfail($chutro_id);
      
        return view('pages.chutro',compact('chutro'));
    }


    public function update(Request $request, $id)
    {
        $chutro = ChuTro::findOrFail($id);

        // Thông báo lỗi tiếng Việt
        $customMessages = [
            'ho_ten.unique' => 'Họ tên đã được sử dụng.',
            'required' => 'Vui lòng nhập :attribute.',
            'email' => 'Địa chỉ email không hợp lệ.',
            'email.regex' => 'Email không được chứa khoảng trắng.',
            'email.unique' => 'Email đã được sử dụng.',
            'min' => 'Mật khẩu phải có ít nhất :min ký tự.',
            'avatar.mimetypes' => 'Chỉ nhận định dạng ảnh: jpeg, png',
            'sodienthoai.required' => 'Vui lòng nhập số điện thoại.',
            'sodienthoai.numeric' => 'Số điện thoại phải là số.',
            'sodienthoai.digits_between' => 'Số điện thoại phải từ 5 đến 12 chữ số.',
            'sodienthoai.unique' => 'Số điện thoại đã tồn tại.',
            'cccd.required' => 'Vui lòng nhập CCCD.',
            'cccd.unique' => 'CCCD đã tồn tại.',
        ];

        // Validate dữ liệu
        $request->validate([
            'ho_ten' => 'required|unique:chutro,ho_ten,' . $id,
            'email' => ['required', 'email', 'unique:chutro,email,' . $id, 'regex:/^[\S]+$/u'],
            'sodienthoai' => 'required|numeric|digits_between:5,12|unique:chutro,sodienthoai,' . $id,
            'cccd' => 'required|string|max:20|unique:chutro,cccd,' . $id,
            'old_password' => 'nullable|min:6',
            'new_password' => 'nullable|min:6',
            'avatar' => ['nullable', 'mimetypes:image/jpeg,image/png'],
        ], $customMessages);

        // Cập nhật thông tin cơ bản
        $chutro->ho_ten = $request->input('ho_ten');
        $chutro->email = $request->input('email');
        $chutro->sodienthoai = $request->input('sodienthoai');
        $chutro->cccd = $request->input('cccd');

        // Xử lý mật khẩu nếu có
        $old_password = $request->input('old_password');
        $new_password = $request->input('new_password');
        $confirm_password = $request->input('confirm_password');

        if ($old_password) 
        {
            if ($chutro && Hash::check($old_password, $chutro->mat_khau)) 
            {
                if ($new_password == $confirm_password) {
                    $chutro->mat_khau = bcrypt($new_password);
                } else 
                {
                    return redirect()->back()->withErrors(['confirm_password' => 'Mật khẩu xác nhận không trùng khớp.']);
                }
            } else 
            {
                return redirect()->back()->withErrors(['old_password' => 'Mật khẩu nhập không khớp. Vui lòng nhập lại']);
            }
        }

        // Xử lý avatar nếu có
        if ($request->hasFile('avatar')) {
            if ($chutro->hinh_anh && file_exists(public_path($chutro->hinh_anh))) {
                unlink(public_path($chutro->hinh_anh));
            }

            $file = $request->file('avatar');
            $fileName = $file->getClientOriginalName();
            $file->move(public_path('images'), $fileName);
            $chutro->hinh_anh = 'images/' . $fileName;
        }

        $chutro->save();

        // Cập nhật lại session
        session([
            'chutro_name' => $chutro->ho_ten,
            'imageUrl' => $chutro->hinh_anh,
        ]);

        flash()->option('position', 'top-center')->timeout(1000)->success('Thông tin được cập nhật thành công!');
        return redirect()->back();
    }


    public function destroy($id)
    {
        $chutro = ChuTro::findOrFail($id);
       
        // Xóa ảnh nếu có
        if ($chutro->hinh_anh && file_exists(public_path($chutro->hinh_anh))) {
            unlink(public_path($chutro->hinh_anh));
        }

        $chutro->delete();

        session()->flush(); // Xóa session nếu đang đăng nhập

        // Trở về trang đăng nhập
        return redirect()->route('auth.showLoginForm');
    }


}
