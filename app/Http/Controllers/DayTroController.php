<?php

namespace App\Http\Controllers;
use App\Models\daytro;
use App\Models\quanly;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;

class DayTroController extends Controller
{
 
    public function __construct() {
        header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
        header("Pragma: no-cache"); // HTTP 1.0.
        header("Expires: 0"); // Proxies.
        header('Access-Control-Allow-Origin: *');     
    }
    
    // Hiện thị dãy trọ
    public function daytro()
    {
        // Lấy data ra ngoài để in ra màn hình
        

        $chutro_id = session('chutro_id'); // Biến đa hình (vừa là chutro vừa là quanly)
       
        if(session()->has('user_type'))
        {
            $daytros = DayTro::where('quanly_id', $chutro_id)->paginate(5);
        }else
        {
            $daytros = DayTro::where('chutro_id', $chutro_id)->paginate(5);
        }
        
        $quanlys = quanly::where('chutro_id', $chutro_id)->get(); // Chỉ có data nếu là role chutro
        
        $currentUrl = url()->current();

        // Lưu URL vào session
        Session::put('current_url', $currentUrl);

        $redirectUrl = Session::get('current_url');
        
        return view('pages.daytro', compact('daytros','quanlys'));
    }

    // Đưa thông tin dãy trọ vào DB 
    public function store(Request $request)
    {
        // Kiểm tra thông tin đầu vào
        $request->validate([
            'tendaytro' => 'required|string|max:255|unique:daytro',
            'tinh' => 'required|string|max:255',
            'huyen' => 'required|string|max:255',
            'xa' => 'required|string|max:255',
            'sonha' => 'required|string|max:255',
        ],[
            'tendaytro.unique' => 'Tên dãy trọ đã tồn tại',
        ]);
        // Lấy id từ session => Đang lưu trong session id = 10 (điều này thay đổi tùy từng trường hợp)
        $chutro_id = session('chutro_id'); // Lấy chutro_id từ session => lấy ra đúng nó luôn

        $daytroData = [
            'chutro_id' => $chutro_id,
            'tendaytro' => $request->tendaytro,
            'tinh' => $request->tinh,
            'huyen' => $request->huyen,
            'xa' => $request->xa,
            'sonha' => $request->sonha,
        ];
        
        if (session()->has('user_type')) {
            // Dua quanly_id vao DB
            $daytroData['quanly_id'] = $chutro_id;

            // Thay doi lai tu quanly_id thanh lai chutro_id thanh lai cho dung chuan  
            $chutro_id = quanly::where('id', $chutro_id)->value('chutro_id');
            $daytroData['chutro_id'] = $chutro_id;
        }

        // Tách ra $chutro_id và quanly_id xử lý dể create riêng 

        // Tạo và lưu vào DB 
        $daytro = Daytro::create($daytroData);

        // Lưu vào database
        $daytro->save();

        // Hiện thị thông báo thành công
        flash()->option('position', 'top-center')->timeout(1000)->success('Đã thêm dãy trọ thành công');


        
        return redirect()->back();
    }   

    public function destroy($id)
    {
        // Khai báo dãy trọ và lấy ra hàng cần xóa theo ID 
        $daytro = daytro::find($id);
        // Xóa hàng đó theo ID
        
        $daytro->delete();
        
        // Hiện thị thông báo thành công 
        flash()->option('position', 'top-center')->timeout(1000)->success('Đã xóa dãy trọ thành công!');

        return redirect()->back();

    }


    public function update(Request $request, $id)
    {
      // Tìm ra trường thông tin cần update => Lấy ra tập data 
      $daytro = DayTro::findOrFail($id);
      
      // Nếu validate thấy vị phạm nó sẽ lưu error vào session và nhảy tới redirect() ngay lập tức 
      $validator = Validator::make($request->all(), [
        'tendaytro' => 'required|string|max:255|unique:daytro,tendaytro,'.$id,
        'tinh' => 'required|string|max:255',
        'huyen' => 'required|string|max:255',
        'xa' => 'required|string|max:255',
        'sonha' => 'required|string|max:255',
        ], [
            'tendaytro.unique' => 'Tên dãy trọ vừa cập nhật đã tồn tại',
        ]);
      
        if ($validator->fails())
        {
            return redirect()->back()->withErrors($validator, 'update_errors_' . $id)->withInput();
        } 

        // Đưa tất cả các thông tin đã update vào DB để sửa
       $daytro->update($request->all());
       $daytro->save();
       // Hiện thị thông báo thành công 
       flash()->option('position', 'top-center')->timeout(1000)->success('Dãy trọ đã được cập nhật thành công!');
       
        //    $daytros = DayTro::paginate(5);
        //    return view('pages.daytro', compact('daytros'));
        //   return redirect()->back();
        session()->flash('success', true);
        
        return redirect()->back();
        
    }   


    public function search(Request $request)
    {   
        // Lấy giá trị tìm kiếm từ request
        $searchValue = $request->input('query');
        
        // Lấy chutro_id từ session
        $chutro_id = session('chutro_id'); // Biến đa hình (vừa là chutro vừa là quanly)

        $quanlys = quanly::where('chutro_id', $chutro_id)->get(); // Hien thi ra cot phan quyen cho quan ly 

        // Nếu user_type tồn tại trong session, lấy dãy trọ thuộc về quan lý hiện tại, nếu không, lấy tất cả dãy trọ
        $daytrosQuery = DayTro::where(function ($query) use ($searchValue) {
            $query->where('tendaytro', 'LIKE', "%{$searchValue}%")
                  ->orWhere('tinh', 'LIKE', "%{$searchValue}%")
                  ->orWhere('huyen', 'LIKE', "%{$searchValue}%")
                  ->orWhere('xa', 'LIKE', "%{$searchValue}%")
                  ->orWhere('sonha', 'LIKE', "%{$searchValue}%");
        });
    
        // Kiểm tra nếu có 'user_type' thì lọc theo quanly_id
        if (session()->has('user_type')) {
            $daytrosQuery->where('quanly_id', $chutro_id); // Nó sẽ có data nếu đang là role quanly 
        }
    
        // Thực hiện phân trang cho kết quả
        $daytros = $daytrosQuery->paginate(5);
    
        // Truyền dữ liệu vào view
        return view('pages.daytro', compact('daytros','quanlys'));
    }
    

    // Đây là thứ cần thiết để tạo ra tài khoản cho Quản Lý 
    public function phanquyen(Request $request, $id)
    {
        // Tìm DayTro bằng ID hoặc trả về lỗi 404 nếu không tìm thấy
        $dayTro = DayTro::findOrFail($id);
    
        // Cập nhật quanly_id từ request
        $dayTro->quanly_id = $request->input('quanly_id');
        $dayTro->save();

        flash()->option('position', 'top-center')->timeout(1000)->success('Trạng thái đã được cập nhật thành công!');

        return redirect()->back();
    }


    
}
