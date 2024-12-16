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
        // dd($request->all());
        
        $chutro = ChuTro::findorfail($id);
        
        // Thông báo bằng tiếng việt
        
        $customMessages = [
            'ho_ten.unique' => 'Họ tên đã được sử dụng.',
            'required' => 'Vui lòng nhập :attribute.',
            'email' => 'Địa chỉ email không hợp lệ.',
            'unique' => 'Địa chỉ email đã được sử dụng.',
            'min' => 'Mật khẩu phải có ít nhất :min ký tự.',
            'regex' => 'Địa chỉ email không được chứa khoảng trắng.',
            // 'confirmPassword.same' => 'Mật khẩu và mật khẩu nhập lại phải trùng nhau',
            'avatar.mimetypes' => 'Chỉ nhận định dạng ảnh: jpeg, png'
        ];

        //dd(1);
        // Validate dữ liệu đầu vào
        $request->validate([
            'ho_ten' => 'required|unique:chutro,ho_ten,' . $id,
            'email' => ['required', 'email', 'unique:chutro,email,' . $id, 'regex:/^[\S]+$/u'],
            'old_password' => 'nullable|min:6',
            'new_password' => 'nullable|min:6',
            // 'confirmPassword' => 'required|same:mat_khau',
            'avatar' => ['nullable', 'mimetypes:image/jpeg,image/png'],
        ], $customMessages );
              
        // Update tên và Email 
        $chutro->ho_ten = $request->input('ho_ten');

        $chutro->email = $request->input('email');
        
        // Update mật khẩu
        $old_password = $request->input('old_password');
        $new_password = $request->input('new_password');
        $confirm_password = $request->input('confirm_password');
        
        // Update picture
        // dd(1);

        // Check coi user có nhập vào ô mật khẩu hay ko 
        if($old_password)
        {
           
            // Check coi mật khẩu user nhập có khớp trong DB hay ko 
            if ($chutro && Hash::check($old_password, $chutro->mat_khau)) 
            {
                // Nếu khớp thì check tiếp 
                if($new_password == $confirm_password)
                {
                   // Băm mật khẩu trước khi update
                   $chutro->mat_khau = bcrypt($new_password);
                }
                
                // Thông báo lỗi nhập sai mật khẩu xác nhận

                return redirect()->back()->withErrors(['confirm_password' => 'Mật khẩu xác nhận không trùng khớp.']);
            }

            // Nếu không khớp thì thông báo lỗi
            // dd(1);
            return redirect()->back()->withErrors(['old_password' => 'Mật khẩu nhập không khớp. Vui lòng nhập lại']);

        }
       
        // Xử lý ảnh
        if ($request->hasFile('avatar'))
        {
           // Xóa ảnh cũ nếu có
           if ($chutro->hinh_anh && file_exists(public_path($chutro->hinh_anh))) {
               unlink(public_path($chutro->hinh_anh));
           }
   
           // Lưu ảnh mới vào thư mục 'images'
           $file = $request->file('avatar');
        //    $fileName = time() . '_' . $file->getClientOriginalName();
           $fileName = $file->getClientOriginalName();
           $file->move(public_path('images'), $fileName);
   
           // Cập nhật đường dẫn ảnh mới
           $chutro->hinh_anh = 'images/' . $fileName;
        }


     
        $chutro->save();
        
        // Thay đổi lại biến session toàn cục để update lại profile 
        session([
            'chutro_name' => $chutro->ho_ten,
            'imageUrl' =>  $chutro->hinh_anh
        ]);


        // Đặt flash message với timeout
        flash()->option('position', 'top-center')->timeout(1000)->success('Thông tin được cập nhật thành công!');
        
        return redirect()->back();

    }

}
