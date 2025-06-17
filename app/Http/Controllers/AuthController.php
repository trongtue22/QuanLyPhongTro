<?php

namespace App\Http\Controllers;

use App\Models\ChuTro;
use App\Models\quanly;
use App\Models\KhachThue;   
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

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
        // Bước 1: Gọi API kiểm tra email tồn tại
        $email = $request->input('email');
        $apiUrl = "https://emailaddressvalidation.com/api/p/email/{$email}";
        
        // Gọi API bằng Laravel HTTP Client
        $response = Http::get($apiUrl);
        $data = $response->json();
        
        // Kiểm tra nếu API không xác nhận được email (verifiedEmail === false)
        if (!($data['email']['verifiedEmail'] ?? false)) {
            // Nếu email không hợp lệ, trả về lỗi tùy chỉnh
            return back()->withErrors(['email' => 'Email không tồn tại hoặc không thể xác minh.'])->withInput();
        }
        
        // Bước 2: Validate dữ liệu đầu vào
        $customMessages = [
            'ho_ten.unique' => 'Họ tên đã được sử dụng. Vui lòng dùng tên khác',
            'required' => 'Vui lòng nhập :attribute.',
            'email' => 'Địa chỉ email không hợp lệ.',
            'unique' => 'Địa chỉ email đã được sử dụng.',
            'min' => 'Mật khẩu phải có ít nhất :min ký tự.',
            'confirmPassword.same' => 'Mật khẩu và mật khẩu nhập lại phải trùng nhau.',
            'hinh_anh.mimetypes' => 'Chỉ nhận định dạng ảnh: jpeg, png.',
        ];

        $validatedData = $request->validate([
            'ho_ten'        => 'required|unique:chutro',
            'email'         => 'required|email|unique:chutro,email',
            'cccd'          => 'required|unique:chutro,cccd',
            'sodienthoai'   => 'required',
            'mat_khau'      => 'required|min:6',
            'confirmPassword' => 'required|same:mat_khau',
            'hinh_anh'      =>  ['nullable', 'mimetypes:image/jpeg,image/png'],
        ], $customMessages);

        // Bước 3: Tạo chủ trọ vào database
        // Lưu ý: mật khẩu cần được băm trước khi lưu
        $chutro = ChuTro::create([
            'ho_ten'        => $validatedData['ho_ten'],
            'email'         => $validatedData['email'],
            'cccd'          => $validatedData['cccd'],
            'sodienthoai'   => $validatedData['sodienthoai'],
            'mat_khau'      => bcrypt($validatedData['mat_khau']),
        ]);

        // Xử lý file ảnh nếu có
        if ($request->hasFile('hinh_anh')) {  
            $file = $request->file('hinh_anh');
            $fileName = $file->getClientOriginalName();
            $file->move(public_path('images'), $fileName);
            $chutro->hinh_anh = 'images/' . $fileName;
        }

        $chutro->save();

        // Chuyển hướng đến trang đăng nhập (login) sau khi đăng ký thành công
        return redirect()->route('auth.showLoginForm')->with('success', 'Đăng ký thành công! Vui lòng đăng nhập.');
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
        $email = $request->input('email');
        $password = $request->input('password');

        // Lấy trong DB ra Email để kiểm tra coi Email user có khớp không => Lấy ra user
        $quanly = quanly::where('email', $email)->first();

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

    public function ViewLogin()
    {
        session()->forget('user_type');

        return view('Customer.login');
    }



    public function khachthueLogin(Request $request)
    {
        
        $request->validate([
            'phone' => 'required',
            'cccd' => 'required'
        ]);

        $khachThue = KhachThue::where('sodienthoai', $request->phone)
            ->where('cccd', $request->cccd)
            ->first();

        if ($khachThue) 
        {
            // Login thành công – bạn có thể lưu thông tin vào session
            session(['khachthue_id' => $khachThue->id]);
            session(['khachthue_name' => $khachThue->tenkhachthue]);
            session(['chutro_id ' => $khachThue->chutro_id]); 
            return redirect()->route('khachthue.dashboard'); // ví dụ route sau khi login
        } else 
        {
            return redirect()->back()->withErrors(['error' => 'Số điện thoại hoặc CCCD không đúng.']);
        }
    }

    






    public function khachthueLogout()
    {   
      
        // Xoá cùng lúc hai biến session
        session()->forget(['khachthue_id', 'khachthue_name', 'chutro_id']);

        // Redirect về trang login
        // return view('Customer.login');
        return redirect()->route('khachthue.ViewLogin');

    }

}
