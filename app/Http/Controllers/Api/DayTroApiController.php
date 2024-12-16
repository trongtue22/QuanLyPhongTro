<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\daytro;
use App\Models\QuanLy;
use Illuminate\Support\Facades\Validator;

class DayTroApiController extends Controller
{
    
  public function daytro()
  {
        // Lấy dữ liệu người dùng từ token JWT dựa trên guard `chutro` hoặc `quanly`
        if (auth()->guard('chutro')->check())
        {
            $user = auth()->guard('chutro')->user();
            $user_type = 'chutro';
        } 
        elseif (auth()->guard('quanly')->check())
        {
            $user = auth()->guard('quanly')->user();
            $user_type = 'quanly';
        } 
    
        // Xác định `chutro_id` cho vai trò hiện tại
        $chutro_id = $user->id;
    
        // Lấy danh sách `daytro` dựa trên `user_type`
        if ($user_type === 'quanly') 
        {
            $daytros = DayTro::where('quanly_id', $chutro_id)->with(['quanly:id,ho_ten'])->paginate(5);
        } else 
        {
            $daytros = DayTro::where('chutro_id', $chutro_id)->with(['quanly:id,ho_ten'])->paginate(5);
        }
    
        // Chuẩn bị dữ liệu trả về
        if ($daytros->items()) {
            return response()->json([
                'success' => true,
                'data' => $daytros->items(),
                'current_page' => $daytros->currentPage(),
                'last_page' => $daytros->lastPage(),
                'total' => $daytros->total(),
                'per_page' => $daytros->perPage(),
                'next_page_url' => $daytros->nextPageUrl(),
                'prev_page_url' => $daytros->previousPageUrl(),
            ]);
        } else {
            return response()->json([
                'success' => true,
                'data' => 'Không có dữ liệu'
            ]);
        }
  }

  public function store(Request $request)
  {
    // Kiểm tra thông tin đầu vào
    $validator = Validator::make($request->all(), [
        'tendaytro' => 'required|string|max:255|unique:daytro',
        'tinh' => 'required|string|max:255',
        'huyen' => 'required|string|max:255',
        'xa' => 'required|string|max:255',
        'sonha' => 'required|string|max:255',
    ], [
        'tendaytro.unique' => 'Tên dãy trọ đã tồn tại',
    ]);

    if ($validator->fails()) 
        {
            // Trả về lỗi validation dưới dạng JSON
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ.',
                'errors' => $validator->errors(),
            ], 422); // 422 Unprocessable Entity (Lỗi dữ liệu không hợp lệ)
        }

    // Lấy id từ token JWT, dựa trên người dùng hiện tại
    $chutro_id = auth()->guard('chutro')->id(); // lấy `chutro_id` từ token thay vì session

    $daytroData = [
        'chutro_id' => $chutro_id,
        'tendaytro' => $request->tendaytro,
        'tinh' => $request->tinh,
        'huyen' => $request->huyen,
        'xa' => $request->xa,
        'sonha' => $request->sonha,
    ];

    // Kiểm tra nếu vai trò là quản lý
    if (auth()->guard('quanly')->check()) {
        // Thêm `quanly_id` vào DB
        $daytroData['quanly_id'] = $chutro_id;

        // Chuyển `chutro_id` về id của chủ trọ thực sự
        $chutro_id = QuanLy::where('id', $chutro_id)->value('chutro_id');
        $daytroData['chutro_id'] = $chutro_id;
    }

    // Tạo và lưu vào DB
    $daytro = Daytro::create($daytroData);

    // Phản hồi JSON thành công
    return response()->json([
        'success' => true,
        'message' => 'Đã thêm dãy trọ thành công',
        'data' => $daytro
    ], 201); // Mã 201 cho "đã tạo thành công"
  }
  

  public function destroy($id)
  {
    // Tìm dãy trọ cần xóa theo ID
    $daytro = DayTro::find($id);

    // Kiểm tra nếu dãy trọ tồn tại
    if (!$daytro) {
        return response()->json([
            'success' => false,
            'message' => 'Dãy trọ không tồn tại',
        ], 404);
    }

    // Xóa dãy trọ
    $daytro->delete();

    // Trả về phản hồi JSON với thông báo thành công
    return response()->json([
        'success' => true,
        'message' => 'Đã xóa dãy trọ thành công!',
    ], 200);
  }

  public function update(Request $request, $id)
  {
    // Tìm dãy trọ cần cập nhật
    $daytro = DayTro::find($id);
    
    if (!$daytro) 
    {
      // Trả về lỗi nếu ID không tồn tại
      return response()->json([
          'success' => false,
          'message' => 'Dãy trọ với ID ' . $id . ' không tồn tại.'
      ], 404);
    }

    // Validate chỉ các trường có trong request, không yêu cầu tất cả
    $validator = Validator::make($request->all(), [
        'tendaytro' => 'sometimes|required|string|max:255|unique:daytro,tendaytro,' . $id,
        'tinh' => 'sometimes|required|string|max:255',
        'huyen' => 'sometimes|required|string|max:255',
        'xa' => 'sometimes|required|string|max:255',
        'sonha' => 'sometimes|required|string|max:255',
    ], [
        'tendaytro.unique' => 'Tên dãy trọ vừa cập nhật đã tồn tại',
    ]);

    // Nếu validate thất bại, trả về lỗi
    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'errors' => $validator->errors()
        ], 422);
    }
    // Cập nhật thông tin chỉ với các trường có trong request
    $daytro->update($request->only(['tendaytro', 'tinh', 'huyen', 'xa', 'sonha']));

    // Trả về phản hồi JSON thành công
    return response()->json([
        'success' => true,
        'message' => 'Dãy trọ đã được cập nhật thành công!',
        'data' => $daytro
    ], 200);
  }

  public function search(Request $request)
  {
      // Kiểm tra người dùng từ token JWT cho cả 'chutro' và 'quanly'
      if (auth()->guard('chutro')->check()) {
          $user = auth()->guard('chutro')->user();
          $user_type = 'chutro';
      } elseif (auth()->guard('quanly')->check()) {
          $user = auth()->guard('quanly')->user();
          $user_type = 'quanly';
      } 
  
      // Lấy giá trị tìm kiếm từ request
      $searchValue = $request->input('query');
  
      // Lấy chutro_id từ người dùng hiện tại
      $chutro_id = $user->id;
      
      // Tạo truy vấn tìm kiếm cho bảng `DayTro`
      $daytrosQuery = DayTro::where(function ($query) use ($searchValue) {
          $query->where('tendaytro', 'LIKE', "%{$searchValue}%")
                ->orWhere('tinh', 'LIKE', "%{$searchValue}%")
                ->orWhere('huyen', 'LIKE', "%{$searchValue}%")
                ->orWhere('xa', 'LIKE', "%{$searchValue}%")
                ->orWhere('sonha', 'LIKE', "%{$searchValue}%");
      })->with(['quanly:id,ho_ten']);
  
      // Lọc kết quả theo `quanly_id` nếu là vai trò 'quanly', ngược lại lọc theo `chutro_id`
      if ($user_type === 'quanly') 
      {
        $daytrosQuery->where('quanly_id', $chutro_id); // Neu la quanly thi loc theo id cua quanly => Hien thi search ra cac daytro duoc quan ly phan quyen thoi
      }
      else 
      {
        $daytrosQuery->where('chutro_id', $chutro_id); // Tim theo chutro_id hien tai trong DB => Trach tim cac data cua chutro khac 
      }

      // Phân trang kết quả tìm kiếm
      $daytros = $daytrosQuery->paginate(5);
  
      // Trả về dữ liệu dưới dạng JSON với các thông tin phân trang
      if ($daytros->items()) 
      {
          return response()->json([
              'success' => true,
              'data' => $daytros->items(), // hien thi theo item ra
              'current_page' => $daytros->currentPage(),
              'last_page' => $daytros->lastPage(),
              'total' => $daytros->total(),
              'per_page' => $daytros->perPage(),
              'next_page_url' => $daytros->nextPageUrl(),
              'prev_page_url' => $daytros->previousPageUrl(),
          ]);
      } else {
          return response()->json([
              'success' => true,
              'data' => 'Không có dữ liệu'
          ]);
      }
  }

   public function phanquyen(Request $request, $id)
    {
        // Tìm DayTro bằng ID hoặc trả về lỗi 404 nếu không tìm thấy
        $dayTro = DayTro::find($id);
    
        if (!$dayTro) 
        {
            // Trả về lỗi nếu ID DayTro không tồn tại
            return response()->json([
                'success' => false,
                'message' => 'Dãy trọ với ID ' . $id . ' không tồn tại.'
            ], 404);
        }
    
        // Lấy `quanly_id` từ request
        $quanlyId = $request->input('quanly_id');
    
        // Nếu `quanly_id` không phải null, kiểm tra nó có tồn tại trong bảng `quanly`
        if (!is_null($quanlyId) && !QuanLy::where('id', $quanlyId)->exists()) 
        {
            // Trả về lỗi nếu `quanly_id` không tồn tại
            return response()->json([
                'success' => false,
                'message' => 'Quản lý với ID ' . $quanlyId . ' không tồn tại.'
            ], 400); // 400 là mã trạng thái HTTP cho "Bad Request"
        }
    
        // Cập nhật `quanly_id` từ request (có thể là null)
        $dayTro->quanly_id = $quanlyId;
        $dayTro->save();
    
        // Trả về response dạng JSON nếu thành công
        return response()->json([
            'success' => true,
            'message' => 'Đã cập nhật trạng thái thành công!',
            'data' => [
                'id' => $dayTro->id,
                'tendaytro' => $dayTro->tendaytro,
                'quanly_id' => $dayTro->quanly_id,
            ],
        ], 200); // 200 là mã trạng thái HTTP cho "OK"
    }





  






}
