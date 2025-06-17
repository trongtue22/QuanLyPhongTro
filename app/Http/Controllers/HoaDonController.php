<?php

namespace App\Http\Controllers;
use App\Models\hopdong;
use App\Models\daytro;
use App\Models\hoadon;
use App\Models\dichvu;
use App\Models\quanly;
use App\Models\phongtro;
use Carbon\Carbon; 
use App\Models\khachthue_phongtro;
use Illuminate\Http\Request;

class HoaDonController extends Controller
{
    //
    public function view()
    {   
        $chutro_id = session('chutro_id');
        $condition = session()->has('user_type') ? 'quanly_id' : 'chutro_id';

        // Lấy các hóa đơn liên quan và phân trang
        $hoadons = Hoadon::whereHas('hopdong.khachthue_phongtro.phongtro.daytro', function ($query) use ($condition, $chutro_id) {
                $query->where($condition, $chutro_id);
            })
            ->with([
                'hopdong.khachthue_phongtro.khachthue',
                'hopdong.khachthue_phongtro.phongtro.daytro',
            ])
            ->paginate(10);

        // Lấy dịch vụ mới nhất cho từng dãy trọ
        $dichvu = DichVu::where('chutro_id', $chutro_id)
            ->orderByDesc('id')
            ->get()
            ->unique('daytro_id')
            ->keyBy('daytro_id');

        // Gán dịch vụ tương ứng vào từng hóa đơn
        $hoadons->getCollection()->transform(function ($hoadon) use ($dichvu) {
            $daytroId = optional($hoadon->hopdong?->khachthue_phongtro?->phongtro?->daytro)->id;
            $hoadon->dv = $dichvu[$daytroId] ?? null;
            return $hoadon;
        });

        return view('pages.hoadon', compact('hoadons'));
    }

    // Trả về data cho phần Thêm Hóa Đơn
    public function viewAdd()
    {

        // Lấy chutro_id từ session
        $chutro_id = session('chutro_id'); 
    
        $dichvu_id = $chutro_id;
    
        $condition = session()->has('user_type') ? 'quanly_id' : 'chutro_id';
        
        if (session()->has('user_type')) 
        {
            $quanly = quanly::where('id', $chutro_id)->first();
            $dichvu_id = $quanly->chutro_id; // Lấy chutro_id của quản lý
        }
    
        // Lấy dịch vụ mới nhất
        // $dichvu = dichvu::where('chutro_id', $dichvu_id)
        //     ->latest() // Lấy dịch vụ mới nhất theo created_at
        //     ->first();

        $dichvu = DichVu::where('chutro_id', $chutro_id)
            ->orderByDesc('id')
            ->get()
            ->unique('daytro_id')
            ->keyBy('daytro_id');
        
        
        // Lấy hợp đồng có liên kết với khách thuê, phòng trọ và dãy trọ
        $hopdongs = HopDong::whereHas('khachthue_phongtro.phongtro.daytro', function ($query) use ($condition, $chutro_id) {
                $query->where($condition, $chutro_id);
            })
            ->with([
                'khachthue_phongtro.khachthue',  
                'khachthue_phongtro.phongtro.daytro' 
            ])
            ->get();
       
        
            
        // Lấy dãy trọ, phòng trọ, khách thuê từ danh sách hợp đồng
        $daytros = $hopdongs->pluck('khachthue_phongtro.phongtro.daytro')->unique('id');
        $phongtros = $hopdongs->pluck('khachthue_phongtro.phongtro')->unique('id');
        $khachthues = $hopdongs->pluck('khachthue_phongtro.khachthue')->unique('id');
    
        // Lấy giá trị nước lớn nhất nhóm theo hopdong_id
        $maxSonuocMoi = HoaDon::selectRaw('MAX(sonuocmoi) as max_sonuocmoi, hopdong_id')
            ->whereHas('dichvus', function($query) use ($dichvu_id) {
                $query->where('chutro_id', $dichvu_id);
            })
            ->groupBy('hopdong_id')
            ->get();
    
        // Lấy giá trị điện lớn nhất nhóm theo hopdong_id
        $maxSodienMoi = HoaDon::selectRaw('MAX(sodienmoi) as max_sodienmoi, hopdong_id')
            ->whereHas('dichvus', function($query) use ($dichvu_id) {
                $query->where('chutro_id', $dichvu_id);
            })
            ->groupBy('hopdong_id')
            ->get();
        
        $khachthue_phongtro = khachthue_phongtro::all();
        
        
        // Trả về view
        return view('pages.addHoaDon', compact(
            'daytros', 
            'phongtros', 
            'khachthues',
            'maxSonuocMoi',
            'hopdongs',
            'maxSodienMoi',
            'khachthue_phongtro',
            'dichvu'
        ));
    }


    public function add(Request $request)
{   
    // Check tính độc nhất của tháng tạo hóa đơn theo cùng 1 dãy hợp đồng 
    $ngaybatdau = Carbon::parse($request->ngaybatdau)->format('Y-m'); // Lấy năm-tháng từ ngày
     
    // Kiểm tra trùng lặp theo hopdong_id và tháng-năm
    $duplicateHoaDon = hoadon::where('hopdong_id', $request->hopdong_id)
        ->whereRaw('DATE_FORMAT(created_at, "%Y-%m") = ?', [$ngaybatdau])
        ->exists();
    
    // Nếu hóa đơn đã tồn tại cho tháng này, trả về thông báo lỗi
    if ($duplicateHoaDon) {
        return redirect()->back()->withErrors(['ngaybatdau' => 'Đã tồn tại hóa đơn cho phòng trọ này trong tháng hiện tại!']);
    }

    // Chuyển đổi tongtien từ định dạng VNĐ (VD: 5.560.000) thành số (VD: 5560000)
    $tongtien = str_replace('.', '', $request->tongtien); // Loại bỏ dấu chấm
    $tongtien = floatval($tongtien); // Chuyển thành số

    // Thêm vào trong hóa đơn
    $hoadon = hoadon::create([
        'dichvu_id' => $request->dichvu_id,
        'hopdong_id' => $request->hopdong_id,
        'sodiencu' => $request->diencu,
        'sodienmoi' => $request->dienmoi,
        'sonuoccu' => $request->nuoccu,
        'sonuocmoi' => $request->nuocmoi,
        'tongtien' => $tongtien, // Sử dụng giá trị đã chuyển đổi
        'status'   => $request->status, 
    ]);

    $hoadon->dichvus()->attach($request->dichvu_id);

    $hoadon->save();

    flash()->option('position', 'top-center')->timeout(2000)->success('Đã thêm hóa đơn thành công!');
    
    return redirect()->route('HoaDon.view');
}

    public function delete($id)
    {   
        $hoadon = hoadon::findOrFail($id);
        $hoadon->delete();
        flash()->option('position', 'top-center')->timeout(2000)->success('Đã xóa hóa đơn thành công!');
        return redirect()->back();    
    }


    public function search(Request $request)
    {
         $searchValue = str_replace(',', '', $request->input('query'));
         $chutro_id = session('chutro_id');
         $condition = session()->has('user_type') ? 'quanly_id' : 'chutro_id';
     
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
                 'dichvus'
             ])
             ->paginate(10);
     
         return view('pages.hoadon', compact('hoadons'));
    }




    public function viewUpdate($id)
    {   
       
        $chutro_id = session('chutro_id');
    
        if (session()->has('user_type')) 
        {
           
            $quanly = quanly::where('id', $chutro_id)->first();
            $chutro_id = $quanly->chutro_id; // Lấy ra chutro_id dựa trên id của quanly 
        }

          $dichvu = DichVu::where('chutro_id', $chutro_id)
        ->orderByDesc('id')
        ->get()
        ->unique('daytro_id')
        ->keyBy('daytro_id');

        // Bắt đầu từ HopDong, lấy ra tất cả hợp đồng có liên kết với khách thuê, phòng trọ và dãy trọ
        $hopdongs = HopDong::whereHas('khachthue_phongtro.phongtro.daytro', function ($query) use ($chutro_id) {
                $query->where('chutro_id', $chutro_id);
            })
            ->with([
                'khachthue_phongtro.khachthue',  // Lấy khách thuê liên quan
                'khachthue_phongtro.phongtro.daytro' // Lấy phòng trọ và dãy trọ liên quan
            ])
            ->get();

        
        
        // Lấy các dãy trọ từ danh sách hợp đồng
        $daytros = $hopdongs->pluck('khachthue_phongtro.phongtro.daytro')->unique('id');
        
        // Lấy các phòng trọ từ danh sách hợp đồng
        $phongtros = $hopdongs->pluck('khachthue_phongtro.phongtro')->unique('id');
       
        // Lấy các khách thuê từ danh sách hợp đồng
        $khachthues = $hopdongs->pluck('khachthue_phongtro.khachthue')->unique('id');
        
        // Tìm giá trị tiền nước lớn nhất nhóm theo `hopdong_id` => Theo dichvu thuộc vào chutro đang login  
        $maxSonuocMoi = HoaDon::selectRaw('MAX(sonuocmoi) as max_sonuocmoi, hopdong_id')
        ->whereHas('dichvus', function($query) use ($chutro_id) {
            $query->where('chutro_id', $chutro_id);
        })
        ->groupBy('hopdong_id')
        ->get();
    
        $maxSodienMoi = HoaDon::selectRaw('MAX(sodienmoi) as max_sodienmoi, hopdong_id')
        ->whereHas('dichvus', function($query) use ($chutro_id) {
            $query->where('chutro_id', $chutro_id);
        })
        ->groupBy('hopdong_id')
        ->get();
    
        $khachthue_phongtro = khachthue_phongtro::all();
       

        // Tạo tiền đề cho hiện thị data hiện tại lúc user nhấn vào 
        $hoadonUpdate = Hoadon::with([
            'hopdong.khachthue_phongtro.phongtro.daytro', // Lấy thông tin phòng trọ và dãy trọ
        ])->findOrFail($id); // Tìm hóa đơn hoặc trả về lỗi nếu không tồn tại
        
        // Xóa session đã nếu có lưu từ trước 
        session()->forget('hoadonTiepTheo');
        
       
        // Tạo tiền đề cho phép auto update bên kia => Update lại các giá trị sửa lại ở hoadon hiện tại => update lại hoadon tiếp theo liền kề nó vì giá trị mới của nó phải lấy từ hoadon hiện tại
        $HoadonHienDai = Hoadon::findorfail($id);

        $hopdongIdHienTai = $HoadonHienDai->hopdong_id;
           
        $sodienmoiHienTai = $HoadonHienDai->sodienmoi;
        
        $hoadonTiepTheo = Hoadon::where('hopdong_id', $hopdongIdHienTai) // Điều kiện lọc theo hopdong_id
        ->where('sodienmoi', '>', $sodienmoiHienTai) // Lọc các giá trị lớn hơn số hiện tại
        ->orderBy('sodienmoi', 'asc') // Sắp xếp theo thứ tự tăng dần
        ->first(); // Lấy đối tượng bảng nghi đầu tiên thỏa mãn điều kiện
        
        // dd($hoadonTiepTheo); 
            
        // Xử lí nếu bản thân hóa đơn này có thông số đã là giá trị lớn nhất trong mảng thì sao 
        if(!$hoadonTiepTheo)
        {       
            // Trường xử lí cho khi bị NULL => Ko cần phải update cho các hóa đơn tiếp theo vì nó đã là lớn nhất rồi 
            session()->put('maxValue', true); // Lưu giá trị true
            session()->put('hoadonTiepTheo', $HoadonHienDai); // Nhưng vẫn cần đảm bảo các thông số < số tiếp theo trong DB 
        }
        else 
        {  
            // Lưu collection vào session => Phải update cho hóa đơn tiếp theo 
            session()->put('maxValue', false);
            session()->put('hoadonTiepTheo', $hoadonTiepTheo);
        }

      
   
        return view('pages.updateHoaDon', compact(
            'daytros', 
            'phongtros', 
            'khachthues',
            'maxSonuocMoi',
            'hopdongs',
            'maxSodienMoi',
            'khachthue_phongtro',
            'dichvu',
            'hoadonUpdate'
        ));

    }




    public function update(Request $request, $id)
    {   
        $ngaybatdau = Carbon::parse($request->ngaybatdau)->format('Y-m'); // Lấy năm-tháng từ ngày
       
        // Recall lại data đã lưu 
        $hoadonTiepTheoId = session('hoadonTiepTheo'); 
        $maxValue  =        session('maxValue'); // Xử khi nó là true 
        // Nó giúp các giá trị nhập vào không được phép > số điện mới khác ( vì như vậy sẽ phá vỡ quy tắc )
        
      
        $hoadonTiepTheo = hoadon::findorfail($hoadonTiepTheoId->id);
        
        // dd($hoadonTiepTheo);

        // Update các thông số của hoadon tiep của hoadon hiện tại vì nếu ko thì sẽ vi phạm nguyện tắc các thông số tiếp theo của hoadon ko dc lấy dựa trên thông số hiện tại đang update cho hoadon này
        $sodienmoiTiepTheo =  $hoadonTiepTheo->sodienmoi;

        $sonuocmoiTiepTheo = $hoadonTiepTheo->sonuocmoi;
       
        $sodiencuTiepTheo =  $hoadonTiepTheo->sodiencu;

        $sonuoccuTiepTheo = $hoadonTiepTheo->sonuoccu;

        // Lấy thông tin hóa đơn cần update 
        $hoadon = Hoadon::findOrFail($id);
       
        // Bảng giá dịch vụ hiện tại 
        $dichvu = dichvu::where('id', $request->dichvu_id)->first();
               
        // Số điện mới hiện tại  
        $sodienmoiHienTai = $request->dienmoi;

        // Số nước mới hiện tại
        $sonuocmoiHienTai = $request->nuocmoi;
       
        // Cái này để đảm bảo các thông số hiện tại nhập < thông số của hóa đơn tiếp theo của nó 
        if($sodienmoiHienTai > $sodienmoiTiepTheo)
        {
            // Thông báo lỗi
            return redirect()->back()->withErrors(['dienmoi' => 'Số điện mới hiện tại cần nhỏ hơn '.$sodienmoiTiepTheo]);
        }
        
        if($sonuocmoiHienTai > $sonuocmoiTiepTheo)
        {
            // Thông báo lỗi
            return redirect()->back()->withErrors(['nuocmoi' => 'Số nước mới hiện tại cần nhỏ hơn '.$sodienmoiTiepTheo]);
        }


        // // Kiểm tra trùng lặp theo hopdong_id và tháng-năm
        // $duplicateHoaDon = hoadon::where('hopdong_id', $request->hopdong_id)
        //     ->whereRaw('DATE_FORMAT(created_at, "%Y-%m") = ?', [$ngaybatdau])
        //     ->exists();
    
        // // Nếu hóa đơn đã tồn tại cho tháng này, trả về thông báo lỗi
        // if ($duplicateHoaDon) {
        //     return redirect()->back()->withErrors(['ngaybatdau' => 'Hóa đơn cho tháng này đã tồn tại cho hợp đồng này!']);
        // }

            // Update tổng tiền mới cho hóa đơn tiếp theo => Vì thông số thay đổi nên ta cần phải update lại tổng tiền cho hoadon tiep theo đó
            if($maxValue == false)
            {
                
               $tongsocantru = ($sodienmoiTiepTheo - $sodiencuTiepTheo) * $dichvu->dien + ($sonuocmoiTiepTheo - $sonuoccuTiepTheo) * $dichvu->nuoc;
               
               $tongsosautruTiepTheo = $hoadonTiepTheo->tongtien - $tongsocantru;
              
               $tongsomoiTiepTheo = $tongsosautruTiepTheo + ($sodienmoiTiepTheo - $sodienmoiHienTai) * $dichvu->dien + ($sonuocmoiTiepTheo  - $sonuocmoiHienTai) * $dichvu->nuoc;
            
               $hoadonTiepTheo->tongtien = $tongsomoiTiepTheo ;
   
               // Update tổng tiền cho hóa đơn hiện tại
               $tongtienHienTai = $request->tongtien;
               $hoadon->tongtien = $tongtienHienTai;
               
               // Update thông tin vào bảng nghi mới 
               
               $hoadonTiepTheo->sodiencu = $sodienmoiHienTai;
               $hoadonTiepTheo->sonuoccu = $sonuocmoiHienTai;
   
               // $hoadonTiepTheo->tongtien = $tongsomoi;
               $hoadonTiepTheo->save();
   
               // Lưu thông tin vào bảng nghi hiện tại
               $hoadon->sodienmoi = $sodienmoiHienTai;
               $hoadon->sonuocmoi  = $sonuocmoiHienTai;
                 
               $hoadon->save();
            }

            $hoadon->created_at  = $request->ngaybatdau;
            
            
            $hoadon->save();
            
            flash()->option('position', 'top-center')->timeout(2000)->success('Đã cập nhật hóa đơn thành công!');
        
        return redirect()->route('HoaDon.view');

            
    }



    public function updateStatus(Request $request, $id)
    {
        // Lấy thông tin hóa đơn cần update 
        $hoadon = Hoadon::findOrFail($id);
        $hoadon->status =  $request->input('status');
        $hoadon->save();
        flash()->option('position', 'top-center')->timeout(2000)->success('Đã cập nhật trạng thái thành công!');
        return redirect()->route('HoaDon.view');
    }
    
}
