<?php

namespace App\Http\Controllers;
use App\Models\daytro;
use App\Models\phongtro;
use App\Models\khachthue;
use App\Models\khachthue_phongtro;
use App\Models\quanly;
use Illuminate\Validation\Rule;
use Flasher\Laravel\Facade\Flasher;
use Flasher\Prime\Test\FlasherAssert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
class PhongTroController extends Controller
{
    public function __construct() {
        header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
        header("Pragma: no-cache"); // HTTP 1.0.
        header("Expires: 0"); // Proxies.
        header('Access-Control-Allow-Origin: *');     
    }
    // Phòng trọ theo Dãy Trọ
    public function show($id)
    {   
        // GỌi ra các Phòng Trọ liên quan đến Dãy Trọ theo ID 
        $daytro = DayTro::with(['phongtros.sucophongtro'])->findOrFail($id);

        // Adjust the number 10 as needed
        $phongtros = $daytro->phongtros()->paginate(5);

        $khachthues = $daytro->phongtros->flatMap->khachthues;
        
        // Lưu các URL vào bên trong Session 
        session_start();
        session(['prev_url' => url()->current()]);
        
        foreach ($daytro->phongtros as $phongtro) {
            $khachthueCount = $phongtro->khachthues->count();
            // Lưu trạng thái của các phòng có khách thuê lại 
            if ($khachthueCount > 0) {
                $phongtro->status = 1; // Đã thuê
            } else {
                $phongtro->status = 0; // Phòng trống
            }
            
            // Lưu lại trạng thái cập nhật
            $phongtro->save();
        }
        
        // Trả về view 
        // with('showContent', true): hiện thị data phongtro chi tiết
        return view('pages.phongtro', compact('daytro', 'phongtros'))->with('showContent', false)->with('khachthuePhongTro',false);;
    }   



    public function stored(Request $request)
    {
            $request->validate([
                'sophong' => 'required|string',
                'tienphong' => 'required|numeric|min:0',
            ], [
                'sophong.required' => 'Trường số phòng là bắt buộc',
                'sophong.string' => 'Trường số phòng phải là chuỗi (có thể chứa chữ và số)',
                'tienphong.required' => 'Trường tiền phòng là bắt buộc',
                'tienphong.numeric' => 'Trường tiền phòng chỉ được phép chứa số',
                'tienphong.min' => 'Không được nhập số âm',
            ]);
        
            // Kiểm tra trùng số phòng trong cùng một dãy trọ
            $exists = PhongTro::where('daytro_id', $request->daytro)
                ->where('sophong', $request->sophong)
                ->exists();
        
            if ($exists) {
                return back()->withErrors(['sophong' => 'Số phòng đã tồn tại trong dãy trọ này'])->withInput();
            }
        
            // Tạo mới phòng trọ
            PhongTro::create([
                'daytro_id' => $request->daytro,
                'sophong' => $request->sophong,
                'tienphong' => $request->tienphong,
            ]);
        
            flash()->option('position', 'top-center')->timeout(1000)->success('Đã thêm phòng trọ thành công');
            return redirect()->back();
    }


    public function delete($id)
    {
        // Tìm phòng trọ để xoa
        $phongtro = phongtro::find($id);
        
        // Xóa phòng trọ
        $phongtro->delete();

        // Hiện thị thông báo thành công 
        flash()->option('position', 'top-center')->timeout(1000)->success('Đã xóa phòng trọ thành công!');

        return redirect()->back();
    }


    public function update(Request $request, $id)
    {
       
        // Tìm ra trường thông tin cần update => Lấy ra tập data 
        $phongtro = phongtro::findOrFail($id);
      
        // Nếu validate thấy vị phạm nó sẽ lưu error vào session và nhảy tới redirect() ngay lập tức 
        $validator = Validator::make($request->all(), 
        [
           'sophong' => 'required|string|max:255|unique:phongtro,sophong,' . $id,
           'tienphong' => 'required|numeric|min:0', // Sửa từ string thành numeric
        ],
        [
            'sophong.unique' => 'Số phòng vừa cập nhật đã tồn tại',
            'tienphong.min'  => 'Không được nhập số âm', // Thông báo lỗi khi giá trị nhỏ hơn 0
        ]);

        // Trở lại trang với session error 
        if ($validator->fails())
        {
            return redirect()->back()->withErrors($validator, 'update_errors_' . $id)->withInput();
        } 

        // Đưa tất cả các thông tin đã update vào DB để sửa
       $phongtro->update($request->all());
      
       // Hiện thị thông báo thành công 
       flash()->option('position', 'top-center')->timeout(1000)->success('Phòng trọ đã được cập nhật thành công!');

       return redirect()->back();
    }   

    
    public function search(Request $request)
    {   
        // Get the search value from the request
        // dd(1);
        $searchValue = $request->input('query');
        
        // Take out id of DayTro
        $daytroId = $request->input('daytro_id'); // Custom cái này theo tình hướng role => Vì nó chi phối cái phía dưới 
    
        $daytro = DayTro::find($daytroId);

       
        // Tìm kiếm dãy trọ dựa trên tên hoặc các trường khác có chứa giá trị 'queryValue'
        $phongtros = $daytro->phongtros()
        ->where('daytro_id', $daytroId) // Chỉ lấy ra các phòng trọ thuộc vào 'daytroId'
        ->where(function ($query) use ($searchValue) {
            $query->where(function ($q) use ($searchValue) {
                $q->where('sophong', 'LIKE', "%{$searchValue}%")
                  ->orWhere('tienphong', 'LIKE', "%{$searchValue}%")
                  ->orWhere('status', 'LIKE', "%{$searchValue}%");
            });
        })
        ->paginate(5);

        // CHỉ lấy các phòng trọ có dãy trọ liên quan đến id QL hiện tại 

        // Return the search view with the resluts compacted
        return view('pages.phongtro', compact('daytro', 'phongtros'))->with('showContent', false)->with('khachthuePhongTro',false);; 
    } 
    
    // Phòng trọ tổng quát
    public function view()
    {
        // Lấy chutro_id từ session
        $chutro_id = session('chutro_id');
       

        if(session()->has('user_type'))
        {
            // Hiện thị ra các dãy trọ thuộc về View Con này 
            $daytros = DayTro::where('quanly_id', $chutro_id)->get(); // Ảnh hướng đến view add luôn 
            
        }
        else
        {
            $daytros = DayTro::where('chutro_id', $chutro_id)->get();
        }

        $phongtros = PhongTro::whereIn('daytro_id', $daytros->pluck('id'))->with('daytro')->paginate(5);

        session_start();
        session(['prev_url' => url()->current()]);


        foreach ($phongtros as $phongtro) 
        {
            $khachthueCount = $phongtro->khachthues->count();
            
            // Lưu trạng thái của các phòng có khách thuê lại
            if ($khachthueCount > 0) 
            {
                $phongtro->status = 1; // Đã thuê
            } else {
                $phongtro->status = 0; // Phòng trống
            }
    
            // Lưu lại trạng thái cập nhật
            $phongtro->save();
        }
        
         // Tắt data bên phòng trọ chi tiết thông qua with(variable) truyền biến 
        return view('pages.phongtro',compact('phongtros','daytros'))->with('showContent', true)->with('khachthuePhongTro',false);

    }


    public function store(Request $request)
    {
        $request->validate([
            'sophong' => 'required|string|max:50|unique:phongtro,sophong',
            'tienphong' => 'required|numeric|min:0',
        ], [
            'sophong.required' => 'Trường số phòng là bắt buộc.',
            'sophong.string' => 'Số phòng phải là chuỗi ký tự.',
            'sophong.max' => 'Số phòng không được quá 50 ký tự.',
            'sophong.unique' => 'Số phòng đã tồn tại.',
            'tienphong.required' => 'Trường tiền phòng là bắt buộc.',
            'tienphong.numeric' => 'Trường tiền phòng chỉ được phép chứa số.',
            'tienphong.min' => 'Không được nhập số âm.',
        ]);
        
        // Lưu vào phòng trọ của bản thân nó => Nếu là QL thì nó đã cũng lưu các dãy trọ hợp lệ (do view add hiện thị toàn dãy trọ của view này)
        $phongtro = PhongTro::create($request->all());
       
        $phongtro->save();
       
        flash()->option('position', 'top-center')->timeout(1000)->success('Phòng trọ đã được thêm thành công!');

        return redirect()->back();
    }

    

    public function searching(Request $request)
    {
        $searchValue = $request->input('query');
       
        $chutro_id = session('chutro_id');

        if(session()->has('user_type'))
        {
            $daytros = DayTro::where('quanly_id', $chutro_id)->get();
            
        }
        else
        {
            $daytros = DayTro::where('chutro_id', $chutro_id)->get();
        }

        // Tìm kiếm trong bảng PhongTro và DayTro
        $phongtros = PhongTro::with('daytro')
        ->where(function ($query) use ($searchValue) {
            $query->where('sophong', 'LIKE', "%{$searchValue}%")
                  ->orWhere('tienphong', 'LIKE', "%{$searchValue}%")
                  ->orWhere('status', 'LIKE', "%{$searchValue}%");
        })
        ->orWhereHas('daytro', function ($query) use ($searchValue, $chutro_id) {  
            // Thêm điều kiện tìm kiếm trong tendaytro
            $query->where('tendaytro', 'LIKE', "%{$searchValue}%")
                  ->when(session()->has('user_type'), function ($q) use ($chutro_id) {
                      $q->where('quanly_id', $chutro_id);
                  }, function ($q) use ($chutro_id) {
                      $q->where('chutro_id', $chutro_id);
                  });
        })
        ->paginate(5);
        
        // Return the search view with the resluts compacted
        return view('pages.phongtro',compact('phongtros','daytros'))->with('showContent', true)->with('khachthuePhongTro',false);
    }



    // Phòng trọ của Khách Thuê  => Chỉ cho chọn mà thôi 
    public function index($id)
    {
        $chutro_id = session('chutro_id');

        $condition = session()->has('user_type') ? 'quanly_id' : 'chutro_id';

        if(session()->has('user_type'))
        {
            // Hiện thị ra các dãy trọ thuộc về View Con này 
            $daytro = DayTro::where('quanly_id', $chutro_id)->first();
        }
    
        // Tìm khách thuê theo ID và load các phòng trọ liên quan cùng dãy trọ
        $khachthue = KhachThue::with('phongtros.daytro') // Load phòng trọ và dãy trọ của các phòng đó
                         ->findOrFail($id);
         
      
         // Lấy ra danh sách các phòng trọ mà khách thuê đó đang thuê và phân trang => pivot table là ở đây => Dùng cho view 
         $phongtros = $khachthue->phongtros()
         ->whereHas('daytro', function($query) use ($condition, $chutro_id) {
             // Lọc các phòng trọ theo dãy trọ có quanly_id hoặc chutro_id đúng với điều kiện
             $query->where($condition, $chutro_id);
         })
         ->orderBy('id', 'asc')
         ->paginate(5);
        
          // Fetch all DayTro along with their PhongTros, excluding the ones already rented by KhachThue => Dùng cho add view 
          $daytros = DayTro::with(['phongtros' => function($query) use ($khachthue) {
            $query->whereDoesntHave('khachthues', function($q) use ($khachthue) {
                $q->where('khachthue_id', $khachthue->id);
            });
        }])
        ->where($condition, $chutro_id)  // Ảnh hướng đến view của bên chức năng add
        ->get();
    
        // dd($phongtros); 

         // Trả về view với các phòng trọ đã được load dãy trọ tương ứng
         return view('pages.phongtro', compact('phongtros','khachthue','daytros'))
                ->with('showContent', false)
                ->with('khachthuePhongTro', true);
    }


    public function add(Request $request)
    {
        $khachthue_id = $request->input('khachthue_id');

        $phongtro_id = $request->input('phongtro_id');
        
        // dd($phongtro_id);
       khachthue_phongtro::create([
            'khachthue_id' => $khachthue_id,
            'phongtro_id' => $phongtro_id,
            // Các trường khác cần thiết khác nếu có
        ]);

       // $khachthue_phongtro->save();

        flash()->option('position', 'top-center')->timeout(1000)->success('Thêm phòng trọ thành công!');

        return redirect()->back();

    }




    public function finding(Request $request)
    {
        $searchValue = $request->input('query');
        $khachthue_id = $request->input('khachthue_id');
    
        // Lấy `chutro_id` từ session
        $chutro_id = session('chutro_id');
    
        // Xác định cột điều kiện dựa trên `user_type`
        $condition = session()->has('user_type') ? 'quanly_id' : 'chutro_id';
    
        // Lấy danh sách dãy trọ thuộc về chủ trọ hiện tại hoặc quản lý
        $daytros = DayTro::where($condition, $chutro_id)->get();
    
        // Tìm khách thuê theo ID và load phòng trọ liên quan cùng dãy trọ
        $khachthue = KhachThue::with('phongtros.daytro')->findOrFail($khachthue_id);
    
        // Tìm kiếm các phòng trọ mà khách thuê đó đang thuê và áp dụng điều kiện
        $phongtros = $khachthue->phongtros()
            ->whereHas('daytro', function ($query) use ($condition, $chutro_id) {
                // Áp dụng điều kiện lọc dãy trọ theo `chutro_id` hoặc `quanly_id`
                $query->where($condition, $chutro_id);
            })
            ->where(function ($query) use ($searchValue) {
                // Thực hiện tìm kiếm các trường `sophong`, `tienphong`, `status` và `tendaytro`
                $query->where('sophong', 'LIKE', "%{$searchValue}%")
                      ->orWhere('tienphong', 'LIKE', "%{$searchValue}%")
                      ->orWhere('status', 'LIKE', "%{$searchValue}%")
                      ->orWhereHas('daytro', function ($query) use ($searchValue) {
                          $query->where('tendaytro', 'LIKE', "%{$searchValue}%");
                      });
            })
            ->orderBy('id', 'asc')
            ->paginate(5);  // Phân trang kết quả tìm kiếm
    
          // Trả về view với kết quả tìm kiếm
        return view('pages.phongtro', compact('phongtros', 'khachthue', 'daytros'))
        ->with('showContent', false)
        ->with('khachthuePhongTro', true);
    }



    public function destroy($khachthue_id, $phongtro_id)
    {
       $khachthue_phongtro = khachthue_phongtro::where('khachthue_id', $khachthue_id)
                                           ->where('phongtro_id', $phongtro_id)
                                           ->first();

      
        $khachthue_phongtro->delete();
        
        flash()->option('position', 'top-center')->timeout(1000)->success('Xóa phòng trọ thành công!');

        return redirect()->back();
             
    }



}
