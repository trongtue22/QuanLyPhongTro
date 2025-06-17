<?php

namespace App\Http\Controllers;
use App\Models\hopdong;
use App\Models\daytro;
use App\Models\phongtro;
use App\Models\quanly;
use App\Models\khachthue_phongtro;
use Illuminate\Http\Request;
use Carbon\Carbon;

class HopDongController extends Controller
{
    public function view()
{
    $chutro_id = session('chutro_id');
    $condition = session()->has('user_type') ? 'quanly_id' : 'chutro_id';

    $hopdongs = HopDong::whereHas('khachthue_phongtro.phongtro.daytro', function ($query) use ($chutro_id, $condition)
    {
        $query->where($condition, $chutro_id); // Biến $condition sẽ biến thiên tùy tình huống
    })
    ->with([
        'khachthue_phongtro.khachthue',
        'khachthue_phongtro.phongtro.daytro.chutro', // Đảm bảo load Chủ Trọ
        'khachthue_phongtro.phongtro.daytro.quanly' // THÊM DÒNG NÀY ĐỂ LOAD QUẢN LÝ
    ])
    ->paginate(10);

    return view('pages.hopdong', compact('hopdongs'));
}

    public function viewAdd()
    {
        // Lấy chutro_id từ session
        $chutro_id = session('chutro_id');
        // dd($chutro_id);
        // Lấy các dãy trọ liên quan đến chutro_id
        if (session()->has('user_type')) {
            // Nếu có user_type, sử dụng quanly_id để lấy dãy trọ
            $daytro_ids = DayTro::where('quanly_id', $chutro_id)->pluck('id');
           
        } else 
        {
            // Nếu không có user_type, sử dụng chutro_id để lấy dãy trọ
            $daytro_ids = DayTro::where('chutro_id', $chutro_id)->pluck('id');
        }
        // dd($daytro_ids);
        // Lấy các phòng trọ thuộc các dãy trọ này và có status = 1
        
        $phongtros = PhongTro::whereIn('daytro_id', $daytro_ids) // Điều kiện dãy trọ thuộc chutro_id
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

        // $phongtro = PhongTro::where('daytro_id',$daytro_ids)->pluck('id');
        // dd($phongtros);
        
        // dd($phongtros);
        // Trả về view với tất cả các phòng trọ và khách thuê
        return view('pages.addHopDong', compact('phongtros'));
    }
    

    public function add(Request $request)
    {
        // Thông báo lỗi
        $message = [
            'required' => 'Vui lòng nhập :attribute.',
            'integer' => 'Vui lòng nhập số.', // 
            'numeric' => 'Vui lòng nhập số hợp lệ.', // Thêm thông báo cho 'numeric'
            'min' => 'Không được nhập số âm.',
            'after' => 'Ngày kết thúc phải sau ngày bắt đầu.' // Sửa lại thông báo lỗi cho đúng nghĩa
        ];

        // Validate data trước khi thêm vào
        $request->validate([
            'phongtro' => 'required|exists:phongtro,id', // Kiểm tra phòng trọ phải tồn tại trong bảng 'phongtro'
            'khachthue' => 'required|exists:khachthue,id', // Kiểm tra khách thuê phải tồn tại trong bảng 'khachthue'
            'ngaybatdau' => 'required|date',
            'ngayketthuc' => 'required|date|after:ngaybatdau', // Ngày kết thúc phải sau ngày bắt đầu
            'tiencoc' => 'required|numeric|min:0', // Tiền cọc phải là số và không âm
            'songuoithue' => 'required|integer|min:1', // Số người thuê phải là số nguyên dương
            'soxe' => 'required|integer|min:0', // Số xe phải là số nguyên không âm
        ], $message);


        // Lấy ra các data liên quan đến Số Phòng 
        $phongtro_id = $request->input('phongtro');

        // Lấy ra các data liên quan đến Khách Thuê
        $khachthue_id = $request->input('khachthue');

        // Tìm bản ghi trong bảng trung gian khachthue_phongtro => Lấy ra nguyên 1 cái mảng chứa nguyên hàng của table đó 
        $khachthue_phongtro = khachthue_phongtro::where('phongtro_id', $phongtro_id)
        ->where('khachthue_id', $khachthue_id)
        ->first();

        // Lấy id từ mảng thu được từ trên 
        $khachthue_phongtro_id = $khachthue_phongtro->id;
        
        // Kiểm tra sự tồn tại trong bảng hopdong
        $exists = hopdong::where('khachthue_phongtro_id', $khachthue_phongtro_id)->exists();
    
        // Nếu tồn tại, trả về lỗi hoặc thông báo cho người dùng
        if ($exists) 
        {
            return redirect()->back()->withErrors(['sophong' => 'Phòng trọ này đã có hợp đồng.']);
        }

        // Đưa ID vừa có được + thông tin phụ vào Table HopDong

        $hopdong = hopdong::create([
            'khachthue_phongtro_id' => $khachthue_phongtro_id,
            'songuoithue' => $request->songuoithue,
            'ngaybatdau' => $request->ngaybatdau,
            'ngayketthuc' => $request->ngayketthuc,
            'tiencoc' => $request->tiencoc,
            'soxe' => $request->soxe,
        ]);

        $hopdong->save();
        
        flash()->option('position', 'top-center')->timeout(2000)->success('Hợp đồng được thêm thành công!');
        
        // Trả về thành công 
        return redirect()->route('HopDong.view');
    }



    public function delete($id)
    {
        $hopdong = hopdong::find($id);
        
        $hopdong->delete();

        flash()->option('position', 'top-center')->timeout(2000)->success('Đã xóa hợp đồng thành công!');

        return redirect()->back();
    }


    public function viewUpdate($id)
    {
         // Lấy chutro_id từ session
         $chutro_id = session('chutro_id');
         
         // Lấy ra hopdong user cần update => Làm tiền đề cho việc lấy lại các giá trị đã chọn để làm default 
         $hopdong = HopDong::findOrFail($id);
         
         // Lấy phòng trọ hiện tại của hợp đồng
         $currentPhongTroId = $hopdong->khachthue_phongtro->phongtro_id;  
         $currentKhachThueId = $hopdong->khachthue_phongtro->khachthue_id;
        
        
        if (session()->has('user_type')) 
        {
           
            $quanly = quanly::where('id', $chutro_id)->first();
            $chutro_id = $quanly->chutro_id; // Lấy ra chutro_id dựa trên id của quanly 
        }

         // Lấy các dãy trọ liên quan đến chutro_id
         $daytro_ids = DayTro::where('chutro_id', $chutro_id)->pluck('id');


         // Lấy các phòng trọ thuộc các dãy trọ này và có status = 1
         $phongtros = PhongTro::whereIn('daytro_id', $daytro_ids) // Điều kiện dãy trọ thuộc chutro_id
                             ->where('status', 1) // Chỉ lấy các phòng trọ có status = 1
                             ->where(function ($query) use ($currentPhongTroId) {
                                 $query->whereNotIn('id', function ($query) {
                                     $query->select('phongtro_id')
                                           ->from('khachthue_phongtro')
                                           ->whereIn('id', function ($query) {
                                               $query->select('khachthue_phongtro_id')
                                                     ->from('hopdong');
                                           });
                                 })
                                 ->orWhere('id', $currentPhongTroId); // Include the current room
                             })
                             ->with('khachthues') // Load khách thuê liên quan
                             ->get();
     
         // Retrieve the tenant and room associated with the contract
         $khachthuePhongTro = $hopdong->khachthue_phongtro;
                             
         // Trả về view với tất cả các phòng trọ và khách thuê
         return view('pages.updateHopDong', compact('hopdong', 'khachthuePhongTro', 'phongtros', 'currentPhongTroId','currentKhachThueId'));
    }


    public function update(Request $request, $id)
    {
        //  dd($request->all());
        $hopdong = hopdong::findOrFail($id);

        // Thông báo lỗi
        $message = [
            'required' => 'Vui lòng nhập :attribute.',
            'integer' => 'Vui lòng nhập số.', // 
            'numeric' => 'Vui lòng nhập số hợp lệ.', // Thêm thông báo cho 'numeric'
            'min' => 'Không được nhập số âm.',
            'after' => 'Ngày kết thúc phải sau ngày bắt đầu.' // Sửa lại thông báo lỗi cho đúng nghĩa
        ];

        // Validate data trước khi thêm vào
        $request->validate([
            'khachthue' => 'required|exists:khachthue,id', // Kiểm tra khách thuê phải tồn tại trong bảng 'khachthue'
            'ngaybatdau' => 'required|date',
            'ngayketthuc' => 'required|date|after:ngaybatdau', // Ngày kết thúc phải sau ngày bắt đầu
            'tiencoc' => 'required|numeric|min:0', // Tiền cọc phải là số và không âm
            'songuoithue' => 'required|integer|min:1', // Số người thuê phải là số nguyên dương
            'soxe' => 'required|integer|min:0', // Số xe phải là số nguyên không âm
        ], $message);

        $data = $request->all();
        
        $hopdong->update($data);

        flash()->option('position', 'top-center')->timeout(2000)->success('Hợp đồng được cập nhật thành công!');
        
        // Trả về thành công 
        return redirect()->route('HopDong.view');
    }


    public function search(Request $request)
    {
        // Lấy giá trị tìm kiếm từ request
        $searchValue = $request->input('query');
        
        // Lấy chutro_id từ session và xác định điều kiện tìm kiếm theo user_type
        $chutro_id = session('chutro_id');
        $condition = session()->has('user_type') ? 'quanly_id' : 'chutro_id';

        // Truy vấn để tìm kiếm các hợp đồng dựa trên các điều kiện tìm kiếm và điều kiện người dùng
        $hopdongs = HopDong::whereHas('khachthue_phongtro.phongtro.daytro', function ($query) use ($chutro_id, $condition) {
                // Ràng buộc đầu tiên: chỉ lấy hợp đồng thuộc về chutro_id hoặc quanly_id hiện tại
                $query->where($condition, $chutro_id);
            })
            ->where(function ($query) use ($searchValue) {
                // Thêm các điều kiện tìm kiếm chi tiết
                $query->whereHas('khachthue_phongtro', function ($q) use ($searchValue) 
                {
                    $q->whereHas('khachthue', function ($subQuery) use ($searchValue)
                    {
                        $subQuery->where('tenkhachthue', 'LIKE', "%{$searchValue}%");
                    })->orWhereHas('phongtro', function ($subQuery) use ($searchValue) 
                    {
                        $subQuery->where('sophong', 'LIKE', "%{$searchValue}%")
                                 ->orWhereHas('daytro', function ($subQuery) use ($searchValue) 
                                 {
                                     $subQuery->where('tendaytro', 'LIKE', "%{$searchValue}%");
                                 });
                    });
                })
                ->orWhere('songuoithue', 'LIKE', "%{$searchValue}%")
                ->orWhereDate('ngaybatdau', '=', $searchValue) // Tìm chính xác ngày bắt đầu
                ->orWhereDate('ngayketthuc', '=', $searchValue) // Tìm chính xác ngày kết thúc
                ->orWhere('tiencoc', 'LIKE', "%{$searchValue}%");
            })
            ->with([
                'khachthue_phongtro.khachthue',  // Load thông tin khách thuê
                'khachthue_phongtro.phongtro.daytro' // Load thông tin phòng trọ và dãy trọ
            ])
            ->paginate(10);

        // Trả về view kết quả tìm kiếm
        return view('pages.hopdong', compact('hopdongs'));
    }
    
    
    







    
}
