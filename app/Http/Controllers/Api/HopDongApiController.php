<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\hopdong;
use App\Models\phongtro;
use App\Models\daytro;
use App\Models\khachthue_phongtro;
use Illuminate\Support\Facades\Validator;

class HopDongApiController extends Controller
{
    //
    public function view()
    {
        // Xác định điều kiện dựa trên loại người dùng
        $condition = auth()->guard('quanly')->check() ? 'quanly_id' : 'chutro_id';
        $userId = auth()->guard('quanly')->check()
            ? auth()->guard('quanly')->id()
            : auth()->guard('chutro')->id();

        // Truy vấn hợp đồng
        $hopdongs = HopDong::whereHas('khachthue_phongtro.phongtro.daytro', function ($query) use ($userId, $condition) {
                $query->where($condition, $userId);
            })
            ->with([
                'khachthue_phongtro.khachthue',
                'khachthue_phongtro.phongtro.daytro',
            ])
            ->get();

        // Định dạng lại dữ liệu
        $formattedHopdongs = $hopdongs->map(function ($hopdong) {
            return [
                'id' => $hopdong->id,
                'tendaytro' => $hopdong->khachthue_phongtro->phongtro->daytro->tendaytro ?? null,
                'tenkhachthue' => $hopdong->khachthue_phongtro->khachthue->tenkhachthue ?? null,
                'sophong' => $hopdong->khachthue_phongtro->phongtro->sophong ?? null,
                'songuoithue' => $hopdong->songuoithue,
                'tienphong' => $hopdong->khachthue_phongtro->phongtro->tienphong ?? null,
                'ngaybatdau' => $hopdong->ngaybatdau,
                'ngayketthuc' => $hopdong->ngayketthuc,
                'tiencoc' => $hopdong->tiencoc,
                'soxe' => $hopdong->soxe,
                'created_at' => $hopdong->created_at,
                'updated_at' => $hopdong->updated_at,
            ];
        });

        // Trả về JSON response
        return response()->json([
            'success' => true,
            'data' => $formattedHopdongs,
        ]);
    }

    public function viewAdd()
    {
        // Xác định điều kiện và ID người dùng
        $condition = auth()->guard('quanly')->check() ? 'quanly_id' : 'chutro_id';
        $userId = auth()->guard('quanly')->check()
            ? auth()->guard('quanly')->id()
            : auth()->guard('chutro')->id();
        
        // Lấy các dãy trọ liên quan đến người dùng
        $daytro_ids = DayTro::where($condition, $userId)->pluck('id');
        
        // Lấy các phòng trọ thuộc các dãy trọ này và có status = 1
        $phongtros = PhongTro::whereIn('daytro_id', $daytro_ids) // Điều kiện dãy trọ thuộc người dùng
            ->where('status', 1) // Chỉ lấy các phòng trọ có status = 1
            ->whereNotIn('id', function ($query) {
                $query->select('phongtro_id')
                      ->from('khachthue_phongtro')
                      ->whereIn('id', function ($query) {
                          $query->select('khachthue_phongtro_id')
                                ->from('hopdong');
                      });
            })
            ->with('khachthues') // Load khách thuê liên quan
            ->get();
        
        // Trả về JSON response
        return response()->json([
            'status' => 'success',
            'data' => $phongtros->map(function ($phongtro) {
                return [
                    'id' => $phongtro->id,
                    'sophong' => $phongtro->sophong,
                    'tienphong' => $phongtro->tienphong,
                    'status' => $phongtro->status,
                    'created_at' => $phongtro->created_at,
                    'updated_at' => $phongtro->updated_at,
                    'khachthues' => $phongtro->khachthues->map(function ($khachthue) {
                        return [
                            'id' => $khachthue->id,
                            'tenkhachthue' => $khachthue->tenkhachthue,
                            'sodienthoai' => $khachthue->sodienthoai,
                            'ngaysinh' => $khachthue->ngaysinh,
                            'cccd' => $khachthue->cccd,
                            'gioitinh' => $khachthue->gioitinh,
                            'created_at' => $khachthue->created_at,
                            'updated_at' => $khachthue->updated_at,
                            'soxe' => $khachthue->pivot->soxe,
                            'songuoithue' => $khachthue->pivot->songuoithue,
                            'tiencoc' => $khachthue->pivot->tiencoc,
                        ];
                    }),
                ];
            }),
        ]);
        
    }


    public function add(Request $request)
    {
        // Thông báo lỗi
        $message = [
            'required' => 'Vui lòng nhập :attribute.',
            'integer' => 'Vui lòng nhập số.',
            'numeric' => 'Vui lòng nhập số hợp lệ.',
            'min' => 'Không được nhập số âm.',
            'after' => 'Ngày kết thúc phải sau ngày bắt đầu.',
        ];

        // Validate data trước khi thêm vào
        $validator = Validator::make($request->all(), [
            'phongtro' => 'required|exists:phongtro,id',
            'khachthue' => 'required|exists:khachthue,id',
            'ngaybatdau' => 'required|date',
            'ngayketthuc' => 'required|date|after:ngaybatdau',
            'tiencoc' => 'required|numeric|min:0',
            'songuoithue' => 'required|integer|min:1',
            'soxe' => 'required|integer|min:0',
        ], $message);

        if ($validator->fails()) 
        {
            // Trả về lỗi validation dưới dạng JSON
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ.',
                'errors' => $validator->errors(),
            ], 422); // 422 Unprocessable Entity (Lỗi dữ liệu không hợp lệ)
        }
        // Lấy ra các data liên quan đến Số Phòng 
        $phongtro_id = $request->input('phongtro');
        $khachthue_id = $request->input('khachthue');

        // Tìm bản ghi trong bảng trung gian khachthue_phongtro
        $khachthue_phongtro = khachthue_phongtro::where('phongtro_id', $phongtro_id)
            ->where('khachthue_id', $khachthue_id)
            ->first();

        // Nếu không tìm thấy
        if (!$khachthue_phongtro) {
            return response()->json([
                'status' => 'error',
                'message' => 'Khách thuê hoặc phòng trọ không hợp lệ.',
            ], 404);
        }

        // Lấy id từ bản ghi trung gian
        $khachthue_phongtro_id = $khachthue_phongtro->id;

        // Kiểm tra sự tồn tại trong bảng hopdong
        $exists = hopdong::where('khachthue_phongtro_id', $khachthue_phongtro_id)->exists();

        if ($exists) {
            return response()->json([
                'status' => 'error',
                'message' => 'Phòng trọ này đã có hợp đồng.',
            ], 400);
        }

        // Tạo hợp đồng mới
        $hopdong = hopdong::create([
            'khachthue_phongtro_id' => $khachthue_phongtro_id,
            'songuoithue' =>$request->songuoithue,
            'ngaybatdau' =>$request->ngaybatdau,
            'ngayketthuc' => $request->ngayketthuc,
            'tiencoc' => $request->tiencoc,
            'soxe' => $request->soxe,
        ]);

        // Lưu hợp đồng vào DB
        $hopdong->save();

        // Trả về response JSON thành công
        return response()->json([
            'status' => 'success',
            'message' => 'Hợp đồng được thêm thành công!',
            'data' => $hopdong,
        ], 201);
    }


    public function delete($id)
    {
        $hopdong = hopdong::find($id);
        
        $hopdong->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Hợp đồng xóa thành công!',
        ], 201);
       
    }


    public function viewUpdate($id)
    {
         // Xác thực quyền truy cập và lấy thông tin người dùng
        // $condition = auth()->guard('quanly')->check() ? 'quanly_id' : 'chutro_id';
        // $userId = auth()->guard('quanly')->check()
        //     ? auth()->guard('quanly')->id()
        //     : auth()->guard('chutro')->id();

        // Lấy hợp đồng cần cập nhật
        $hopdong = HopDong::with('khachthue_phongtro.phongtro.daytro')->find($id);

        if (!$hopdong) {
            return response()->json([
                'status' => 'error',
                'message' => 'Hợp đồng không tồn tại.',
            ], 404);
        }

        // Lấy thông tin cần thiết từ hợp đồng
        $currentPhongTro = $hopdong->khachthue_phongtro->phongtro;
        $currentDayTro = $currentPhongTro->daytro;
        $currentKhachThueId = $hopdong->khachthue_phongtro->khachthue_id;

        // Lấy danh sách khách thuê liên quan đến phòng trọ
        $khachthues = $currentPhongTro->khachthues->map(function ($khachthue) {
            return [
                'id' => $khachthue->id,
                'tenkhachthue' => $khachthue->tenkhachthue,
            
            ];
        });

        // Chuẩn bị dữ liệu JSON trả về
        $response = [
            'status' => 'success',
            'data' => [
                'hopdong' => [
                    'id' => $hopdong->id,
                    'sophong' => $currentPhongTro->sophong,
                    'daytro' => $currentDayTro->id,
                    'songuoithue' => $hopdong->songuoithue,
                    'tenkhachthue' => $hopdong->khachthue_phongtro->khachthue->tenkhachthue,
                    'ngaybatdau' => $hopdong->ngaybatdau,
                    'ngayketthuc' => $hopdong->ngayketthuc,
                    'tiencoc' => $hopdong->tiencoc,
                    'soxe' => $hopdong->soxe,
                    'created_at' => $hopdong->created_at,
                    'updated_at' => $hopdong->updated_at,
                ],
                'khachthues' => $khachthues,
                'currentPhongTroId' => $currentPhongTro->id,
                'currentKhachThueId' => $currentKhachThueId,
            ],
        ];

        return response()->json($response);
    }



    public function update(Request $request, $id)
    {
        // Tìm hợp đồng theo ID
        $hopdong = HopDong::find($id);

        if (!$hopdong) {
            return response()->json([
                'status' => 'error',
                'message' => 'Hợp đồng không tồn tại.'
            ], 404);
        }

        // Thông báo lỗi
        $message = [
            'required' => 'Vui lòng nhập :attribute.',
            'integer' => 'Vui lòng nhập số.',
            'numeric' => 'Vui lòng nhập số hợp lệ.',
            'min' => 'Không được nhập số âm.',
            'after' => 'Ngày kết thúc phải sau ngày bắt đầu.'
        ];

        // Validate dữ liệu
        $validator = Validator::make($request->all(), [
            'phongtro' => 'required|exists:phongtro,id',
            'khachthue' => 'required|exists:khachthue,id',
            'ngaybatdau' => 'required|date',
            'ngayketthuc' => 'required|date|after:ngaybatdau',
            'tiencoc' => 'required|numeric|min:0',
            'songuoithue' => 'required|integer|min:1',
            'soxe' => 'required|integer|min:0',
        ], $message);

       

        if ($validator->fails()) 
        {
            // Trả về lỗi validation dưới dạng JSON
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ.',
                'errors' => $validator->errors(),
            ], 422); // 422 Unprocessable Entity (Lỗi dữ liệu không hợp lệ)
        }
        $data = $request->all();
        // Cập nhật dữ liệu
        $hopdong->update($data);

        // Trả về JSON thành công
        return response()->json([
            'status' => 'success',
            'message' => 'Hợp đồng được cập nhật thành công!',
            'data' => $hopdong
        ], 200);
    }


    public function search(Request $request)
    {
        // Lấy giá trị tìm kiếm từ request
        $searchValue = $request->input('query');
        
        // Xác định điều kiện dựa trên phân loại người dùng (quản lý hoặc chủ trọ)
        $condition = auth()->guard('quanly')->check() ? 'quanly_id' : 'chutro_id';
        $userId = auth()->guard('quanly')->check()
            ? auth()->guard('quanly')->id()
            : auth()->guard('chutro')->id();
        
        // Truy vấn để tìm kiếm các hợp đồng dựa trên các điều kiện tìm kiếm và điều kiện người dùng
        $hopdongs = HopDong::whereHas('khachthue_phongtro.phongtro.daytro', function ($query) use ($condition, $userId) {
                $query->where($condition, $userId); // Ràng buộc quyền truy cập
            })
            ->where(function ($query) use ($searchValue) {
                $query->whereHas('khachthue_phongtro', function ($q) use ($searchValue) {
                    $q->whereHas('khachthue', function ($subQuery) use ($searchValue) {
                        $subQuery->where('tenkhachthue', 'LIKE', "%{$searchValue}%");
                    })->orWhereHas('phongtro', function ($subQuery) use ($searchValue) {
                        $subQuery->where('sophong', 'LIKE', "%{$searchValue}%")
                                 ->orWhereHas('daytro', function ($subQuery) use ($searchValue) {
                                     $subQuery->where('tendaytro', 'LIKE', "%{$searchValue}%");
                                 });
                    });
                })
                ->orWhere('songuoithue', 'LIKE', "%{$searchValue}%")
                ->orWhereDate('ngaybatdau', '=', $searchValue)
                ->orWhereDate('ngayketthuc', '=', $searchValue)
                ->orWhere('tiencoc', 'LIKE', "%{$searchValue}%");
            })
            ->with([
                'khachthue_phongtro.khachthue',
                'khachthue_phongtro.phongtro.daytro'
            ])
            ->paginate(10);
            
        // Trả về JSON response
        return response()->json([
            'status' => 'success',
            'message' => 'Kết quả tìm kiếm hợp đồng.',
            'data' => $hopdongs
        ], 200);
    }

    

}
