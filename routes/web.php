<?php

use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChutroController;
use App\Http\Controllers\DashBoardController;
use App\Http\Controllers\DayTro;
use App\Http\Controllers\DayTroController;
use App\Http\Controllers\DayTroDetail;
use App\Http\Controllers\DayTroDetailController;
use App\Http\Controllers\DichVuController;
use App\Http\Controllers\HoaDonController;
use App\Http\Controllers\HopDongController;
use App\Http\Controllers\KhachThueController;
use App\Http\Controllers\PhongTroController;
use App\Http\Controllers\QuanLyController;
use App\Http\Controllers\EmailController;

use Illuminate\Support\Facades\Route;


// Midlleware
use App\Http\Middleware\ClearFlashMessage;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;


//  Route::get('/hello',[AuthApiController::class, 'show']);


// Cấu trúc luồng:
// 1 => 2 => 3
// 2' => 3 (chỉ thay đổi 1 tí nên vẫn có thể giữ lại nhiều)
// 3' => 2'' (cho user thêm phòng trọ mà khách hàng đó sẽ thuê)

                                    /* Phần xác thực => Liên quan đến table ChuTro*/ 
// Hiện thị View trang Register
Route::get('/auth/register',[AuthController::class,'showRegisterForm'])->name('auth.showRegisterForm');

// Đưa thông tin đăng kí user vào database
Route::post('/auth/register',[AuthController::class,'register'])->name('auth.register');

// Hiện thị View trang Login
Route::get('auth/login',[AuthController::class,'showLoginForm'])->name('auth.showLoginForm');

// Login vào trang Home
Route::post('auth/login',[AuthController::class,'login'])->name('auth.login');

// Hiện thị view login dưới danh nghĩa QL 
Route::get('quanly/auth/login',[AuthController::class,'quanly']);

// Login vào trang Home dưới dạng QL
Route::post('quanly/auth/login',[AuthController::class,'quanlyLogin'])->name('quanly.login');


// Phần Logout
Route::post('auth/logout',[AuthController::class,'logout'])->name('auth.logout');





// Check coi các khi user access các router phía dưới đã có login chưa 
Route::middleware(['checklogin','PivotDichVu'])->group(function () {    

// Phần view setting
Route::get('user/setting',[ChutroController::class,'view'])->name('user.setting');

// Phần update setting
Route::put('user/setting/{id}', [ChutroController::class, 'update'])->name('user.update');

                                    /*  Dãy Trọ - CRUD => table DayTro */ 
// View dãy trọ 
Route::get('user/daytro', [DayTroController::class, 'daytro'])->name('daytro'); // Login trả về đây (trước đó check middleware)

// Thêm dãy trọ 
Route::post('user/daytro/store',[DayTroController::class,'store'])->name('daytro.store');

// Xóa dãy trọ
Route::delete('/daytro/{id}', [DayTroController::class, 'destroy'])->name('daytro.destroy');

// Update dãy trọ
Route::put('/daytro/{id}', [DayTroController::class, 'update'])->name('daytro.update');

// Search dãy trọ 
Route::get('/daytro', [DayTroController::class, 'search'])->name('daytro.search');

// Phân quyền cho dãy trọ
Route::put('daytro/updatePhanQuyen/{id}', [DayTroController::class, 'phanquyen'])->name('daytro.phanquyen');


                                  /*  Phòng Trọ theo Dãy Trọ - CRUD => table PhongTro - Dãy Trọ */ 

// View phòng trọ theo dãy trọ => Chú ý URL vì user sẽ nhìn thấy nó ở view
Route::get('user/daytro/{id}/phongtro', [PhongTroController::class, 'show'])->name('phongtroDayTro.view');

// Thêm phòng trọ vào dãy trọ
Route::post('user/daytro/phongtro', [PhongTroController::class, 'stored'])->name('phongtroDayTro.store');

// Xóa phòng trọ theo dãy trọ
Route::delete('user/daytro/phongtro/{id}', [PhongTroController::class, 'delete'])->name('phongtroDayTro.destroy');

// Edit phòng trọ theo dãy trọ
Route::put('user/daytro/phongtro/{id}', [PhongTroController::class, 'update'])->name('phongtroDayTro.update');

// Search phòng trọ
Route::get('/user/daytro/phongtro/', [PhongTroController::class, 'search'])->name('phongtroDayTro.search');

                                 // Khách thuê theo Phòng Trọ - CRUD => Table Khach Thue - Phong Tro
// View khách thuê theo phòng trọ
Route::get('user/daytro/phongtro/{id}/khachthue', [KhachThueController::class, 'show'])->name('khachthuePhongTro.view');

// Thêm khách thuê theo phòng trọ 
Route::post('user/daytro/phongtro/khachthue', [KhachThueController::class, 'stored'])->name('khachthuePhongTro.stored');

// Xóa khách thuê trong phòng trọ 
Route::delete('user/daytro/phongtro/{phongtro_id}/khachthue/{khachthue_id}', [KhachThueController::class, 'delete'])->name('khachthuePhongTro.destroy');

// search khách thuê trong phòng trọ 
Route::get('user/daytro/phongtro/khachthue', [KhachThueController::class, 'search'])->name('khachthuePhongTro.search');

// Update khách thuê trong phòng trọ 
Route::put('user/daytro/phongtro/khachthue/{id}', [KhachThueController::class, 'update'])->name('khachthuePhongTro.update');


                                        // Phòng trọ tổng quát 
// View Phòng trọ tổng quát 
Route::get('user/phongtro',[PhongTroController::class,'view'])->name('PhongTro.view');

// View add Phòng Trọ 
Route::post('user/phongtro/store', [PhongTroController::class, 'store'])->name('phongtro.store');

// Searching Phòng trọ tổng quát 
Route::get('user/phongtro/search', [PhongTroController::class, 'searching'])->name('PhongTro.search');


                                         // Khách thuê tổng quát
// View khách thuê tổng quát 
Route::get('user/khachthue',[KhachThueController::class,'view'])->name('KhachThue.view');

// Xóa khách thuê khỏi DB tổng quát
Route::delete('user/khachthue/{khachthue_id}',[KhachThueController::class, 'destroy'])->name('KhachThue.delete');

// Thêm khách thuê vào DB tổng quát
Route::post('user/khachthue', [KhachThueController::class, 'add'])->name('KhachThue.stored');

// Searching khách thuê tổng quát
Route::get('user/khachthue/search', [KhachThueController::class, 'searching'])->name('KhachThue.search');


                                        // Phòng trọ của khách thuê 
// View cho phòng trọ của Khách Thuê 
Route::get('user/khachthue/{id}/phongtro', [PhongTroController::class, 'index'])->name('phongtroKhachThue.view');

// Searching cho phòng trọ của khách thuê
Route::get('user/khachthue/phongtro/search', [PhongTroController::class, 'finding'])->name('phongtroKhachThue.search');

// Thêm phòng trọ cho khách thuê 
Route::post('user/khachthue/phongtro/add', [PhongTroController::class, 'add'])->name('phongtroKhachThue.stored');

// Xóa phòng trọ theo khách thuê
Route::delete('user/khachthue/{khachthue_id}/phongtro/{phongtro_id}', [PhongTroController::class, 'destroy'])->name('phongtroKhachThue.destroy');


                                        // Hợp đồng của khách thuê
// View hợp đồng
Route::get('user/hopdong', [HopDongController::class, 'view'])->name('HopDong.view');

// View thêm hợp đồng
Route::get('user/hopdong/add', [HopDongController::class, 'viewAdd'])->name('HopDong.viewAdd');

// Thêm hợp đồng 
Route::post('user/hopdong/add', [HopDongController::class, 'add'])->name('HopDong.stored');

// Xóa hợp đồng
Route::delete('user/hopdong/delete/{id}', [HopDongController::class, 'delete'])->name('HopDong.delete');

// View update 
Route::get('user/hopdong/update/{id}', [HopDongController::class, 'viewUpdate'])->name('HopDong.viewUpdate');

// Update hợp đồng
Route::put('user/hopdong/update/{id}', [HopDongController::class, 'update'])->name('HopDong.update');

// Search hợp đồng
Route::get('user/hopdong/search', [HopDongController::class, 'search'])->name('HopDong.search');


                                        // Dich Vu
// View dịch vụ 
Route::get('user/dichvu', [DichVuController::class, 'view'])->name('DichVu.view');            

// Update dịch vụ
Route::put('user/dichvu/update/{id}', [DichVuController::class, 'update'])->name('DichVu.update');


                                        //  Hoa Don
// View hóa đơn
Route::get('user/hoadon', [HoaDonController::class, 'view'])->name('HoaDon.view');

// View thêm hóa đơn
Route::get('user/hoadon/add', [HoaDonController::class, 'viewAdd'])->name('HoaDonAdd.view');                        

// Chức năng thêm hóa đơn
Route::post('user/hoadon/add', [HoaDonController::class, 'add'])->name('HoaDon.stored');

// Xóa hóa đơn
Route::delete('user/hoadon/delete/{id}', [HoaDonController::class, 'delete'])->name('HoaDon.delete');

// Serach hóa đơn
Route::get('user/hoadon/search', [HoaDonController::class, 'search'])->name('HoaDon.search');


// View update hóa đơn 
Route::get('user/hoadon/update/{id}', [HoaDonController::class, 'viewUpdate'])->name('HoaDon.viewUpdate');

//  Update hóa đơn 
Route::put('user/hoadon/update/{id}', [HoaDonController::class, 'update'])->name('HoaDon.update');

// Update trạng thái hóa đơn ở view
Route::put('user/hoadon/updateStatus/{id}', [HoaDonController::class, 'updateStatus'])->name('HoaDon.updateStatus');


                                            // Thống kê
// View thống kê
Route::get('user/dashboard', [DashBoardController::class, 'view'])->name('Dashboard.view');



                                            // Phân quyền quản lý
// View
Route::get('user/quanly', [QuanLyController::class, 'view'])->name('QuanLy.view');

// Tạo quản lý
Route::post('user/quanly/add', [QuanLyController::class, 'add'])->name('QuanLy.add');                        

// Xóa quản lý
Route::delete('user/quanly/delete/{id}', [QuanLyController::class, 'delete'])->name('QuanLy.delete');

// Tìm kiếm quản lý 
Route::get('user/quanly/search', [QuanLyController::class, 'search'])->name('QuanLy.search');

// Update quản lý
Route::put('user/quanly/update/{id}', [QuanLyController::class, 'update'])->name('QuanLy.update');


                                                // Email
Route::get('user/send-welcome-email', [EmailController::class, 'sendWelcomeEmail']);

});

