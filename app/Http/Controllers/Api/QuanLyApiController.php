<?php

namespace App\Http\Controllers\Api;
use App\Models\QuanLy;
use App\Models\hoadon;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class QuanLyApiController extends Controller
{
    //
    public function view()
    {
        if (auth()->guard('quanly')->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Không có chức năng quản lý cho vài trò này!',
            ], 403); // Mã HTTP 403: Forbidden
        }
        
        $chutro_id = auth()->guard('chutro')->user()->id; // Lấy `chutro_id` từ người dùng đã đăng nhập
        
        // Lấy danh sách quản lý thuộc chủ trọ hiện tại và phân trang
        $quanlys = QuanLy::where('chutro_id', $chutro_id)->paginate(5);
    
        // Kiểm tra nếu có dữ liệu, trả về JSON với dữ liệu quản lý
        if ($quanlys->items()) {
            return response()->json([
                'success' => true,
                'data' => $quanlys->items(),           // Danh sách quản lý của trang hiện tại
                'current_page' => $quanlys->currentPage(),
                'last_page' => $quanlys->lastPage(),
                'total' => $quanlys->total(),
                'per_page' => $quanlys->perPage(),      // Số mục mỗi trang
                'next_page_url' => $quanlys->nextPageUrl(),
                'prev_page_url' => $quanlys->previousPageUrl(),
            ]);
        } else {
            // Trả về JSON khi không có dữ liệu
            return response()->json([
                'success' => false,
                'message' => 'Không có dữ liệu quản lý nào',
            ]);
        }
    }


    public function check()
    {
        if (Auth::guard('quanly')->check()) 
        {   
            $quanly = Auth::guard('quanly')->user();

                // Trả về JSON chứa thông tin người dùng và token
                return response()->json([
                    'success' => true,
                    'content' => $quanly,
                    'message' => 'Đăng nhập thành công',
                   
                ]);
        }
            
            return response()->json([
                'success' => false,
                'message' => 'Đăng nhập thất bại',
               
            ]);
    }


    public function quanly()
    {
        return response()->json([
            'success' => true,
            'role' => 'quanly',
            'message' => 'Login form for Quanly role displayed successfully'
        ]);
    }

    public function add(Request $request)
    {
        // Kiểm tra người dùng đang đăng nhập với guard 'chutro'
        if (!auth()->guard('chutro')->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền thực hiện hành động này.',
            ], 403); // Mã HTTP 403: Forbidden
        }

       
        // Xác thực yêu cầu
        $validator = Validator::make($request->all(), 
        [
            'ho_ten' => 'required|string|max:255|unique:quanly',
            'cccd' => 'required|max:25|unique:quanly',
            'sodienthoai' => 'required|numeric|digits_between:5,12|unique:quanly',
        ], [
            'sodienthoai.numeric' => 'Số điện thoại phải là số.',
            'ho_ten.max' => 'Tên quản lý không được quá 255 ký tự.',
            'cccd.max' => 'CCCD không được quá 25 ký tự.',
            'cccd.unique' => 'CCCD này đã được đăng ký. Vui lòng chọn CCCD khác.',
            'sodienthoai.unique' => 'Số điện thoại đã được sử dụng. Vui lòng chọn số khác.',
            'sodienthoai.digits_between' => 'Số điện thoại phải từ 5 đến 12 chữ số.',
        ]);

      

        if ($validator->fails()) 
        {
            // Trả về lỗi validation dưới dạng JSON
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ.',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Lấy chutro_id từ người dùng đăng nhập
        $chutro_id = auth()->guard('chutro')->user()->id;

        // Chuẩn bị dữ liệu để lưu
        $data = $request->only(['ho_ten', 'cccd', 'sodienthoai']);
        $data['chutro_id'] = $chutro_id;
        $data['mat_khau'] = bcrypt($request->password);

        


        // Tạo quản lý mới
        // $quanly = QuanLy::create($data);

        $quanly = QuanLy::create([
            'chutro_id'   => $chutro_id, // Gán trực tiếp từ guard hoặc giá trị khác
            'ho_ten'      => $request->ho_ten,
            'sodienthoai' => $request->sodienthoai,
            'gioitinh'    => $request->gioitinh,
            'cccd'        => $request->cccd,
            'mat_khau'    => bcrypt($request->password), // Mã hóa mật khẩu trực tiếp
        ]);
            
        // ]);

        // return response()->json([
        //     'success' => "ok",
        //     // 'message' => 'Bạn không có quyền thực hiện hành động này.',
        // ], 200); // Mã HTTP 403: Forbidden 

        // Trả về JSON thành công
        return response()->json([
            'success' => true,
            'message' => 'Đã thêm quản lý thành công!',
            'data' => [
                'id' => $quanly->id, // Đưa id lên đầu
                'ho_ten' => $quanly->ho_ten,
                'sodienthoai' => $quanly->sodienthoai,
                'gioitinh' => $quanly->gioitinh,
                'cccd' => $quanly->cccd,
                'created_at' => $quanly->created_at,
                'updated_at' => $quanly->updated_at,
            ],
        ]);
    }

    public function delete($id)
    {
   
        // Tìm hóa đơn hoặc trả lỗi nếu không tìm thấy
        $quanlys = quanly::where('id', $id);
        
        // Xóa hóa đơn
        $quanlys->delete();

        // Trả về phản hồi JSON khi xóa thành công
        return response()->json([
            'success' => true,
            'message' => 'Đã xóa quản lý thành công!',
        ], 200);
    
    }


    public function update(Request $request, $id)
    {
   
        // Lấy chutro_id từ guard
        $chutro_id = auth()->guard('chutro')->user()->id;

        // Tìm đối tượng `QuanLy` theo ID và kiểm tra xem có thuộc về chủ trọ hiện tại không
        $quanly = QuanLy::where('chutro_id', $chutro_id)->findOrFail($id);

        // Xác thực dữ liệu từ yêu cầu
        $validator = Validator::make($request->all(), [
            'ho_ten' => 'required|string|max:255',
            'gioitinh' => 'required|in:0,1', // 0: Nam, 1: Nữ
            'cccd' => 'required|string|max:20|unique:quanly,cccd,' . $id, // CCCD phải là duy nhất, ngoại trừ chính nó
            'sodienthoai' => 'required|numeric|digits_between:5,12|unique:quanly,sodienthoai,' . $id, // Số điện thoại phải là duy nhất, ngoại trừ chính nó
        ], [
            'sodienthoai.numeric' => 'Số điện thoại phải là số.',
            'ho_ten.max' => 'Tên không được quá 255 ký tự.',
            'cccd.max' => 'CCCD không được quá 20 ký tự.',
            'cccd.unique' => 'CCCD này đã được đăng ký. Vui lòng chọn CCCD khác.',
            'sodienthoai.unique' => 'Số điện thoại này đã được sử dụng. Vui lòng chọn số khác.',
            'sodienthoai.digits_between' => 'Số điện thoại phải từ 5 đến 12 chữ số.',
        ]);

        if ($validator->fails()) 
        {
            // Trả về lỗi validation dưới dạng JSON
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ.',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Cập nhật các trường trong bảng `QuanLy`
        $quanly->update([
            'ho_ten' => $request->ho_ten,
            'gioitinh' => $request->gioitinh,
            'sodienthoai' => $request->sodienthoai,
            'cccd' => $request->cccd,
        ]);

        // Trả về phản hồi JSON thành công
        return response()->json([
            'success' => true,
            'message' => 'Đã cập nhật quản lý thành công!',
            'data' => $quanly,
        ], 200);
    }


    public function search(Request $request)
    {
  
        // Lấy ID của chủ trọ từ guard (authenticates 'chutro' user)
        $chutro_id = auth()->guard('chutro')->user()->id;

        // Lấy giá trị tìm kiếm từ yêu cầu
        $searchValue = $request->input('query');

        // Tìm kiếm trong bảng `quanly` dựa trên các trường `ho_ten`, `gioitinh`, `cccd`, và `sodienthoai`
        $quanlys = QuanLy::where('chutro_id', $chutro_id)
                         ->where(function ($query) use ($searchValue) {
                             $query->where('ho_ten', 'LIKE', "%{$searchValue}%")
                                   ->orWhere('gioitinh', 'LIKE', "%{$searchValue}%")
                                   ->orWhere('cccd', 'LIKE', "%{$searchValue}%")
                                   ->orWhere('sodienthoai', 'LIKE', "%{$searchValue}%");
                         })
                         ->paginate(5);  // Phân trang
        
                        

        // Trả về kết quả dưới dạng JSON
        return response()->json([
            'success' => true,
            'message' => 'Kết quả tìm kiếm thành công!',
            'data' => $quanlys,
        ], 200);
   
    }


}
