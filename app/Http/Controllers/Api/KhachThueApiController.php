<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\phongtro;
use App\Models\khachthue;
use App\Models\QuanLy;
use App\Models\khachthue_phongtro;

use Illuminate\Support\Facades\Validator;

class KhachThueApiController extends Controller
{
    //
    public function show($id)
    {
        // Lấy từ phòng trọ -> khách thuê
        $phongtro = Phongtro::with('khachthues')->findOrFail($id);
    
        // Kiểm tra số lượng khách thuê trong phòng trọ
        $khachthueCount = $phongtro->khachthues->count();
        $phongtro->status = $khachthueCount > 0 ? 1 : 0;
        $phongtro->save();
    
        // Phân trang khách thuê
        $khachthues = $phongtro->khachthues()->paginate(5);
        
        $khachthuesData = $khachthues->getCollection()->map(function ($khachthue) {
            return $khachthue->makeHidden('pivot')->setAttribute('gioitinh', $khachthue->gioitinh == 0 ? 'Nam' : 'Nữ');
        });
    
        // Thay đổi tập dữ liệu của $khachthues để không chứa 'pivot'
        $khachthues->setCollection($khachthuesData);

        // Lấy khách thuê từ các phòng khác
        $khachthue_ids = $khachthues->pluck('id')->toArray();
        $khachthuekhac = KhachThue::whereNotIn('id', $khachthue_ids)->get()->map(function ($khachthue) {
            return $khachthue->setAttribute('gioitinh', $khachthue->gioitinh == 0 ? 'nam' : 'nữ');
        });
    
        // Trả về JSON đơn giản hơn
        return response()->json([
            'status' => 'success',
            'data' => [
                'sophong' => $phongtro->sophong,
                'khachthues' => 
                [
                    'data' => $khachthues->items(), // Chỉ lấy danh sách khách thuê
                    'current_page' => $khachthues->currentPage(),
                    'last_page' => $khachthues->lastPage(),
                    'total' => $khachthues->total(),
                    'per_page' => $khachthues->perPage(),
                    'next_page_url' => $khachthues->nextPageUrl(),
                    'prev_page_url' => $khachthues->previousPageUrl(),
                   
                ],
                'khachthuekhac' => $khachthuekhac
            ],
          
        ]);
    }


    public function stored(Request $request, $id)
    {
        // Kiểm tra nếu người dùng chỉ gửi CCCD
        if ($request->has('cccd') && !$request->has('tenkhachthue')) 
        {
            // Kiểm tra xem khách thuê đã tồn tại với CCCD chưa
            $khachthue = KhachThue::where('cccd', $request->cccd)->first();

            if (!$khachthue) {
                // Nếu không có khách thuê với CCCD này, yêu cầu người dùng cung cấp thông tin đầy đủ
                return response()->json([
                    'success' => false,
                    'message' => 'Khách thuê không tồn tại, vui lòng cung cấp thông tin đầy đủ.',
                    'data' => [
                        'sodienthoai' => 'required|numeric|digits_between:5,12|unique:khachthue',
                        'ngaysinh' => 'required|date',
                        'gioitinh' => 'required|string|in:nam,nu',
                        'cccd' => 'required|string|max:25|unique:khachthue'
                    ]
                ], 422);
            } else {
                // Nếu có khách thuê với CCCD này, kiểm tra xem họ đã có trong phòng chưa
                $phongTro = PhongTro::find($id); // Lấy phòng trọ theo ID

                if (!$phongTro) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Phòng trọ không tồn tại',
                    ], 404);
                }

                // Kiểm tra xem khách thuê đã có trong phòng trọ chưa
                if ($phongTro->khachthues()->where('khachthue_id', $khachthue->id)->exists()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Khách thuê này đã có trong phòng.',
                    ], 422);
                }

                // Gán khách thuê vào phòng trọ nếu chưa có
                $phongTro->khachthues()->attach($khachthue->id);

                $khachthue->gioitinh = $khachthue->gioitinh == 0 ? 'nam' : 'nu';
                
                return response()->json([
                    'success' => true,
                    'id' =>  $phongTro->id,
                    'sophong' =>  $phongTro->sophong,
                    'message' => 'Khách thuê đã được thêm vào phòng trọ thành công.',
                    'data' => $khachthue
                ]);
            }
        }
   
        // Nếu có thông tin đầy đủ, validate và tạo khách thuê mới
        $validator = Validator::make($request->all(), [
            'tenkhachthue' => 'required|string|max:255',
            'sodienthoai' => 'required|numeric|digits_between:5,12|unique:khachthue',
            'ngaysinh' => 'required|date',
            'cccd' => 'required|string|max:25|unique:khachthue',
            'gioitinh' => 'required|integer|in:0,1',
        ], [
            'tenkhachthue.required' => 'Tên khách thuê là bắt buộc.',
            'sodienthoai.unique' => 'Số điện thoại đã được sử dụng. Vui lòng chọn số khác.',
            'cccd.unique' => 'CCCD này đã được đăng ký với khách thuê khác. Vui lòng chọn CCCD khác.',
            'ngaysinh.required' => 'Ngày sinh chưa có thông tin',
            'gioitinh.required' => 'Giới tính chưa có thông tin'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ.',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Lấy `chutro_id` từ token JWT dựa trên người dùng hiện tại
        $chutro_id = auth()->guard('chutro')->id();

        if (auth()->guard('quanly')->check()) {
            $quanly_id = $chutro_id;
            $chutro_id = QuanLy::where('id', $quanly_id)->value('chutro_id');
        }

        // Kiểm tra phòng trọ có tồn tại hay không
        $phongTro = PhongTro::find($id); // Sử dụng `$id` từ URL

        if (!$phongTro) {
            return response()->json([
                'success' => false,
                'message' => 'Phòng trọ không tồn tại',
            ], 404);
        }

        // Kiểm tra xem khách thuê có tồn tại trong phòng này chưa
        $existsInRoom = $phongTro->khachthues()->where('tenkhachthue', $request->tenkhachthue)->exists();

        if ($existsInRoom) {
            return response()->json([
                'success' => false,
                'message' => 'Tên khách thuê đã tồn tại trong phòng hiện tại.',
            ], 422);
        }

        // Tạo khách thuê mới nếu chưa tồn tại
        $khachthue = KhachThue::create([
            'tenkhachthue' => $request->tenkhachthue,
            'sodienthoai' => $request->sodienthoai,
            'ngaysinh' => $request->ngaysinh,
            'gioitinh' => $request->gioitinh,
            'cccd' => $request->cccd,
            'chutro_id' => $chutro_id,
        ]);

        // Gán khách thuê vào phòng trọ thông qua bảng pivot
        $phongTro->khachthues()->attach($khachthue->id);

        $khachthue->gioitinh = $khachthue->gioitinh == 0 ? 'nam' : 'nu';
        // Phản hồi JSON thành công
        return response()->json([
            'success' => true,
            'message' => 'Đã thêm khách thuê thành công',
            'id' =>  $phongTro->id,
            'sophong' =>  $phongTro->sophong,
            'data' => array_merge(['id' => $khachthue->id], $khachthue->toArray())
        ], 201);
    }


    public function delete($phongtro_id, $khachthue_id)
    {
        // Tìm kiếm bản ghi trong bảng pivot giữa Phòng Trọ và Khách Thuê
        $khachthue_phongtro = khachthue_phongtro::where('phongtro_id', $phongtro_id)
                                                 ->where('khachthue_id', $khachthue_id)
                                                 ->first();

        // Kiểm tra xem bản ghi có tồn tại không
        if (!$khachthue_phongtro) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy mối quan hệ giữa phòng trọ và khách thuê.',
            ], 404); // Trả về mã lỗi 404 nếu không tìm thấy
        }

        // Xóa bản ghi khỏi bảng pivot
        $khachthue_phongtro->delete();

        // Trả về phản hồi thành công
        return response()->json([
            'success' => true,
            'message' => 'Đã xóa khách thuê khỏi phòng trọ thành công!',
        ], 200); // Trả về mã thành công 200
    }


    public function update(Request $request, $id)
    {
        // Tìm khách thuê cần cập nhật thông tin
        $khachthue = KhachThue::find($id);

        // Kiểm tra nếu không tìm thấy khách thuê
        if (!$khachthue) {
            return response()->json([
                'success' => false,
                'message' => 'Khách thuê không tồn tại.',
            ], 404); // Trả về mã lỗi 404 nếu không tìm thấy khách thuê
        }

        // Xác thực các dữ liệu gửi lên
        $validator = Validator::make($request->all(), [
            'tenkhachthue' => 'required|string|max:255|unique:khachthue,tenkhachthue,' . $id,
            'sodienthoai'  => 'required|numeric|digits_between:5,12|unique:khachthue,sodienthoai,' . $id,
            'cccd'         => 'required|max:25|unique:khachthue,cccd,' . $id,
        ], [
            'tenkhachthue.unique' => 'Tên khách thuê vừa cập nhật đã tồn tại',
            'sodienthoai.unique'  => 'Số điện thoại vừa cập nhật đã tồn tại',
            'cccd'                => 'Căn cước công dân vừa cập nhật đã tồn tại',
            'sodienthoai.numeric' => 'Số điện thoại phải là số.',
            'tenkhachthue.max'    => 'Tên khách thuê không được quá 255 ký tự.',
            'sodienthoai.digits_between' => 'Số điện thoại phải từ 5 đến 12 chữ số.',
            'cccd.max'            => 'CCCD không được quá 25 ký tự.',
        ]);

        // Kiểm tra nếu dữ liệu không hợp lệ
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ.',
                'errors'  => $validator->errors(),
            ], 422); // Trả về mã lỗi 422 nếu xác thực không thành công
        }

        // Cập nhật thông tin khách thuê trong cơ sở dữ liệu
        $khachthue->update($request->all());

        $khachthue->gioitinh = $khachthue->gioitinh == 0 ? 'nam' : 'nu';
        // Trả về phản hồi thành công
        return response()->json([
            'success' => true,
            'message' => 'Đã cập nhật thông tin khách thuê thành công!',
            'data'    => $khachthue, // Trả lại dữ liệu khách thuê đã được cập nhật
        ], 200); // Trả về mã thành công 200
    }

    public function search(Request $request, $id)
    {
        // Tìm phòng trọ theo ID từ route
        $phongtro = PhongTro::find($id);
    
        if(!$phongtro) 
        {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy phòng trọ.'
            ], 404);
        }
    
        // Lấy giá trị tìm kiếm từ request
        $searchValue = $request->input('query');
    
        // Lấy danh sách khách thuê trong phòng trọ
        $khachthues = $phongtro->khachthues()
            ->where(function ($query) use ($searchValue) {
                $query->where('tenkhachthue', 'LIKE', "%{$searchValue}%")
                    ->orWhere('sodienthoai', 'LIKE', "%{$searchValue}%")
                    ->orWhere('ngaysinh', 'LIKE', "%{$searchValue}%")
                    ->orWhere('cccd', 'LIKE', "%{$searchValue}%");
    
                // Tìm kiếm theo giới tính
                if (strtolower($searchValue) === 'nam') {
                    $query->orWhere('gioitinh', 0);
                } elseif (strtolower($searchValue) === 'nữ') {
                    $query->orWhere('gioitinh', 1);
                }
            })
            ->paginate(5);
    
        // Lấy danh sách khách thuê không thuộc phòng trọ này
        $khachthue_ids = $phongtro->khachthues->pluck('id')->toArray();
        // $khachthuekhac = KhachThue::whereNotIn('id', $khachthue_ids)->get();
    
        // Trả về JSON response
        return response()->json([
            'success' => true,
            'message' => 'Tìm kiếm thành công.',
            'data' => [
                'id' => $phongtro->id,
                'sophong' => $phongtro->sophong,
                'khachthues' => $khachthues->items(),
                'pagination' => [
                    'current_page' => $khachthues->currentPage(),
                    'last_page' => $khachthues->lastPage(),
                    'total' => $khachthues->total(),
                    'per_page' => $khachthues->perPage()
                ],
                // 'khachthuekhac' => $khachthuekhac
            ]
        ]);
    }

    // Khách thuê tổng quát
    public function view()  
    {
        $chutro_id = auth()->guard('chutro')->id(); // Lấy ID người dùng hiện tại
     
        if (auth()->guard('quanly')->check())  // Nếu là QL
        {
           // $quanly = quanly::where('id', $chutro_id)->first();
           $quanly_id = auth()->guard('quanly')->id();
           $quanly = quanly::where('id', $quanly_id)->first();
           $chutro_id =  $quanly->chutro_id;
           
        }

        $khachthues = KhachThue::where('chutro_id', $chutro_id)
         ->withCount('phongtros') // Đếm số lượng phòng trọ đã thuê (từ bảng pivot)
         ->paginate(10); // Phân trang kết quả
        // Lấy URL để có thể quay lại trang 

        return response()->json([
            'success' => true,
            'message' => 'Danh sách khách thuê.',
            'data' => [
                'khachthues' => $khachthues->items(), // Lấy dữ liệu đã phân trang
                'pagination' => [
                    'total' => $khachthues->total(),
                    'per_page' => $khachthues->perPage(),
                    'current_page' => $khachthues->currentPage(),
                    'last_page' => $khachthues->lastPage(),
                    'from' => $khachthues->firstItem(),
                    'to' => $khachthues->lastItem(),
                ],
            ],
        ]);
    }


    public function destroy($khachthue_id)
    {
      // Tìm khách thuê theo ID
      $khachthue = KhachThue::find($khachthue_id);

      if(!$khachthue)
      {
        return response()->json([
            'success' => false,
            'message' => 'Không tìm ra được id của khách thuê',
        ], 200); // Trả về mã thành công 200
      }
      
      // Xóa khách thuê
      $khachthue->delete();
  
          // Hiện thị thông báo thành công 
          return response()->json([
            'success' => true,
            'message' => 'Đã xóa khách thuê thành công!',
        ], 200); // Trả về mã thành công 200

    }



    public function add(Request $request)
    {
        // Validate yêu cầu
        $validator = Validator::make($request->all(),
        [
            'tenkhachthue' => 'required|string|max:255',
            'sodienthoai' => 'required|numeric|digits_between:5,12|unique:khachthue',
            'ngaysinh' => 'required|date',
            'cccd' => 'required|max:25|unique:khachthue',
        ],
        [
            'sodienthoai.numeric' => 'Số điện thoại phải là số.',
            'tenkhachthue.required' => 'Tên khách thuê là bắt buộc.',
            'tenkhachthue.max' => 'Tên khách thuê không được quá 25 ký tự.',
            'sodienthoai.digits_between' => 'Số điện thoại phải từ 5 đến 12 chữ số.',
            'cccd.max' => 'CCCD không được quá 25 ký tự.',
            'cccd.unique' => 'CCCD này đã được đăng kí. Vui lòng chọn CCCD khác.',
            'sodienthoai.unique' => 'Số điện thoại đã được sài. Vui lòng chọn số khác.',
        ]
        );
    
            if ($validator->fails()) 
            {
                return response()->json([
                    'success' => false,
                    'message' => 'Dữ liệu không hợp lệ.',
                    'errors' => $validator->errors(),
                ], 422);
            }
            // Lấy chutro_id từ auth
            $chutro_id = auth()->guard('chutro')->id();
    
            // Nếu là quản lý, lấy chutro_id từ bảng `quanly`
            if (auth()->guard('quanly')->check()) 
            {
                $quanly = Quanly::findOrFail($chutro_id);
                $chutro_id = $quanly->chutro_id;
            }
        
            // Tạo dữ liệu khách thuê
            $data = $request->all();
            $data['chutro_id'] = $chutro_id;
        
            // Đưa khách thuê vào DB
            $khachThue = KhachThue::create($data);
        
            // Trả về phản hồi JSON thành công
            return response()->json([
                'success' => true,
                'message' => 'Khách thuê đã được thêm thành công.',
                'data' => array_merge(['id' => $khachThue->id], $khachThue->toArray())
            ], 201);
    }


    public function searching(Request $request)
    {
        $searchValue = $request->input('query');

        // Validate input tìm kiếm
        $request->validate([
            'query' => 'nullable|string|max:255',
        ]);

        // Tạo query builder cho KhachThue
        $query = KhachThue::query();

        // Tìm kiếm theo tên, số điện thoại và CCCD
        $query->where('tenkhachthue', 'LIKE', "%{$searchValue}%")
              ->orWhere('sodienthoai', 'LIKE', "%{$searchValue}%")
              ->orWhere('cccd', 'LIKE', "%{$searchValue}%");

        // Tìm kiếm giới tính theo từ khóa "nam" hoặc "nữ"
        if (strtolower($searchValue) === 'nam') {
            $query->orWhere('gioitinh', 0);
        } elseif (strtolower($searchValue) === 'nữ') {
            $query->orWhere('gioitinh', 1);
        }

        // Thực thi truy vấn và phân trang kết quả
        $khachthues = $query->paginate(5);

        // Trả về phản hồi JSON
        return response()->json([
            'success' => true,
            'message' => 'Kết quả tìm kiếm khách thuê.',
            'data' => [
                'khachthues' => $khachthues->items(), // Dữ liệu đã phân trang
                'pagination' => [
                    'total' => $khachthues->total(),
                    'per_page' => $khachthues->perPage(),
                    'current_page' => $khachthues->currentPage(),
                    'last_page' => $khachthues->lastPage(),
                    'from' => $khachthues->firstItem(),
                    'to' => $khachthues->lastItem(),
                ],
            ],
        ]);
    }






}
