<?php

namespace App\Http\Controllers\Api;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ChuTro;
use App\Models\quanly;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthApiController extends Controller
{
       //
    
    public function show()
    {
           return response()->json([
               'success' => true,
               'message' => 'Hello World'
           ], 200);
    }

    public function showRegisterForm()
    {
        // Chỉ trả về JSON response
        return response()->json([
            'success' => true,
            "role" => "chutro",
            'message' => 'Register form displayed successfully'
        ], 200);
    }
    

    public function register(Request $request) 
    {    
        // Thông báo lỗi bằng tiếng Việt
        $customMessages = [
            'ho_ten.unique' => 'Họ tên đã được sử dụng.',
            'required' => 'Vui lòng nhập :attribute.',
            'email' => 'Địa chỉ email không hợp lệ.',
            'unique' => 'Địa chỉ email đã được sử dụng.',
            'min' => 'Mật khẩu phải có ít nhất :min ký tự.',
            'confirmPassword.same' => 'Mật khẩu và mật khẩu nhập lại phải trùng nhau',
            'hinh_anh.mimetypes' => 'Chỉ nhận định dạng ảnh: jpeg, png'
        ];
        
        // Validate dữ liệu đầu vào
        $validator = Validator::make($request->all(), [
            'ho_ten' => 'required|unique:chutro',
            'email' => 'required|email|unique:chutro,email',
            'mat_khau' => 'required|min:6',
            'confirmPassword' => 'required|same:mat_khau',
            'hinh_anh' => ['nullable', 'mimetypes:image/jpeg,image/png'],
        ], $customMessages);
    
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi xác thực',
                'errors' => $validator->errors() // Hiện ra mảng chứa các lỗi 
            ], 422);
        }

        // Đưa dữ liệu vào Database
        $chutro = ChuTro::create([
            'ho_ten' => $request->ho_ten,
            'email' => $request->email,
            'mat_khau' => bcrypt($request->mat_khau), 
        ]);
    
        // Xử lí file ảnh nếu có
        if ($request->hasFile('hinh_anh')) {  
            $file = $request->file('hinh_anh');
            $fileName = time() . '_' . $file->getClientOriginalName(); // Đặt tên file ảnh duy nhất
            $file->move(public_path('images'), $fileName);
            $chutro->hinh_anh = 'images/' . $fileName;
        }
    
        // $token = auth()->login($user);
        // Lưu các thông tin vào database
        $chutro->save();
    
        // Trả về JSON thành công
        return response()->json([
            'success' => true,
            'message' => 'Đăng ký thành công',
            'data' => $chutro,  // Trả về thông tin người dùng vừa được đăng ký
            // 'token' => $token  // Trả về token
        ], 201); // HTTP status 201: Created
    }



    public function showLoginForm()
    {
        return response()->json([
            'success' => true,
            'role' => 'chutro',
            'message' => 'Login form displayed successfully'
        ], 200);
    }



    public function login(Request $request)
    {
        // Kiểm tra thông tin đăng nhập
        $credentials = [
            'email' => $request->email,
            'password' => $request->password // Phải giữ nguyên tên password vì đây là tham số của hàm attempt 
        ];
    
        // Tạo token và lấy ra các bảng nghitrong DB dựa trên thông số đầu vào 
        $token = Auth::guard('chutro')->attempt($credentials); 
     
        // Lấy thông tin của user login
        $user = Auth::guard('chutro')->user();  
          
        if(!$token) 
        {
            return response()->json([
                'status' => 401,
                'message' => 'Đăng nhập thất bại! Vui lòng nhập lại'
            ], 401);
        }
        
        // Trả về token khi đăng nhập thành công
        return response()->json([
            'success' => true,
            'message' => 'Đăng nhập thành công',
            'user' => $user,
            'access_token' => $token
        ]);
    
    }
    
    
    public function logout()
    {
        // Kiểm tra và hủy đăng nhập cho người dùng với vai trò `chutro`
        if (auth()->guard('chutro')->check()) {
            // Hủy phiên đăng nhập của người dùng `chutro` và hủy token JWT
            auth()->guard('chutro')->logout(true);
    
            return response()->json([
                'success' => true,
                'message' => 'Đăng xuất chủ trọ và hủy token thành công'
            ], 200);
        }
        
        // Kiểm tra và hủy đăng nhập cho người dùng với vai trò `quanly`
        if (auth()->guard('quanly')->check()) {
            // Hủy phiên đăng nhập của người dùng `quanly` và hủy token JWT
            auth()->guard('quanly')->logout(true);
    
            return response()->json([
                'success' => true,
                'message' => 'Đăng xuất quản lý và hủy token thành công'
            ], 200);
        }
    
        // Nếu không có người dùng nào đăng nhập, trả về lỗi
        return response()->json([
            'success' => false,
            'message' => 'Không tìm thấy người dùng đang đăng nhập'
        ], 401);
    }


    public function quanlyLogin(Request $request)
    {
        // Lấy dữ liệu đăng nhập từ request
        $credentials = [
            'sodienthoai' => $request->input('phone'),
            'password' => $request->input('password'),
        ];
    
        // Sử dụng Auth::guard('quanly')->attempt() để xác thực thông tin đăng nhập
        $token = Auth::guard('quanly')->attempt($credentials);
    
        $quanly = Auth::guard('quanly')->user();

        if (!$token) 
        {
            // Lấy thông tin người dùng đã đăng nhập từ guard('quanly')
            return response()->json([
                'success' => false,
                'status' => 401,
                'message' => 'Số điện thoại hoặc mật khẩu không đúng.'
            ], 401);
        }
    
       // Trả về JSON chứa thông tin người dùng và token
        return response()->json([
            'success' => true,
            'message' => 'Đăng nhập thành công',
            'user' => $quanly,
            'access_token' => $token
        ]);
    }
    
}



