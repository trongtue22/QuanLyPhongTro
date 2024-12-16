<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\hoadon;
use App\Models\QuanLy;
use App\Models\dichvu;
use App\Models\hopdong;
use App\Models\KhachThue_PhongTro;
use Carbon\Carbon; 

class HoaDonApiController extends Controller
{
    //
    public function view()
    {
        // Kiểm tra loại người dùng (quản lý hoặc chủ trọ)
        $condition  = auth()->guard('quanly')->check() ? 'quanly_id' : 'chutro_id';
        $userId = auth()->guard('quanly')->check()
            ? auth()->guard('quanly')->id()
            : auth()->guard('chutro')->id();

        // Truy vấn lấy hóa đơn thuộc về người dùng hiện tại
        $hoadons = Hoadon::whereHas('hopdong.khachthue_phongtro.phongtro.daytro', function ($query) use ($condition , $userId) {
                $query->where($condition , $userId); // Lọc theo quanly_id hoặc chutro_id
            })
            ->with([
                'hopdong.khachthue_phongtro.khachthue',   // Thông tin khách thuê
                'hopdong.khachthue_phongtro.phongtro.daytro', // Thông tin phòng trọ và dãy trọ
                'dichvu'  // Thông tin dịch vụ
            ])
            ->paginate(10); // Số lượng hóa đơn trên mỗi trang

        $hoadonsArray = $hoadons->toArray();
        unset($hoadonsArray['links']); // Loại bỏ trường links

        // Trả về JSON
        return response()->json([
            'status' => 'success',
            'data' => $hoadonsArray
        ]); 
    }   

    public function viewAdd()
    {
        // Xác định loại người dùng dựa trên auth guard
        $userType = auth()->guard('quanly')->check() ? 'quanly_id' : 'chutro_id';

        $userId = auth()->guard('quanly')->check()
            ? auth()->guard('quanly')->id()
            : auth()->guard('chutro')->id();

        // Nếu là quản lý, lấy chutro_id liên kết với quản lý
        $chutro_ID = $userId;
    
        $dichvu_id = $chutro_ID;

        if (auth()->guard('quanly')->check()) {
            $quanly = QuanLy::find($userId);
            $dichvu_id = $quanly->chutro_id;
        }

        // Lấy thông tin dịch vụ
        $dichvu = DichVu::where('chutro_id', $chutro_ID)->first();

        // Lấy các hợp đồng liên quan
        $hopdongs = HopDong::whereHas('khachthue_phongtro.phongtro.daytro', function ($query) use ($userType, $chutro_ID) {
                $query->where($userType, $chutro_ID);
            })
            ->with([
                'khachthue_phongtro.khachthue',
                'khachthue_phongtro.phongtro.daytro'
            ])
            ->get();

        // Lấy danh sách dãy trọ, phòng trọ, và khách thuê duy nhất từ hợp đồng
        $daytros = $hopdongs->pluck('khachthue_phongtro.phongtro.daytro')->unique('id')->values();
        $phongtros = $hopdongs->pluck('khachthue_phongtro.phongtro')->unique('id')->values();
        $khachthues = $hopdongs->pluck('khachthue_phongtro.khachthue')->unique('id')->values();

        // Lấy giá trị số nước mới lớn nhất nhóm theo hopdong_id
        $maxSonuocMoi = HoaDon::selectRaw('MAX(sonuocmoi) as max_sonuocmoi, hopdong_id')
            ->whereHas('dichvu', function ($query) use ($dichvu_id) {
                $query->where('chutro_id', $dichvu_id);
            })
            ->groupBy('hopdong_id')
            ->get();

        // Lấy giá trị số điện mới lớn nhất nhóm theo hopdong_id
        $maxSodienMoi = HoaDon::selectRaw('MAX(sodienmoi) as max_sodienmoi, hopdong_id')
            ->whereHas('dichvu', function ($query) use ($dichvu_id) {
                $query->where('chutro_id', $dichvu_id);
            })
            ->groupBy('hopdong_id')
            ->get();

        // Lấy toàn bộ khachthue_phongtro
        $khachthue_phongtro = KhachThue_PhongTro::all();

        // Trả về dữ liệu JSON
        return response()->json([
            'status' => 'success',
            'data' => [
                'daytros' => $daytros,
                'phongtros' => $phongtros,
                'khachthues' => $khachthues,
                'maxSonuocMoi' => $maxSonuocMoi,
                'maxSodienMoi' => $maxSodienMoi,
                'hopdongs' => $hopdongs,
                'khachthue_phongtro' => $khachthue_phongtro,
                'dichvu' => $dichvu,
            ]
        ]);
    }

    public function add(Request $request)
    {
      

        // Check tính độc nhất của tháng tạo hóa đơn theo cùng 1 dãy hợp đồng 
        $ngaybatdau = Carbon::parse($request->ngaybatdau)->format('Y-m'); // Lấy năm-tháng từ ngày

       
        // Kiểm tra trùng lặp theo hopdong_id và tháng-năm
        $duplicateHoaDon = HoaDon::where('hopdong_id', $request->hopdong_id)
            ->whereRaw('DATE_FORMAT(created_at, "%Y-%m") = ?', [$ngaybatdau])
            ->exists();
            
        // Nếu hóa đơn đã tồn tại cho tháng này, trả về thông báo lỗi
        if ($duplicateHoaDon) {
            return response()->json([
                'status' => 'error',
                'message' => 'Hóa đơn cho tháng này đã tồn tại cho hợp đồng này!'
            ], 400); // Trả về lỗi với mã 400
        }

       
        // Thêm hóa đơn mới
        $hoadon = HoaDon::create([
            'dichvu_id' => $request->dichvu_id,
            'hopdong_id' => $request->hopdong_id,
            'sodiencu' => $request->diencu,
            'sodienmoi' => $request->dienmoi,
            'sonuoccu' => $request->nuoccu,
            'sonuocmoi' => $request->nuocmoi,
            'tongtien' => $request->tongtien,
            'status' => $request->status,
            
        ]);
        
        // Trả về thông báo thành công
        return response()->json([
            'status' => 'success',
            'message' => 'Đã thêm hóa đơn thành công!',
            'data' => $hoadon // Trả về thông tin hóa đơn vừa tạo
        ], 201); // Trả về thành công với mã 201 (Created)
    }

    public function delete($id)
    {
        // Tìm hóa đơn theo ID
        $hoadon = HoaDon::findOrFail($id);

        // Xóa hóa đơn
        $hoadon->delete();

        // Trả về phản hồi JSON
        return response()->json([
            'message' => 'Đã xóa hóa đơn thành công!',
            'data' => $hoadon
        ], 200); // HTTP status code 200 - OK
    }

    public function search(Request $request)
    {
        $searchValue = str_replace(',', '', $request->input('query'));
        
        // Kiểm tra xem người dùng có đăng nhập từ guard nào không
        if (auth()->guard('quanly')->check()) {
            $user = auth()->guard('quanly')->user();
            $condition = 'quanly_id';
        } elseif (auth()->guard('chutro')->check()) {
            $user = auth()->guard('chutro')->user();
            $condition = 'chutro_id';
        } 

        // Lấy ID người dùng từ guard đã đăng nhập
        $chutro_id = $user->id;

        // Tìm kiếm các hóa đơn thuộc về chủ trọ hoặc quản lý hiện tại
        $hoadons = Hoadon::whereHas('hopdong.khachthue_phongtro.phongtro.daytro', function ($query) use ($condition, $chutro_id) {
            $query->where($condition, $chutro_id);
        })
        ->where(function ($query) use ($searchValue) {
            if ($searchValue) {
                $query->whereHas('hopdong.khachthue_phongtro.khachthue', function ($q) use ($searchValue) {
                        $q->where('tenkhachthue', 'LIKE', "%{$searchValue}%");
                    })
                    ->orWhereHas('hopdong.khachthue_phongtro.phongtro', function ($q) use ($searchValue) {
                        $q->where('sophong', 'LIKE', "%{$searchValue}%")
                          ->orWhereHas('daytro', function ($subQuery) use ($searchValue) {
                              $subQuery->where('tendaytro', 'LIKE', "%{$searchValue}%");
                          });
                    })
                    ->orWhere('sodiencu', 'LIKE', "%{$searchValue}%")
                    ->orWhere('sodienmoi', 'LIKE', "%{$searchValue}%")
                    ->orWhere('sonuoccu', 'LIKE', "%{$searchValue}%")
                    ->orWhere('sonuocmoi', 'LIKE', "%{$searchValue}%")
                    ->orWhere('tongtien', 'LIKE', "%{$searchValue}%")
                    ->orWhereHas('hopdong.khachthue_phongtro.phongtro', function ($q) use ($searchValue) {
                        $q->where('tienphong', 'LIKE', "%{$searchValue}%");
                    });
            }
        })
        ->with([
            'hopdong.khachthue_phongtro.khachthue',
            'hopdong.khachthue_phongtro.phongtro.daytro',
            'dichvu'
        ])
        ->paginate(10);

        // Trả về dữ liệu dưới dạng JSON
        return response()->json([
            'data' => $hoadons->items(), // Trả về các hóa đơn tìm được
            'pagination' => [
                'total' => $hoadons->total(),
                'per_page' => $hoadons->perPage(),
                'current_page' => $hoadons->currentPage(),
                'last_page' => $hoadons->lastPage(),
                'next_page_url' => $hoadons->nextPageUrl(),
                'prev_page_url' => $hoadons->previousPageUrl(),
            ]
        ], 200); // Trả về HTTP status code 200
    }

    public function viewUpdate($id)
    {
        // Lấy chutro_id từ session hoặc thông qua auth
        $user = auth()->guard('quanly')->check()
            ? auth()->guard('quanly')->id()
            : auth()->guard('chutro')->id();
        
        $condition = auth()->guard('quanly')->check() ? 'quanly_id' : 'chutro_id';
        $chutro_id = $user;

        // Kiểm tra dịch vụ
        $dichvu = DichVu::where('chutro_id', $chutro_id)->first();

        // Lấy các hợp đồng liên quan đến chủ trọ
        $hopdongs = HopDong::whereHas('khachthue_phongtro.phongtro.daytro', function ($query) use ($condition, $chutro_id) {
            $query->where($condition, $chutro_id);
        })
            ->with([
                'khachthue_phongtro.khachthue',
                'khachthue_phongtro.phongtro.daytro'
            ])
            ->get();

        // Trích xuất danh sách duy nhất
        $daytros = $hopdongs->pluck('khachthue_phongtro.phongtro.daytro')->unique('id');
        $phongtros = $hopdongs->pluck('khachthue_phongtro.phongtro')->unique('id');
        $khachthues = $hopdongs->pluck('khachthue_phongtro.khachthue')->unique('id');

        // Tìm giá trị lớn nhất cho `sonuocmoi` và `sodienmoi`
        $maxSonuocMoi = HoaDon::selectRaw('MAX(sonuocmoi) as max_sonuocmoi, hopdong_id')
            ->whereHas('dichvu', function ($query) use ($chutro_id) {
                $query->where('chutro_id', $chutro_id);
            })
            ->groupBy('hopdong_id')
            ->get();

        $maxSodienMoi = HoaDon::selectRaw('MAX(sodienmoi) as max_sodienmoi, hopdong_id')
            ->whereHas('dichvu', function ($query) use ($chutro_id) {
                $query->where('chutro_id', $chutro_id);
            })
            ->groupBy('hopdong_id')
            ->get();

        // Lấy hóa đơn hiện tại
        $hoadonUpdate = HoaDon::with([
            'hopdong.khachthue_phongtro.phongtro.daytro'
        ])->findOrFail($id);

        // Tìm hóa đơn tiếp theo
        $HoadonHienDai = HoaDon::findOrFail($id);
        $hopdongIdHienTai = $HoadonHienDai->hopdong_id;
        $sodienmoiHienTai = $HoadonHienDai->sodienmoi;

        $hoadonTiepTheo = HoaDon::where('hopdong_id', $hopdongIdHienTai)
            ->where('sodienmoi', '>', $sodienmoiHienTai)
            ->orderBy('sodienmoi', 'asc')
            ->first();

        $maxValue = !$hoadonTiepTheo; // Nếu không có hóa đơn tiếp theo, maxValue là true

        return response()->json([
            'daytros' => $daytros,
            'phongtros' => $phongtros,
            'khachthues' => $khachthues,
            'maxSonuocMoi' => $maxSonuocMoi,
            'maxSodienMoi' => $maxSodienMoi,
            'hopdongs' => $hopdongs,
            'dichvu' => $dichvu,
            'hoadonUpdate' => $hoadonUpdate,
            'hoadonTiepTheo' => $hoadonTiepTheo,
            'maxValue' => $maxValue
        ], 200);
    }  
    

    public function update(Request $request, $id)
    {
        // Lấy hóa đơn hiện tại
        $HoadonHienDai = Hoadon::findOrFail($id);
    
        $hopdongIdHienTai = $HoadonHienDai->hopdong_id;
        $sodienmoiHienTai = $request->dienmoi;
        $sonuocmoiHienTai = $request->nuocmoi;
    
        // Lấy hóa đơn tiếp theo
        $hoadonTiepTheo = Hoadon::where('hopdong_id', $hopdongIdHienTai)
            ->where('sodiencu', '>=', $sodienmoiHienTai)
            ->orderBy('created_at', 'asc')
            ->first();
    
        // Nếu không có hóa đơn tiếp theo
         if($hoadonTiepTheo)
         {
    
            // Tính lại các thông số hóa đơn tiếp theo
            $dichvu = Dichvu::findOrFail($hoadonTiepTheo->dichvu_id);
    
            $tongsocantru = ($hoadonTiepTheo->sodienmoi - $sodienmoiHienTai) * $dichvu->dien 
                          + ($hoadonTiepTheo->sonuocmoi - $sonuocmoiHienTai) * $dichvu->nuoc;
    
            $hoadonTiepTheo->tongtien = $hoadonTiepTheo->tongtien - $tongsocantru;
    
            // Cập nhật số điện, nước cũ cho hóa đơn tiếp theo
            $hoadonTiepTheo->sodiencu = $sodienmoiHienTai;
            $hoadonTiepTheo->sonuoccu = $sonuocmoiHienTai;
    
            $hoadonTiepTheo->save();
        }
    
        // Cập nhật hóa đơn hiện tại
        $HoadonHienDai->sodienmoi = $sodienmoiHienTai;
        $HoadonHienDai->sonuocmoi = $sonuocmoiHienTai;
        $HoadonHienDai->tongtien = $request->tongtien;
        $HoadonHienDai->created_at = $request->ngaybatdau;
        $HoadonHienDai->save();
    
        return response()->json(['message' => 'Đã cập nhật hóa đơn thành công!'], 200);
    }
    

    public function updateStatus(Request $request, $id)
    {
        // Lấy thông tin hóa đơn cần update
        $hoadon = Hoadon::findOrFail($id);

        // Cập nhật trạng thái
        $hoadon->status = $request->input('status');
        $hoadon->save();

        // Trả về phản hồi thành công
        return response()->json([
            'success' => true,
            'message' => 'Đã cập nhật trạng thái thành công!',
            'data' => $hoadon
        ], 200);
    }









}
