<?php

namespace App\Http\Controllers;

use App\Models\quanly;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class QuanLyController extends Controller
{
    // Hiện thị quản lý 
    public function view()
    {
        $chutro_id = session('chutro_id');
        
        $quanlys = quanly::where('chutro_id', $chutro_id)->paginate(5);
        
        return view('pages.quanly',compact('quanlys'));

    }


    public function add(Request $request)
    {
        $request->validate([
            'ho_ten' => 'required|string|max:255|unique:quanly',
            'email'        => 'required|email|max:255|unique:quanly',
            'cccd'    => 'required|max:25|unique:quanly',
           'sodienthoai' => 'required|numeric|digits_between:5,12|unique:quanly',
        ],[
            'email.required' => 'Vui lòng nhập email.',
            'email.email' => 'Email không hợp lệ.',
            'email.unique' => 'Email đã được sử dụng.',
            'sodienthoai.numeric' => 'Số điện thoại phải là số.',
            'ho_ten.max' => 'Tên khách thuê không được quá 25 ký tự.',
            'cccd.max' => 'CCCD không được quá 25 ký tự.',
            'cccd.unique' => 'CCCD này đã được đăng kí. Vui lòng chọn CCCD khác',
            'sodienthoai.unique' => 'Số điện thoại đã được sài. Vui lòng chọn số khác',
            'tenkhachthue.max' => 'Tên khách thuê không được quá 25 ký tự.',
            'sodienthoai.digits_between' => 'Số điện thoại phải từ 5 đến 12 chữ số.',
        ]);
        
        // Lấy chutro_id từ session
        $chutro_id = session('chutro_id');
        $data = $request->all();
        $data['chutro_id'] = $chutro_id;
        $data['mat_khau'] = bcrypt($request->password);
        $quanly = quanly::create($data);
        $quanly->save();

        flash()->option('position', 'top-center')->timeout(1000)->success('Đã thêm quản lý thành công');
  
        return redirect()->back();
    }


    public function delete($id)
    {
        $quanlys = quanly::where('id', $id);
        
        $quanlys->delete();

        flash()->option('position', 'top-center')->timeout(1000)->success('Xóa quản lý thành công!');

        return redirect()->back();

    }

    public function search(Request $request)
    {   
         // Lấy ID của chủ trọ từ session để đảm bảo chỉ tìm kiếm trong dữ liệu của chủ trọ đó
         $chutro_id = session('chutro_id');
         
         // Lấy giá trị tìm kiếm từ yêu cầu
         $searchValue = $request->input('query');
         
         // Tìm kiếm trong bảng `quanly` dựa trên các trường `ho_ten`, `gioitinh`, và `cccd`
         $quanlys = QuanLy::where('chutro_id', $chutro_id)
                     ->where(function ($query) use ($searchValue) {
                         $query->where('ho_ten', 'LIKE', "%{$searchValue}%")
                               ->orWhere('gioitinh', 'LIKE', "%{$searchValue}%")
                               ->orWhere('cccd', 'LIKE', "%{$searchValue}%")
                               ->orWhere('sodienthoai', 'LIKE', "%{$searchValue}%");
                     })
                     ->paginate(5);  // Phân trang
         
         // Trả về view `quanly` cùng với các kết quả tìm kiếm
         return view('pages.quanly', compact('quanlys'));   
    }


    public function update(Request $request, $id)
    {
        // Lấy chutro_id từ session
        $chutro_id = session('chutro_id');

        // Tìm quản lý thuộc về chủ trọ hiện tại
        $quanly = QuanLy::where('chutro_id', $chutro_id)->findOrFail($id);

        // Xác thực dữ liệu
        $request->validate([
            'ho_ten'       => 'required|string|max:255',
            'email'        => 'required|email:rfc,dns|max:255|unique:quanly,email,' . $id,
            'cccd'         => 'required|string|max:25|unique:quanly,cccd,' . $id,
            'sodienthoai'  => 'required|numeric|digits_between:5,12|unique:quanly,sodienthoai,' . $id,
            // Không bắt buộc password, nhưng nếu có thì validate thêm
            'password'     => 'nullable|string|min:6|max:255',
        ],[
            'email.required'        => 'Vui lòng nhập email.',
            'email.email'           => 'Email không hợp lệ.',
            'email.unique'          => 'Email đã được sử dụng.',
            'sodienthoai.numeric'   => 'Số điện thoại phải là số.',
            'sodienthoai.digits_between' => 'Số điện thoại phải từ 5 đến 12 chữ số.',
            'sodienthoai.unique'    => 'Số điện thoại đã được sử dụng.',
            'ho_ten.max'            => 'Tên quản lý không được quá 255 ký tự.',
            'cccd.max'              => 'CCCD không được quá 25 ký tự.',
            'cccd.unique'           => 'CCCD đã được đăng ký.',
            'password.min'          => 'Mật khẩu phải ít nhất 6 ký tự.',
            'password.max'          => 'Mật khẩu không được quá 255 ký tự.',
        ]);

        // Cập nhật các thông tin chung
        $quanly->ho_ten      = $request->ho_ten;
        $quanly->email       = $request->email;
        $quanly->gioitinh    = $request->gioitinh;
        $quanly->sodienthoai = $request->sodienthoai;
        $quanly->cccd        = $request->cccd;

        // Nếu người dùng nhập mật khẩu mới, thì băm lại và lưu
        if ($request->filled('password')) {
            $quanly->mat_khau = bcrypt($request->password);
        }

        $quanly->save();

        flash()->option('position', 'top-center')->timeout(2000)->success('Đã cập nhật quản lý thành công!');
        return redirect()->back();
    }

    public function profile()
    {
        $chutro_id = session('chutro_id'); // Đây là quản lý id
        $quanly = QuanLy::where('chutro_id', $chutro_id)->findOrFail($chutro_id);

        return view('pages.quanlyProfile', compact('quanly'));
        
    }


    public function updateProfile(Request $request, $id)
    {
        $quanly = QuanLy::findOrFail($id);

        $customMessages = [
            'email.required' => 'Vui lòng nhập địa chỉ email.',
            'email.email' => 'Địa chỉ email không hợp lệ.',
            'email.regex' => 'Email không được chứa khoảng trắng.',
            'email.unique' => 'Email đã được sử dụng bởi một quản lý khác.',
            'sodienthoai.required' => 'Vui lòng nhập số điện thoại.',
            'sodienthoai.numeric' => 'Số điện thoại phải là số.',
            'sodienthoai.digits_between' => 'Số điện thoại phải từ 5 đến 12 chữ số.',
            'sodienthoai.unique' => 'Số điện thoại đã tồn tại.',
            'cccd.required' => 'Vui lòng nhập CCCD.',
            'cccd.string' => 'CCCD phải là chuỗi ký tự.',
            'cccd.max' => 'CCCD không được vượt quá :max ký tự.',
            'cccd.unique' => 'CCCD đã tồn tại.',
            'old_password.min' => 'Mật khẩu cũ phải có ít nhất :min ký tự.',
            'new_password.min' => 'Mật khẩu mới phải có ít nhất :min ký tự.',
            'new_password.confirmed' => 'Mật khẩu xác nhận không trùng khớp.',
        ];

        // Validate the request data
        $request->validate([
            'email' => ['required', 'email', 'max:255', 'unique:quanly,email,' . $id, 'regex:/^\S+$/u'],
            'sodienthoai' => 'required|numeric|digits_between:5,12|unique:quanly,sodienthoai,' . $id,
            'cccd' => 'required|string|max:20|unique:quanly,cccd,' . $id,
            'old_password' => 'nullable|string|min:6',
            'new_password' => 'nullable|string|min:6|confirmed',
        ], $customMessages);

        // Update basic information (excluding ho_ten)
        $quanly->email = $request->input('email');
        $quanly->sodienthoai = $request->input('sodienthoai');
        $quanly->cccd = $request->input('cccd');

        // Handle password update if new_password is provided
        if (!empty($request->input('new_password'))) {
            $old_password = $request->input('old_password');
            $new_password = $request->input('new_password');

            // 1. Check if old_password was provided
            if (empty($old_password)) {
                throw ValidationException::withMessages([
                    'old_password' => 'Vui lòng nhập mật khẩu cũ để thay đổi mật khẩu.',
                ]);
            }

            // 2. Verify the old password using the 'mat_khau' column
            if (!Hash::check($old_password, $quanly->mat_khau)) { // Changed $quanly->password to $quanly->mat_khau
                throw ValidationException::withMessages([
                    'old_password' => 'Mật khẩu cũ không đúng. Vui lòng nhập lại.',
                ]);
            }

            // If old password is correct and new password is confirmed by validation
            $quanly->mat_khau = Hash::make($new_password); // Changed $quanly->password to $quanly->mat_khau
        }

        // Save the updated manager information
        $quanly->save();

        // Update relevant session data after successful save
        session([
            'chutro_name' => $quanly->ho_ten,
        ]);

        // Flash a success message
        flash()->option('position', 'top-center')->timeout(1000)->success('Thông tin được cập nhật thành công!');
        
        return redirect()->back();
    }
   


}


