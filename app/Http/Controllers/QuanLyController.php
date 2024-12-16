<?php

namespace App\Http\Controllers;

use App\Models\quanly;
use Illuminate\Http\Request;

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
            'cccd'    => 'required|max:25|unique:quanly',
           'sodienthoai' => 'required|numeric|digits_between:5,12|unique:quanly',
        ],[
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
        // Tìm đối tượng `QuanLy` theo ID và kiểm tra xem có thuộc về chủ trọ hiện tại không
       
        $chutro_id = session('chutro_id');
        $quanly = QuanLy::where('chutro_id', $chutro_id)->findOrFail($id);
    
        // Xác thực dữ liệu từ yêu cầu
        $request->validate([
            'ho_ten' => 'required|string|max:255',
            // 'gioitinh' => 'required|string|in:Nam,Nữ', // Giới tính có thể là Nam hoặc Nữ
            'cccd' => 'required|string|max:20|unique:quanly,cccd,' . $id, // CCCD phải là duy nhất
            'sodienthoai' => 'required|numeric|digits_between:5,12|unique:quanly,sodienthoai,' . $id, // Sửa lại cú pháp unique
        ],[
            'sodienthoai.numeric' => 'Số điện thoại phải là số.',
            'ho_ten.max' => 'Tên khách thuê không được quá 25 ký tự.',
            'cccd.max' => 'CCCD không được quá 25 ký tự.',
            'cccd.unique' => 'CCCD này đã được đăng kí. Vui lòng chọn CCCD khác',
            'sodienthoai.unique' => 'Số điện thoại đã được sài. Vui lòng chọn số khác',
            'tenkhachthue.max' => 'Tên khách thuê không được quá 25 ký tự.',
            'sodienthoai.digits_between' => 'Số điện thoại phải từ 5 đến 12 chữ số.',
        ]);
    

        // Cập nhật các trường trong bảng `quanly`
        $quanly->update([
            'ho_ten' => $request->ho_ten,
            'gioitinh' => $request->gioitinh,
            'sodienthoai' => $request->sodienthoai,
            'cccd' => $request->cccd,
        ]);
        
        flash()->option('position', 'top-center')->timeout(2000)->success('Đã cập nhật quản lý thành công!');
        // Trả về thông báo thành công
        return redirect()->back();
    }

   


}


