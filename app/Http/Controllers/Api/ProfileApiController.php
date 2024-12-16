<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ChuTro;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ProfileApiController extends Controller
{
    //
    public function view()
    {
        if (auth()->guard('quanly')->check()) 
        {
            return response()->json([
                'success' => false,
                'message' => 'Không có chức năng cho vài trò này!',
            ], 403); // Mã HTTP 403: Forbidden
        }

        // Lấy chủ trọ ID từ guard thay vì session
        $chutro_id = auth()->guard('chutro')->user()->id;

        // Tìm chủ trọ theo ID
        $chutro = ChuTro::find($chutro_id);
      
        // Trả về thông tin chủ trọ dưới dạng JSON
        return response()->json([
            'success' => true,
            'message' => 'Lấy thông tin chủ trọ thành công!',
            'data' => $chutro,
        ], 200);
    }

    public function update(Request $request, $id)
    {
       

        // Tìm chủ trọ theo ID
        $chutro = ChuTro::findOrFail($id);
    
        // Kiểm tra và cập nhật tên
        if ($request->has('ho_ten')) {
            $chutro->ho_ten = $request->input('ho_ten');
        }

    
        // Kiểm tra và cập nhật email
        if ($request->has('email')) {
            $chutro->email = $request->input('email');
        }
        
      
        // Cập nhật mật khẩu nếu có
        $old_password = $request->input('old_password');
        $new_password = $request->input('new_password');
        $confirm_password = $request->input('confirm_password');
    
        if ($old_password) {
            if (Hash::check($old_password, $chutro->mat_khau)) {
                if ($new_password == $confirm_password) {
                    $chutro->mat_khau = bcrypt($new_password);
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Mật khẩu xác nhận không trùng khớp.'
                    ], 400);
                }
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Mật khẩu nhập không khớp. Vui lòng nhập lại.'
                ], 400);
            }
        }
    
        // Xử lý ảnh đại diện
        if ($request->hasFile('avatar')) {
            // Xóa ảnh cũ nếu có
            if ($chutro->hinh_anh && file_exists(public_path($chutro->hinh_anh))) {
                unlink(public_path($chutro->hinh_anh));
            }
    
            // Lưu ảnh mới
            $file = $request->file('avatar');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('images'), $fileName);
    
            // Cập nhật đường dẫn ảnh mới
            $chutro->hinh_anh = 'images/' . $fileName;
        }
    
        // Lưu thông tin cập nhật
        $chutro->save();

      
        // Trả về thông báo thành công
        return response()->json([
            'success' => true,
            'message' => 'Thông tin đã được cập nhật thành công!',
            'data' => $chutro
        ]);
    }
    


}
