<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\daytro;
use App\Models\khachthue;
use App\Models\phongtro;
use App\Models\khachthue_phongtro;
use App\Models\quanly;
use App\Models\hoadon;
use App\Models\DichVu;
use App\Models\hopdong;
use Illuminate\Cache\Console\ForgetCommand;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;
class KhachThueController extends Controller
{
    
    public function __construct() {
        header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
        header("Pragma: no-cache"); // HTTP 1.0.
        header("Expires: 0"); // Proxies.
        header('Access-Control-Allow-Origin: *');     
    }
    // Khách thuê theo dãy trọ 
    public function show($id)
    {

        // Lấy từ phòng trọ -> khách thuê
        $phongtro = Phongtro::with('khachthues')->findOrFail($id);
        
         // Kiểm tra số lượng khách thuê trong phòng trọ
        $khachthueCount = $phongtro->khachthues->count();
        $phongtro->status = $khachthueCount > 0 ? 1 : 0;
        $phongtro->save();
        // Lấy ra tất cả các khách thuê từ phòng trọ KHÁC (trừ ra phòng trọ ta đang đứng)
        
        
        // Tách phòng trọ ra thành biến khách thuê => khách thuê đang tồn trong page đang đứng
         // Phân trang khách thuê
        $khachthues = $phongtro->khachthues()->paginate(5);  

        // Tách phòng trọ 
        $khachthue_ids = $khachthues->pluck('id')->toArray();
        //dd($khachthue_ids);
        $khachthuekhac = KhachThue::whereNotIn('id', $khachthue_ids)->get();
        // dd($khachthuekhac);
        // Trả về view 

        // Gọi lại URL đã lưu 
        $prevUrl = session('prev_url');
        // dump($prevUrl);

        return view('pages.khachthue', compact('phongtro','khachthues' ,'khachthuekhac', 'prevUrl'))->with('khachthuetongquat', false);
    }

    public function stored(Request $request)
    {
        
        $chutro_id = session('chutro_id'); // fix cái này
      
        if (session()->has('user_type')) // Nếu là quản lý 
        {
            $quanly = quanly::where('id', $chutro_id)->first();
            $chutro_id = $quanly->chutro_id; // Lấy ra chutro_id dựa trên id của quanly 
        }

        $request->merge([
            'tenkhachthue' => trim($request->tenkhachthue),
        ]);
    
        
        // Đai diện cho phòng hiện tại 
        $phongTro = PhongTro::findOrFail($request->input('phongtro_id')); // Lấy ra các phòng hiện tại với id cụ thể (id=1)

        // Check coi phòng chuận bị thêm có tồn tại trong phòng trọ hiện tại chưa 
        $existsInRoom = $phongTro->khachthues()->where('tenkhachthue', $request->tenkhachthue)->exists(); // Lấy từ phòng trọ hiện tại ra khách thuê để so sách với input 
       

        // Kiểm tra coi tên khách thuê có tồn tại trong table Cha Khách Thuê ko 
        $khachthue = KhachThue::where('tenkhachthue', $request->tenkhachthue)->first();
        
        // Kiểm tra coi các thông tin khác có trùng khớp hay không  
        $thongtinkhachthue = KhachThue::where($request->only(['sodienthoai', 'ngaysinh', 'cccd', 'gioitinh']))->first();

    
        // Nếu data tồn tại trong phòng trọ này  
        // Trùng tên trong table nhưng lại khác các thông tin khác 
        if($existsInRoom or ($khachthue and !$thongtinkhachthue))
        {
            return redirect()->back()->withErrors(['tenkhachthue' => 'Tên khách thuê đã tồn tại trong phòng hiện tại.']);   
        }
        
       
        // Tên khách thuê không trùng với tên trong table
        if(!$khachthue)
        {
            // Nếu ko trùng tên nhưng có thông tin khác trùng thì với thông tin khác của Table 
            $request->validate([
                'sodienthoai' => 'required|numeric|digits_between:5,12|unique:khachthue',
                'ngaysinh'=>'required|date|',
                'cccd'    => 'required|max:25|unique:khachthue',
                ],
                [
                    'sodienthoai.numeric' => 'Số điện thoại phải là số.',
                    'tenkhachthue.max' => 'Tên khách thuê không được quá 25 ký tự.',
                    'sodienthoai.digits_between' => 'Số điện thoại phải từ 5 đến 12 chữ số.',
                    'cccd.max' => 'CCCD không được quá 25 ký tự.',
                    'cccd.unique' => 'CCCD này đã được đăng kí với khách thuê khác. Vui lòng chọn CCCD khác',
                    'sodienthoai.unique' => 'Số điện thoại đã được sài. Vui lòng chọn số khác'
                ]);

            // Tạo mới bảng nghi trong table khách thuê (ko có thông tin khác trùng)
            $khachthue = KhachThue::create([
                'tenkhachthue' => $request->tenkhachthue,
                'sodienthoai' => $request->sodienthoai,
                'ngaysinh' => $request->ngaysinh,
                'gioitinh' => $request->gioitinh,
                'cccd' => $request->cccd,
                'chutro_id' => $chutro_id, 
                ]);
        }

        // Gán khách thuê vào phòng trọ thông qua bảng phụ (pivot)
        PhongTro::findOrFail($request->input('phongtro_id'))
        ->khachthues()
        ->attach($khachthue->id); // Giờ đã có bảng nghi trong table thì lấy id của nó ra 
       
        // Thông báo thành công 
        flash()->option('position', 'top-center')->timeout(1000)->success('Đã thêm khách thuê thành công');
                

        return redirect()->back();

    }



    public function delete($phongtro_id, $khachthue_id)
    {
        // Xóa dựa trên tòa độ cụ thể của phòng trọ + khach thuê trong phòng trọ 
        $khachthue_phongtro = khachthue_phongtro::where('phongtro_id', $phongtro_id)
                                            ->where('khachthue_id', $khachthue_id)
                                            ->first();
        $khachthue_phongtro->delete();

        // Hiện thị thông báo thành công 
        flash()->option('position', 'top-center')->timeout(1000)->success('Đã xóa phòng trọ thành công!');

        return redirect()->back();
 
    }


    public function search(Request $request)
    {

        // Lấy giá trị tìm kiếm từ request
        $searchValue = $request->input('query');

        // Tìm phòng trọ theo ID
        $phongtro = PhongTro::find($request->input('phongtro_id'));

        // Tách phòng trọ ra thành biến khách thuê => Khách thuê đang tồn trong page đang đứng
        $khachthues = $phongtro->khachthues;

        // Tách phòng trọ 
        $khachthue_ids = $khachthues->pluck('id')->toArray();
        $khachthuekhac = KhachThue::whereNotIn('id', $khachthue_ids)->get();
         

        // Tìm kiếm khách thuê trong phòng trọ theo giá trị tìm kiếm
        $khachthues = $phongtro->khachthues()
        ->where(function ($query) use ($searchValue) {
            $query->where('tenkhachthue', 'LIKE', "%{$searchValue}%")
                  ->orWhere('sodienthoai', 'LIKE', "%{$searchValue}%")
                  ->orWhere('ngaysinh', 'LIKE', "%{$searchValue}%")
                  ->orWhere('cccd', 'LIKE', "%{$searchValue}%")
                  ->orWhere('ngaysinh', 'LIKE', "%{$searchValue}%");
    
            // Handle the gender search separately to avoid conflicts
            if (strtolower($searchValue) === 'nam') {
                $query->orWhere('gioitinh', 0);
            } elseif (strtolower($searchValue) === 'nữ') {
                $query->orWhere('gioitinh', 1);
            }
        })
        ->paginate(5);
         // Phân trang kết quả

        // Trả về view với kết quả tìm kiếm
        return view('pages.khachthue', [
            'phongtro' => $phongtro,
            'khachthues' => $khachthues,
            'khachthuekhac' => $khachthuekhac
        ])->with('khachthuetongquat', false); // Thêm để ko bị lỗi searching 
    }



    public function update(Request $request, $id)
    {
      
     // Tìm ra khách thuê cần được update vào 
     $khachthue = KhachThue::find($id);
   
     $validator = Validator::make($request->all(),
     [
         'tenkhachthue' => 'required|string|max:255|unique:khachthue,tenkhachthue,'.$id,
         'sodienthoai'  => 'required|numeric|digits_between:5,12|unique:khachthue,sodienthoai,'.$id,
         'cccd'         =>  'required|max:25|unique:khachthue,cccd,'.$id,
     ], 
         [
            'tenkhachthue.unique' => 'Tên khách thuê vừa cập nhật đã tồn tại',
            'sodienthoai.unique' => 'Số điện thoại vừa cập nhật đã tồn tại',
            'cccd'               => 'Căn cước công dân vừa cập nhật đã tồn tại',
            'sodienthoai.numeric' => 'Số điện thoại phải là số.',
            'tenkhachthue.max' => 'Tên khách thuê không được quá 25 ký tự.',
            'sodienthoai.digits_between' => 'Số điện thoại phải từ 5 đến 12 chữ số.',
            'cccd.max' => 'CCCD không được quá 25 ký tự.',
        ]);

        if ($validator->fails())
        {
            return redirect()->back()->withErrors($validator, 'update_errors_' . $id)->withInput();
        } 
        
     
         // Update the tenant's information in the database
         $khachthue->update($request->all());
     
         // Flash a success message and redirect back
         flash()->option('position', 'top-center')->timeout(1000)->success('Đã cập nhật phòng trọ thành công!');
     
         return redirect()->back();

   
    }



    // Khách thuê tổng quát
    public function view()  
    {
        
        $chutro_id = session('chutro_id'); // Biến đa hình 
        // dd($chutro_id);

        if(session()->has('user_type'))
        {
            $quanly = quanly::where('id', $chutro_id)->first();
            $chutro_id =  $quanly->chutro_id;
        }

        $khachthues = KhachThue::where('chutro_id', $chutro_id)
         ->withCount('phongtros') // Đếm số lượng phòng trọ đã thuê (từ bảng pivot)
         ->paginate(10); // Phân trang kết quả
        // Lấy URL để có thể quay lại trang 

       return view('pages.khachthue',compact('khachthues'))->with('khachthuetongquat', true); 
    }


    public function destroy($khachthue_id)
    {
    // Tìm khách thuê theo ID
    $khachthue = KhachThue::findOrFail($khachthue_id);
    
    // Xóa khách thuê
    $khachthue->delete();

    // Hiện thị thông báo thành công 
    flash()->option('position', 'top-center')->timeout(1000)->success('Đã xóa phòng trọ thành công!');

    return redirect()->back();
    }


    public function add(Request $request)
   {
   
    $request->validate([
      'sodienthoai' => 'required|numeric|digits_between:5,12|unique:khachthue',
      'ngaysinh'=>'required|date|',
      'cccd'    => 'required|max:25|unique:khachthue',
      ],
      [
          'sodienthoai.numeric' => 'Số điện thoại phải là số.',
          'tenkhachthue.max' => 'Tên khách thuê không được quá 25 ký tự.',
          'sodienthoai.digits_between' => 'Số điện thoại phải từ 5 đến 12 chữ số.',
          'cccd.max' => 'CCCD không được quá 25 ký tự.',
          'cccd.unique' => 'CCCD này đã được đăng kí. Vui lòng chọn CCCD khác',
          'sodienthoai.unique' => 'Số điện thoại đã được sài. Vui lòng chọn số khác'
      ]);
    
    // Lấy chutro_id từ session
    $chutro_id = session('chutro_id');
    
    if (session()->has('user_type')) 
    {
      
        $quanly = quanly::where('id', $chutro_id)->first();
        $chutro_id = $quanly->chutro_id; // Lấy ra chutro_id dựa trên id của quanly 
    }

    $data = $request->all();
    $data['chutro_id'] = $chutro_id;

    // Đưa khách thuê vào DB tổng quát
    $khachThue = KhachThue::create($data);
      
    // Lưu khách thuê vào cơ sở dữ liệu
    $khachThue->save();
  
    flash()->option('position', 'top-center')->timeout(1000)->success('Đã thêm khách thuê thành công');
  
    return redirect()->back();
  
   }
   
   

   public function searching(Request $request)
   {
       $searchValue = $request->input('query');
   
       // Validate the search input
       $request->validate([
           'query' => 'nullable|string|max:255',
       ]);
   
       // Create the query builder for KhachThue
       $query = KhachThue::query();
   
       // General search on name, phone number, and CCCD
       $query->where('tenkhachthue', 'LIKE', "%{$searchValue}%")
           ->orWhere('sodienthoai', 'LIKE', "%{$searchValue}%")
           ->orWhere('cccd', 'LIKE', "%{$searchValue}%");
   
       // Special case: gender search based on the keywords "nam" or "nữ"
       if (strtolower($searchValue) === 'nam') {
           $query->orWhere('gioitinh', 0);
       } elseif (strtolower($searchValue) === 'nữ') {
           $query->orWhere('gioitinh', 1);
       }
   
       // Execute the query with pagination
       $khachthues = $query->paginate(5);
   
       // Return only the search results view
       return view('pages.khachthue',compact('khachthues'))->with('khachthuetongquat', true); 
   }
   

   public function khachthueDashboard()
   {
    
       $khachthueId = session('khachthue_id');

        // Tải thêm mối quan hệ 'chutro' khi tìm khách thuê
       $khachThue = KhachThue::with('phongtros.khachthues', 'phongtros.daytro.quanly', 'chutro')->findOrFail($khachthueId);
        $chuTroEmail = $khachThue->chutro->email ?? 'Không có thông tin Chủ trọ';

       $groupedByDaytro = $khachThue->phongtros->groupBy('daytro.tendaytro');

       $currentPage = request()->get('page', 1);
       $perPage = 5; // Number of dãy trọ per page

       // Get the keys (dãy trọ names)
       $daytroKeys = $groupedByDaytro->keys()->toArray();

       // Slice the keys based on the current page
       $slicedKeys = array_slice($daytroKeys, ($currentPage - 1) * $perPage, $perPage);

       // Create a new collection containing only the dãy trọ for the current page
       $paginatedGroupedByDaytro = new Collection();
       foreach ($slicedKeys as $key) {
           $paginatedGroupedByDaytro->put($key, $groupedByDaytro->get($key));
       }
      
       $quanLys = $khachThue->phongtros
        ->map(fn($phong) => $phong->daytro->quanly) // lấy trực tiếp object
        ->filter()                                 // bỏ các giá trị null
        ->unique('id')                             // loại trùng theo id
        ->values();                                // reset key

       $paginator = new LengthAwarePaginator(
           $paginatedGroupedByDaytro,
           $groupedByDaytro->count(),
           $perPage,
           $currentPage,
           ['path' => request()->url()]
       );

    //    dd($chuTroEmail);
       
       return view('customer.dashboard', [
           'groupedByDaytro' => $paginator, // Pass the paginator of dãy trọ
           'khachThue' => $khachThue,
           'quanLys' => $quanLys, // thêm dòng này
           'chuTroEmail' => $chuTroEmail, // Thêm email của chủ trọ
       ]);
   }
  


    public function khachthueHoaDon($phongtro_id)
    {
        // Lấy phòng trọ
        $phong = PhongTro::with(['daytro'])->findOrFail($phongtro_id);

        // Lấy id khách thuê từ session (khách thuê đang đăng nhập)
        $khachthue_id = session('khachthue_id');

        // Tìm các hợp đồng của khách thuê này trong phòng trọ này
        $hopdongIds = HopDong::whereHas('khachthue_phongtro', function ($query) use ($khachthue_id, $phongtro_id) {
            $query->where('khachthue_id', $khachthue_id)
                  ->where('phongtro_id', $phongtro_id);
        })->pluck('id');

        // Lấy tất cả hóa đơn của hợp đồng này
        $hoadons = HoaDon::whereIn('hopdong_id', $hopdongIds)
            ->with([
                'hopdong.khachthue_phongtro.khachthue',
                'hopdong.khachthue_phongtro.phongtro.daytro'
            ])
            ->orderByDesc('created_at')
            ->paginate(5);

        // Lấy toàn bộ dịch vụ mới nhất theo từng dãy trọ
        $dichvus = DichVu::orderByDesc('id')
            ->get()
            ->unique('daytro_id')
            ->keyBy('daytro_id');

        // Gán dịch vụ cho từng hóa đơn
        foreach ($hoadons as $hoadon) {
            $daytroId = optional($hoadon->hopdong?->khachthue_phongtro?->phongtro?->daytro)->id;
            $hoadon->dv = $dichvus[$daytroId] ?? null;
        }

        return view('customer.hoadon', compact('hoadons', 'phong'));
    }


   public function sendEmail(Request $request)
{
    $request->validate([
        'recipient' => 'required|string',
        'subject' => 'required|string|max:255',
        'message' => 'required|string|max:1000',
    ]);

    $recipient = $request->input('recipient');
    $subject = 'Liên hệ từ khách thuê: ' . session('khachthue_name');

    if (str_starts_with($recipient, 'manager_')) // Nếu mã nhập có từ bắt đầu là 'manager_' 
    {
        $id = (int) str_replace('manager_', '', $recipient);
        // Gửi mail cho quản lý 
        $quanly = QuanLy::find($id);
        if (!$quanly || !$quanly->email) {
            return back()->withErrors(['recipient' => 'Không tìm thấy quản lý.']);
        }
        $toEmail = $quanly->email;

    } 
    elseif (str_starts_with($recipient, 'chutro_')) // Nếu mã nhập có từ bắt đầu là 'chutro_' 
    {
        
        $toEmail = str_replace('chutro_', '', $recipient); // Lấy email từ mã nhập bằng cách bỏ chữ chutro ra khỏi string 

        if (!filter_var($toEmail, FILTER_VALIDATE_EMAIL)) // Dùng  FILTER_VALIDATE_EMAIL để kiem tra dinh dang Emaail
        {
            dd(3);
            return back()->withErrors(['recipient' => 'Email chủ trọ không hợp lệ.']);
        }

    } 
    else // Nếu TH ko phải chủ trọ hay cả quản lý  
    {
        return back()->withErrors(['recipient' => 'Người nhận không hợp lệ.']);
    }

   
    // Gửi mail tới cho chủ trọ sau khi gán email vô ToEmail
    Mail::raw($request->message, function ($mail) use ($toEmail, $subject) 
    {
        $mail->to($toEmail)->subject($subject);
    });

    flash()->option('position', 'top-center')->timeout(2000)->success('Đã gửi email thành công!');

    return redirect()->back();
}

    public function profile()
    {
        $khachthueId = session('khachthue_id');
       
        $khachThue = KhachThue::findOrFail($khachthueId);

        // dd($khachThue);
        return view('customer.profile', compact('khachThue'));
    }
   

    public function updateProfile(Request $request, $id)
    {
      
        // Xác thực dữ liệu đầu vào từ form
        $request->validate([
            'tenkhachthue' => 'required|string|max:255',
            'sodienthoai' => 'nullable|string|max:20',
            'ngaysinh' => 'nullable|date',
            'gioitinh' => 'nullable|integer|in:0,1,2', // 0: Nam, 1: Nữ, 2: Khác
            'cccd' => 'nullable|string|max:20|unique:khachthue,cccd,' . $id, // Giữ nguyên validation dù là readonly ở view để bảo mật
        ]);

        // Tìm khách thuê theo ID
        $khachThue = KhachThue::findOrFail($id);

        // Kiểm tra bảo mật: Đảm bảo khách thuê đang đăng nhập có quyền cập nhật hồ sơ này
        // Đây là bước rất quan trọng để tránh người dùng thay đổi thông tin của người khác
        if (session('khachthue_id') != $khachThue->id) {
            return back()->with('error', 'Bạn không có quyền cập nhật hồ sơ này.');
        }

        // Cập nhật thông tin khách thuê
        $khachThue->tenkhachthue = $request->tenkhachthue;
        $khachThue->sodienthoai = $request->sodienthoai;
        $khachThue->ngaysinh = $request->ngaysinh;
        $khachThue->gioitinh = $request->gioitinh;
        // Lưu thay đổi vào cơ sở dữ liệu
        $khachThue->save();

       

        // Chuyển hướng trở lại trang profile với thông báo thành công
        flash()->option('position', 'top-center')->timeout(1000)->success('Đã thêm khách thuê thành công');

          return redirect()->back();
    }

}