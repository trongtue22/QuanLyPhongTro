<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\DayTroApiController;
use App\Http\Controllers\Api\QuanLyApiController;
use App\Http\Controllers\Api\PhongTroApiController;
use App\Http\Controllers\Api\KhachThueApiController;
use App\Http\Controllers\Api\HopDongApiController;
use App\Http\Controllers\Api\HoaDonApiController;
use App\Http\Controllers\Api\DichVuApiController;
use App\Http\Controllers\Api\DashboardApiController;
use App\Http\Controllers\Api\ProfileApiController;

Route::get('/user', function (Request $request) 
{
    return $request->user();
})->middleware('auth:sanctum');

// Nhớ dùng /api trong postman khi test các API dưới đây

Route::get('/hello',[AuthApiController::class, 'show']);




// Hiện thị View trang Register
Route::get('/auth/register',[AuthApiController::class,'showRegisterForm']);

// Dang ky tai khoan
Route::post('/auth/register',[AuthApiController::class,'register']);


// Hiện thị View trang Login
Route::get('auth/login',[AuthApiController::class,'showLoginForm']);


// Login vào trang Home
Route::post('auth/login',[AuthApiController::class,'login']);

// Login dưới vai trò QL 
Route::post('quanly/auth/login',[AuthApiController::class,'quanlyLogin']);

// Show view login dưới vai trò QL 
Route::get('quanly/auth/login',[QuanLyApiController::class,'quanly']);


Route::get('quanly/user/check', [QuanLyApiController::class, 'check']);


Route::middleware(['CheckApiToken'])->group(function () 
{

// API logout khỏi tài khoản
Route::post('auth/logout',[AuthApiController::class,'logout']);

                                            // Api của dãy trọ 
// API của dãy trọ
Route::get('user/daytro', [DayTroApiController::class, 'daytro']);


// API add của dãy trọ 
Route::post('user/daytro/store',[DayTroApiController::class,'store']);

// API Xóa dãy trọ
Route::delete('/daytro/{id}', [DayTroApiController::class, 'destroy']);

// API Update dãy trọ
Route::put('/daytro/{id}', [DayTroApiController::class, 'update']);

// Search dãy trọ 
Route::get('/daytro', [DayTroApiController::class, 'search']);

// APi phan quyen QL cho day tro 
Route::put('daytro/phanquyen/{id}', [DayTroApiController::class, 'phanquyen']);

                    /*  Phòng Trọ theo Dãy Trọ - CRUD => table PhongTro - Dãy Trọ */ 

// View phòng trọ theo dãy trọ => Chú ý URL vì user sẽ nhìn thấy nó ở view
Route::get('user/daytro/{id}/phongtro', [PhongTroApiController::class, 'show']);

// Thêm phòng trọ vào dãy trọ
Route::post('user/daytro/{id}/phongtro', [PhongTroApiController::class, 'stored']);

// Xóa phòng trọ theo dãy trọ
Route::delete('user/daytro/phongtro/{id}', [PhongTroApiController::class, 'delete']);

// Edit phòng trọ theo dãy trọ
Route::put('user/daytro/phongtro/{id}', [PhongTroApiController::class, 'update']);

// Search phòng trọ
Route::get('/user/daytro/{id}/phongtro/', [PhongTroApiController::class, 'search']);


                    // Khách thuê theo Phòng Trọ - CRUD => Table Khach Thue - Phong Tro

// View khách thuê theo phòng trọ
Route::get('user/daytro/phongtro/{id}/khachthue', [KhachThueApiController::class, 'show']);

// Thêm khách thuê theo phòng trọ 
Route::post('user/daytro/phongtro/{id}/khachthue', [KhachThueApiController::class, 'stored']);

// Xóa khách thuê trong phòng trọ 
Route::delete('user/daytro/phongtro/{phongtro_id}/khachthue/{khachthue_id}', [KhachThueApiController::class, 'delete']);

// Update khách thuê trong phòng trọ 
Route::put('user/daytro/phongtro/khachthue/{id}', [KhachThueApiController::class, 'update']);

// search khách thuê trong phòng trọ 
Route::get('user/daytro/phongtro/{id}/khachthue', [KhachThueApiController::class, 'search']);


                                 // Phòng trọ tổng quát
// View Phòng trọ tổng quát 
Route::get('user/phongtro',[PhongTroApiController::class,'view']);

// View add Phòng Trọ 
Route::post('user/phongtro/store', [PhongTroApiController::class, 'store']);

// Searching Phòng Trọ tổng quát
Route::get('user/phongtro/search', [PhongTroApiController::class, 'searching']);


                                // Khách thuê tổng quát
// View khách thuê tổng quát 
Route::get('user/khachthue',[KhachThueApiController::class,'view']);

// Xóa khách thuê khỏi DB tổng quát
Route::delete('user/khachthue/{khachthue_id}',[KhachThueApiController::class, 'destroy']);

// Thêm khách thuê vào DB tổng quát
Route::post('user/khachthue', [KhachThueApiController::class, 'add']);

// Searching khách thuê tổng quát
Route::get('user/khachthue/search', [KhachThueApiController::class, 'searching']);


                                // Phòng trọ của khách thuê 
// View cho phòng trọ của Khách Thuê 
Route::get('user/khachthue/{id}/phongtro', [PhongTroApiController::class, 'index']);

// Searching cho phòng trọ của khách thuê
Route::get('user/khachthue/{id}/phongtro/search', [PhongTroApiController::class, 'finding']);

// Thêm phòng trọ cho khách thuê 
Route::post('user/khachthue/phongtro/add', [PhongTroApiController::class, 'add']);

// Xóa phòng trọ theo khách thuê
Route::delete('user/khachthue/{khachthue_id}/phongtro/{phongtro_id}', [PhongTroApiController::class, 'destroy']);



                                // Hợp đồng của khách thuê
// View hợp đồng
Route::get('user/hopdong', [HopDongApiController::class, 'view']);

// View thêm hợp đồng
Route::get('user/hopdong/add', [HopDongApiController::class, 'viewAdd']);

// Thêm hợp đồng 
Route::post('user/hopdong/add', [HopDongApiController::class, 'add']);

// Xóa hợp đồng
Route::delete('user/hopdong/delete/{id}', [HopDongApiController::class, 'delete']);

// View update 
Route::get('user/hopdong/update/{id}', [HopDongApiController::class, 'viewUpdate']);

// Update hợp đồng
Route::put('user/hopdong/update/{id}', [HopDongApiController::class, 'update']);

// Search hợp đồng
Route::get('user/hopdong/search', [HopDongApiController::class, 'search']);

                                    // Dich Vu
// View dịch vụ 
Route::get('user/dichvu', [DichVuApiController::class, 'view']);            

// Update dịch vụ
Route::put('user/dichvu/update/{id}', [DichVuApiController::class, 'update']);



                                             //  Hoa Don
// View hóa đơn
Route::get('user/hoadon', [HoaDonApiController::class, 'view']);

// View thêm hóa đơn
Route::get('user/hoadon/add', [HoaDonApiController::class, 'viewAdd']);                        

// Chức năng thêm hóa đơn
Route::post('user/hoadon/add', [HoaDonApiController::class, 'add']);

// Xóa hóa đơn
Route::delete('user/hoadon/delete/{id}', [HoaDonApiController::class, 'delete']);

// Serach hóa đơn
Route::get('user/hoadon/search', [HoaDonApiController::class, 'search']);

// View update hóa đơn 
Route::get('user/hoadon/update/{id}', [HoaDonApiController::class, 'viewUpdate']);

//  Update hóa đơn 
Route::put('user/hoadon/update/{id}', [HoaDonApiController::class, 'update']);

// Update trạng thái hóa đơn ở view
Route::put('user/hoadon/updateStatus/{id}', [HoaDonApiController::class, 'updateStatus']);


                                         // Api của quản lý 
// APi của quản lý
Route::get('/user/quanly', [QuanLyApiController::class, 'view']);

// Tạo quản lý
Route::post('user/quanly/add', [QuanLyApiController::class, 'add']);                        

// Xóa quản lý
Route::delete('user/quanly/delete/{id}', [QuanLyApiController::class, 'delete']);

// Tìm kiếm quản lý 
Route::get('user/quanly/search', [QuanLyApiController::class, 'search']);

// Update quản lý
Route::put('user/quanly/update/{id}', [QuanLyApiController::class, 'update']);


                                          // Thống kê
// View thống kê
Route::get('user/dashboard', [DashboardApiController::class, 'view'])->name('Dashboard.view');

                                        // Profile
// Phần view setting
Route::get('user/setting',[ProfileApiController::class,'view']);

// Phần update setting
Route::put('user/setting/{id}', [ProfileApiController::class, 'update']);

});