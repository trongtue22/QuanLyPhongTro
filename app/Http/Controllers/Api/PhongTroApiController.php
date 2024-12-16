<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\daytro;
use App\Models\phongtro;
use App\Models\khachthue;
use App\Models\khachthue_phongtro;
use Illuminate\Support\Facades\Validator;

class PhongTroApiController extends Controller
{
    //
    public function show($id)
    {   
        // Lấy Dãy Trọ và các Phòng Trọ liên quan theo ID, bao gồm cả thông tin Khách Thuê cho mỗi Phòng Trọ
        $daytro = DayTro::with('phongtros.khachthues')->find($id);
    
        if (!$daytro) 
        {
            return response()->json([
                'success' => false,
                'message' => 'Dãy trọ không tồn tại.'
            ], 404);
        }
    
        // Phân trang danh sách Phòng Trọ
        $phongtros = $daytro->phongtros()->paginate(5);
    
    
        // Cập nhật trạng thái cho mỗi Phòng Trọ dựa trên số lượng Khách Thuê
        foreach ($daytro->phongtros as $phongtro) 
        {
            $khachthueCount = $phongtro->khachthues->count();
            $phongtro->status = $khachthueCount > 0 ? 1 : 0; // 1: Đã thuê, 0: Phòng trống
            $phongtro->save();
        }
    
        // Trả về response JSON
        return response()->json([
            'success' => true,
            'data' => [
                'daytro_id' => $daytro->id,
                'tendaytro' => $daytro->tendaytro,
                'phongtros' => $phongtros->map(function ($phongtro) {
                    return [
                        'id' => $phongtro->id,
                        'sophong' => $phongtro->sophong,
                        'tienphong' => $phongtro->tienphong,
                        'status' => $phongtro->status ? 'Đã thuê' : 'Phòng trống',
                        'so_khach_thue' => $phongtro->khachthues->count(),
                    ];
                }),
                'pagination' => [
                    'current_page' => $phongtros->currentPage(),
                    'last_page' => $phongtros->lastPage(),
                    'total' => $phongtros->total(),
                    'per_page' => $phongtros->perPage(),
                    'next_page_url' => $phongtros->nextPageUrl(),
                    'prev_page_url' => $phongtros->previousPageUrl(),
                ],
            ]
        ]);        
    }

    public function stored(Request $request, $id) 
    {   
        
        // Tìm Dãy Trọ theo ID
        $daytro = DayTro::find($id);
    
        if (!$daytro) 
        {
            return response()->json([
                'success' => false,
                'message' => 'Dãy trọ không tồn tại.'
            ], 404);
        }
    
        // Validate dữ liệu từ request
        $validator = Validator::make($request->all(), [
            'sophong' => 'required|numeric|unique:phongtro,sophong',
            'tienphong' => 'required|numeric',
        ], [
           'sophong.required' => 'Trường số phòng là bắt buộc.',
           'sophong.numeric' => 'Số phòng phải là số',
           'sophong.unique' => 'Số phòng đã tồn tại.',
           'tienphong.required' => 'Trường tiền phòng là bắt buộc.',
           'tienphong.numeric' => 'Tiền phòng phải là số',
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
       
        // Tạo phòng trọ và lưu vào DB => error 
        $phongtro = phongtro::create([
            'daytro_id' => $id,
            'sophong' => $request->sophong,
            'tienphong' => $request->tienphong,
        ]);
        
        // Trả về response JSON
        return response()->json([
            'success' => true,
            'message' => 'Đã thêm phòng trọ thành công',
            'data' => [
                'id' => $phongtro->id,
                'sophong' => $phongtro->sophong,
                'tienphong' => $phongtro->tienphong,
                'daytro_id' => $phongtro->daytro_id,
            ]
        ], 201); // 201 là mã trạng thái HTTP cho "Created"
    }

    public function delete($id)
    {
       // Tìm phòng trọ để xóa
       $phongtro = PhongTro::find($id);
       
       if (!$phongtro) {
           // Trả về JSON nếu không tìm thấy phòng trọ
           return response()->json([
               'success' => false,
               'message' => 'Phòng trọ không tồn tại.'
           ], 404);
       }
   
       // Xóa phòng trọ
       $phongtro->delete();
   
       // Trả về JSON xác nhận xóa thành công
       return response()->json([
           'success' => true,
           'message' => 'Đã xóa phòng trọ thành công!'
       ]);
    }

    public function update(Request $request, $id)
    {
        // Tìm phòng trọ cần cập nhật
        $phongtro = PhongTro::findOrFail($id);
        
        // Xác thực dữ liệu đầu vào
        $validator = Validator::make($request->all(), 
        [
            'sophong' => 'required|integer|unique:phongtro,sophong,' . $id,
            'tienphong' => 'required|numeric',
        ], [
            'sophong.required' => 'Trường số phòng là bắt buộc.',
            'sophong.unique' => 'Số phòng vừa cập nhật đã tồn tại trước.',
            'tienphong.required' => 'Trường tiền phòng là bắt buộc.',
            'tienphong.numeric' => 'Trường tiền phòng phải là số.'
        ]);
    
        // Nếu có lỗi, trả về JSON với thông tin lỗi
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
    
        // Cập nhật thông tin phòng trọ
        $phongtro->update($request->all());
    
        // Trả về JSON xác nhận cập nhật thành công
        return response()->json([
            'success' => true,
            'message' => 'Phòng trọ đã được cập nhật thành công!',
            'data' => [
                'id' => $phongtro->id,
                'sophong' => $phongtro->sophong,
                'tienphong' => $phongtro->tienphong
            ]
        ]);
    }

    public function search(Request $request, $id)
    {
        // Lấy giá trị tìm kiếm từ request
        $searchValue = $request->input('query');
        
        // Kiểm tra xem Dãy Trọ có tồn tại không
        $daytro = DayTro::find($id);
        if (!$daytro) {
            return response()->json([
                'success' => false,
                'message' => 'Dãy trọ không tồn tại.'
            ], 404);
        }

        // Tìm kiếm phòng trọ trong dãy trọ dựa trên giá trị tìm kiếm
        $phongtros = $daytro->phongtros()
            ->where(function ($query) use ($searchValue) {
                $query->where('sophong', 'LIKE', "%{$searchValue}%")
                      ->orWhere('tienphong', 'LIKE', "%{$searchValue}%")
                      ->orWhere('status', 'LIKE', "%{$searchValue}%");
            })
            ->paginate(5);

        if ($phongtros->isEmpty()) 
        {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy phòng trọ nào phù hợp.'
            ], 404);
        }

        // Trả về dữ liệu JSON
        return response()->json([
            'success' => true,
            'data' => [
                'daytro' => [
                    'id' => $daytro->id,
                    'tendaytro' => $daytro->tendaytro,
                ],
                'phongtros' => $phongtros->items(), // Lấy các phòng trọ từ phân trang
                'pagination' => [
                    'current_page' => $phongtros->currentPage(),
                    'last_page' => $phongtros->lastPage(),
                    'total' => $phongtros->total(),
                    'per_page' => $phongtros->perPage(),
                    'next_page_url' => $phongtros->nextPageUrl(),
                    'prev_page_url' => $phongtros->previousPageUrl(),
                ]
            ]
        ]);
    }

    // Phòng trọ tổng quát
    public function view()
    {
        $chutro_id =auth()->guard('chutro')->id(); // Lấy ID người dùng hiện tại

        if (auth()->guard('quanly')->check()) 
        {

            $quanly_id = auth()->guard('quanly')->id();
            
            // Lấy danh sách dãy trọ thuộc quản lý
            $daytros = DayTro::where('quanly_id', $quanly_id)->get();

        } else 
        {
            // Lấy danh sách dãy trọ thuộc chủ trọ
            $daytros = DayTro::where('chutro_id', $chutro_id)->get();
        }

        // Lấy danh sách phòng trọ thuộc các dãy trọ
        $phongtros = PhongTro::whereIn('daytro_id', $daytros->pluck('id'))
            ->with('daytro', 'khachthues') // Eager load các quan hệ
            ->paginate(5);

        foreach ($phongtros as $phongtro) {
            $khachthueCount = $phongtro->khachthues->count();
            $phongtro->status = $khachthueCount > 0 ? 1 : 0; // 1: Đã thuê, 0: Phòng trống
        }

        return response()->json([
            'success' => true,
            'message' => 'Lấy danh sách phòng trọ thành công.',
            'data' => [
                'phongtros' => $phongtros->map(function ($phongtro) {
                    return [
                        'id' => $phongtro->id,
                        'daytro_id' => $phongtro->daytro_id,
                        'sophong' => $phongtro->sophong,
                        'tienphong' => $phongtro->tienphong,
                        'status' => $phongtro->status,
                        'songuoithue' => $phongtro->khachthues->count(),
                        'created_at' => $phongtro->created_at,
                        'updated_at' => $phongtro->updated_at,
                        'daytro' => [
                            'id' => $phongtro->daytro->id,
                            'tendaytro' => $phongtro->daytro->tendaytro,
                        ],
                    ];
                }),
                'daytros' => $daytros->map(function ($daytro) {
                    return [
                        'id' => $daytro->id,
                        'tendaytro' => $daytro->tendaytro,
                    ];
                }),
                'pagination' => [
                    'current_page' => $phongtros->currentPage(),
                    'last_page' => $phongtros->lastPage(),
                    'total' => $phongtros->total(),
                    'per_page' => $phongtros->perPage()
                ]
            ]
        ]);
        
    }


    public function store(Request $request)
    {
        // Xác thực dữ liệu đầu vào
        $validator = Validator::make($request->all(), [
            'sophong' => 'required|numeric|unique:phongtro,sophong',
            'tienphong' => 'required|numeric',
            'daytro_id' => 'required|numeric',
            'daytro_id.numeric' => 'Dãy trọ id phải là số',
            'daytro_id.required' => 'Dãy trọ id không thể thiếu'
        ], [
           'sophong.required' => 'Trường số phòng là bắt buộc.',
           'sophong.numeric' => 'Số phòng phải là số',
           'sophong.unique' => 'Số phòng đã tồn tại.',
           'tienphong.required' => 'Trường tiền phòng là bắt buộc.',
           'tienphong.numeric' => 'Tiền phòng phải là số',
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
        
        
        // Tạo phòng trọ mới
        $phongtro = phongtro::create([
            'daytro_id' => $request->daytro_id,
            'sophong' => $request->sophong,
            'tienphong' => $request->tienphong,
        ]);

        // Phản hồi thành công
        return response()->json([
            'success' => true,
            'message' => 'Phòng trọ đã được thêm thành công!',
            'data' => [
                'id' => $phongtro->id,
                'daytro_id' => $phongtro->daytro_id,
                'sophong' => $phongtro->sophong,
                'tienphong' => $phongtro->tienphong,
                'created_at' => $phongtro->created_at,
                'updated_at' => $phongtro->updated_at,
            ],
        ], 201);
    }


    public function searching(Request $request)
    {
        $searchValue = $request->input('query');
    
        // Kiểm tra người dùng qua guard
        if (!auth()->guard('chutro')->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Người dùng chưa đăng nhập.',
            ], 401);
        }
    
        $user = auth()->guard('chutro')->user();
    
        // Lấy danh sách dãy trọ
        $daytros = DayTro::select('id', 'tendaytro')
            ->where('chutro_id', $user->id)
            ->get();
    
        // Tìm kiếm trong bảng PhongTro và DayTro
        $phongtros = PhongTro::with(['daytro:id,tendaytro'])
            ->where(function ($query) use ($searchValue) {
                $query->where('sophong', 'LIKE', "%{$searchValue}%")
                      ->orWhere('tienphong', 'LIKE', "%{$searchValue}%")
                      ->orWhere('status', 'LIKE', "%{$searchValue}%");
            })
            ->whereHas('daytro', function ($query) use ($searchValue, $user) {
                $query->where('tendaytro', 'LIKE', "%{$searchValue}%")
                      ->where('chutro_id', $user->id);
            })
            ->paginate(5);
        
        // Xây dựng JSON trực tiếp
        $phongtroData = [];
        foreach ($phongtros as $phongtro) {
            $phongtroData[] = [
                'id' => $phongtro->id,
                'daytro_id' => $phongtro->daytro_id,
                'sophong' => $phongtro->sophong,
                'tienphong' => $phongtro->tienphong,
                'status' => $phongtro->status,
                'created_at' => $phongtro->created_at,
                'updated_at' => $phongtro->updated_at,
                'daytro' => [
                    'id' => $phongtro->daytro->id,
                    'tendaytro' => $phongtro->daytro->tendaytro,
                ],
            ];
        }
    
        $daytroData = [];
        foreach ($daytros as $daytro) {
            $daytroData[] = [
                'id' => $daytro->id,
                'tendaytro' => $daytro->tendaytro,
            ];
        }
    
        // Trả về JSON
        return response()->json([
            'success' => true,
            'message' => 'Kết quả tìm kiếm phòng trọ.',
            'data' => [
                'phongtros' => $phongtroData,
                'daytros' => $daytroData,
                'pagination' => [
                    'total' => $phongtros->total(),
                    'per_page' => $phongtros->perPage(),
                    'current_page' => $phongtros->currentPage(),
                    'last_page' => $phongtros->lastPage(),
                    'from' => $phongtros->firstItem(),
                    'to' => $phongtros->lastItem(),
                ],
            ],
        ]);
    }

    public function index($id)
    {
        $chutro_id = auth()->guard('chutro')->id(); // Lấy ID chủ trọ hiện tại
        $condition = auth()->guard('quanly')->check() ? 'quanly_id' : 'chutro_id';

        if (auth()->guard('quanly')->check()) {
            // Nếu là quản lý, lấy dãy trọ thuộc quyền quản lý
            $daytro = daytro::where('quanly_id', $chutro_id)->first();
        }

        // Tìm khách thuê theo ID và load các phòng trọ liên quan cùng dãy trọ
        $khachthue = KhachThue::with('phongtros.daytro')->find($id);

        if(!$khachthue)
        {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm ra khách thuê',
            ]);
        }

        // Lấy danh sách phòng trọ mà khách thuê này đã thuê và phân trang
        $phongtros = $khachthue->phongtros()
            ->whereHas('daytro', function ($query) use ($condition, $chutro_id) {
                $query->where($condition, $chutro_id);
            })
            ->orderBy('id', 'asc')
            ->paginate(5);

        // Lấy danh sách dãy trọ và phòng trọ chưa được thuê bởi khách thuê này
        $daytros = DayTro::with(['phongtros' => function ($query) use ($khachthue) {
            $query->whereDoesntHave('khachthues', function ($q) use ($khachthue) {
                $q->where('khachthue_id', $khachthue->id);
            });
        }])
        ->where($condition, $chutro_id)
        ->get();

        // Trả về phản hồi JSON
        return response()->json([
            'success' => true,
            'message' => 'Danh sách phòng trọ của khách thuê.',
            'data' => [
                'id' => $khachthue->id,
                'tenkhachthue' => $khachthue->tenkhachthue,
                'phongtros' => [
                    'data' => $phongtros->map(function ($phongtro) {
                        // Loại bỏ trường pivot khỏi dữ liệu
                        return [
                            'id' => $phongtro->id,
                            'sophong' => $phongtro->sophong,
                            'tienphong' => $phongtro->tienphong,
                            'status' => $phongtro->status,
                            'created_at' => $phongtro->created_at,
                            'updated_at' => $phongtro->updated_at,
                        ];
                    }),
                    'pagination' => [
                        'total' => $phongtros->total(),
                        'per_page' => $phongtros->perPage(),
                        'current_page' => $phongtros->currentPage(),
                        'last_page' => $phongtros->lastPage(),
                        'from' => $phongtros->firstItem(),
                        'to' => $phongtros->lastItem(),
                    ],
                ],
                'daytros' => $daytros->map(function ($daytro) {
                    return [
                        'id' => $daytro->id,
                        'tendaytro' => $daytro->tendaytro,
                        'phongtros' => $daytro->phongtros->map(function ($phongtro) {
                            return [
                                'id' => $phongtro->id,
                                'sophong' => $phongtro->sophong,
                            ];
                        }),
                    ];
                }),
            ],
        ]);
        
    }   


    public function finding(Request $request, $id)
    {
        // Xác thực người dùng
        $user = auth()->guard('chutro')->user() ?? auth()->guard('quanly')->user();
       
        if (!$user) 
        {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền truy cập.',
            ], 403);
        }

        // Lấy ID của người dùng hiện tại
        $chutro_id = $user->id;

        $condition = auth()->guard('quanly')->check() ? 'quanly_id' : 'chutro_id';

        // Lấy dữ liệu tìm kiếm từ request
        $searchValue = $request->input('query');

        // Kiểm tra và tìm khách thuê theo ID từ URL
        $khachthue = KhachThue::with('phongtros.daytro')->find($id);
        if (!$khachthue) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy khách thuê.',
            ], 404);
        }

        // Lấy danh sách dãy trọ thuộc quyền quản lý/chủ trọ
        $daytros = DayTro::where($condition, $chutro_id)->get();

        // Tìm kiếm các phòng trọ mà khách thuê đang thuê
        $phongtros = $khachthue->phongtros()
            ->whereHas('daytro', function ($query) use ($condition, $chutro_id)
             {
                $query->where($condition, $chutro_id);
            })
            ->where(function ($query) use ($searchValue) {
                $query->where('sophong', 'LIKE', "%{$searchValue}%")
                      ->orWhere('tienphong', 'LIKE', "%{$searchValue}%")
                      ->orWhere('status', 'LIKE', "%{$searchValue}%")
                      ->orWhereHas('daytro', function ($query) use ($searchValue) {
                          $query->where('tendaytro', 'LIKE', "%{$searchValue}%");
                      });
            })
            ->orderBy('id', 'asc')
            ->paginate(5); // Phân trang

        // Trả về JSON response
        return response()->json([
            'success' => true,
            'message' => 'Danh sách phòng trọ của khách thuê.',
            'data' => [
                'id' => $khachthue->id,
                'tenkhachthue' => $khachthue->tenkhachthue,
                'phongtros' => [
                    'data' => $phongtros->items(),
                    'pagination' => [
                        'total' => $phongtros->total(),
                        'per_page' => $phongtros->perPage(),
                        'current_page' => $phongtros->currentPage(),
                        'last_page' => $phongtros->lastPage(),
                        'from' => $phongtros->firstItem(),
                        'to' => $phongtros->lastItem(),
                    ],
                ],
                'daytros' => $daytros->map(function ($daytro) {
                    return [
                        'id' => $daytro->id,
                        'tendaytro' => $daytro->tendaytro,
                        'phongtros' => $daytro->phongtros->map(function ($phongtro) {
                            return [
                                'id' => $phongtro->id,
                                'sophong' => $phongtro->sophong,
                            ];
                        }),
                    ];
                }),
            ],
        ]);
    }


    public function add(Request $request)
    {
        // Kiểm tra xác thực
        $user = auth()->guard('chutro')->user() ?? auth()->guard('quanly')->user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Người dùng không được xác thực.',
            ], 401);
        }
    
        // Lấy dữ liệu từ request
        $khachthue_id = $request->input('khachthue_id');
        $phongtro_id = $request->input('phongtro_id');
    
        // Xác thực dữ liệu đầu vào
        $validator = Validator::make($request->all(), [
            'khachthue_id' => 'required|exists:khachthue,id',
            'phongtro_id' => 'required|exists:phongtro,id',
        ], [
            'khachthue_id.required' => 'ID khách thuê là bắt buộc.',
            'khachthue_id.exists' => 'Khách thuê không tồn tại.',
            'phongtro_id.required' => 'ID phòng trọ là bắt buộc.',
            'phongtro_id.exists' => 'Phòng trọ không tồn tại.',
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
        
    
        // Tạo bản ghi mới trong bảng pivot
        $khachthue_phongtro = khachthue_phongtro::create([
            'khachthue_id' => $khachthue_id, 
            'phongtro_id' =>  $phongtro_id 
        ]);

        $exists = khachthue_phongtro::where('khachthue_id', $khachthue_id)
        ->where('phongtro_id', $phongtro_id)
        ->exists();

        if ($exists) {
            // Trả về phản hồi JSON lỗi
            return response()->json([
                'success' => false,
                'message' => 'Tổ hợp khách thuê và phòng trọ đã tồn tại.',
            ], 400); // 400: Bad Request
        }

        $khachthue_phongtro->load(['khachthue', 'phongtro']);
    
        // Trả về phản hồi JSON thành công
        return response()->json([
            'success' => true,
            'message' => 'Thêm phòng trọ vào khách thuê thành công!',
            'data' => [
                'id' => $khachthue_phongtro->id,
                'khachthue_id' => $khachthue_phongtro->khachthue->id,
                'khachthue' => $khachthue_phongtro->khachthue->tenkhachthue,
                'phongtro_id' => $khachthue_phongtro->phongtro->id,
                'phongtro' => $khachthue_phongtro->phongtro->sophong,
            ],
        ], 201); // 201: Created
    }


    public function destroy($khachthue_id, $phongtro_id)
    {    
        // Tìm bản ghi trong bảng pivot
        $khachthue_phongtro = khachthue_phongtro::where('khachthue_id', $khachthue_id)
            ->where('phongtro_id', $phongtro_id)
            ->first();

        if (!$khachthue_phongtro) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy bản ghi phù hợp để xóa.',
            ], 404);
        }

        // Xóa bản ghi
        $khachthue_phongtro->delete();

        // Trả về phản hồi JSON
        return response()->json([
            'success' => true,
            'message' => 'Xóa phòng trọ thành công!',
            'data' => [
                'khachthue_id' => $khachthue_id,
                'phongtro_id' => $phongtro_id,
            ],
        ], 200); // 200: OK
    }    


    

   

}
