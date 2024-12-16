<?php

namespace App\Http\Controllers;

use App\Models\ChuTro;
use App\Models\quanly;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;


class AuthController extends Controller
{
    // Xử lý Login và Register

    
    // Hiện thị View Register
    public function showRegisterForm()
    {
        // Hiện thị thông báo logout thành công 
        return view('Auth.register');
    }

    // Đăng ký tài khoản Register
    public function register(Request $request) 
    {    
        // Thông báo bằng tiếng việt
        $customMessages = [
            'ho_ten.unique' => 'Họ tên đã được sử dụng.',
            'required' => 'Vui lòng nhập :attribute.',
            'email' => 'Địa chỉ email không hợp lệ.',
            'unique' => 'Địa chỉ email đã được sử dụng.',
            'min' => 'Mật khẩu phải có ít nhất :min ký tự.',
            'confirmPassword.same' => 'Mật khẩu và mật khẩu nhập lại phải trùng nhau',
            'hinh_anh.mimetypes' => 'Chỉ nhận định dạng ảnh: jpeg, png'
        ];
        
        // Validate dữ liệu đầu vào (so sách với bên trong database)
        $request->validate([
            'ho_ten' => 'required|unique:chutro',
            'email' => 'required|email|unique:chutro,email',
            'mat_khau' => 'required|min:6',
            'confirmPassword' => 'required|same:mat_khau',
            // Cận thận với việc Validate ảnh => Vì nó phải dùng [...]
            'hinh_anh' =>  ['nullable', 'mimetypes:image/jpeg,image/png'],
        ], $customMessages);

        
        // Đưa các data vào Database
        $chutro = ChuTro::create([
            'ho_ten' => $request->ho_ten,
            'email' => $request->email,
             // Băm mật khẩu trước khi lưu
            'mat_khau' => bcrypt($request->mat_khau), 
        ]);

        // Xử lí file ảnh được chọn
         if ($request->hasFile('hinh_anh')) 
         {  
            // Lưu vào Folder public/images  
            $file = $request->file('hinh_anh');
            $fileName = $file->getClientOriginalName();
            $file->move(public_path('images'), $fileName);
            $chutro->hinh_anh = 'images/' . $fileName;
        }


        // Lưu các thông tin vào trong Database
        $chutro->save();

        

        // Chuyển hướng đến trang login thông qua route
        return redirect()->route('auth.showLoginForm');
    }    


    // Hiện thị View Login
    public function showLoginForm()
    {
        session()->forget('user_type');

       return view('Auth.login');
    }


    // Phần xử lý Login vô Home
    public function login(Request $request)  
    {
        // Lấy ra các data truyền vào bởi User
        $email = $request->input('email');
        $password = $request->input('password');

        // Lấy trong DB ra Email để kiểm tra coi Email user có khớp không => Lấy ra user
        $user = ChuTro::where('email', $email)->first();

        // Kiểm tra mật khẩu của user coi có khớp không 
        if ($user && Hash::check($password, $user->mat_khau)) 
        {            
            // Chuyển hướng đến trang chủ nếu đăng nhập thành công => Khởi tạo các session để check middleware
            session(['chutro_id' => $user->id]);
            session(['chutro_name' => $user->ho_ten]);
            session(['imageUrl' =>  $user->hinh_anh]); 
               
            return redirect()->route('daytro');
        }

        // Nếu Login sai thì trở lại trang login với thông báo lỗi 
        return redirect()->back()->withErrors(['error' => 'Email hoặc mật khẩu không đúng.']);

    }

    public function logout()
    {
       // Xoá cùng lúc hai biến session 'chutro_id' và 'chutro_name'
       session()->forget(['chutro_id', 'chutro_name', 'imageUrl']);
       
       session(['logout' => 'true']);
       
       // Lưu thông báo vào session thủ công
       session(['success' => 'Đã đăng xuất thành công!']);
       
       if(session()->has('user_type'))
       {
          return redirect()->route('quanly.login');
       }
       // Chuyển hướng người dùng về trang đăng nhập sau khi Logout 
       return redirect()->route('auth.showLoginForm');

    }


    public function quanly()
    {
        session(['user_type' => 'quanly']);

        return view('Auth.login');
    }

    public function quanlyLogin(Request $request)
    {
      
        // Lấy ra các data truyền vào bởi User
        $phone = $request->input('phone');
        $password = $request->input('password');

        // Lấy trong DB ra Email để kiểm tra coi Email user có khớp không => Lấy ra user
        $quanly = quanly::where('sodienthoai', $phone)->first();

        // Kiểm tra mật khẩu của user coi có khớp không 
        if ($quanly && Hash::check($password, $quanly->mat_khau)) 
        {            
            // Chuyển hướng đến trang chủ nếu đăng nhập thành công => Khởi tạo các session để check middleware
            session(['chutro_id' => $quanly->id]);
            session(['chutro_name' => $quanly->ho_ten]);
           //  session(['imageUrl' =>  $user->hinh_anh]); 
               
            // dd($user->hinh_anh);
            return redirect()->route('daytro');
        }

        // Nếu Login sai thì trở lại trang login với thông báo lỗi 
        return redirect()->back()->withErrors(['error' => 'Số điện thoại hoặc mật khẩu không đúng.']);
    }



    
}
