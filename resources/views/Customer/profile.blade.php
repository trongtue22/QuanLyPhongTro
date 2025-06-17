@extends('layouts.app')

@section('title', 'Hồ sơ Khách thuê')

@section('content')
<div class="container mx-auto px-4 py-8">
      <div class="d-flex align-items-center justify-content-between mb-4">
        <div style="width: 160px"></div> {{-- Để cân bằng với nút bên phải --}}
        <a href="{{ route('khachthue.dashboard') }}" class="btn btn-secondary btn-sm ms-0">
            ← Quay lại danh sách phòng
        </a>
    </div>

    <h1 class="text-3xl font-bold text-gray-800 mb-6 text-center">Hồ Sơ Cá Nhân Khách Thuê</h1>


    @if (session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <strong class="font-bold">Lỗi!</strong>
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-1 gap-8"> {{-- Thay đổi thành md:grid-cols-1 vì chỉ còn 1 cột chính --}}
        {{-- Phần thông tin cá nhân và form cập nhật --}}
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-2xl font-semibold text-gray-700 mb-4 border-b pb-2">Thông tin cá nhân</h2>
            {{-- Form cập nhật thông tin khách thuê, sử dụng phương thức PUT --}}
            <form action="{{route('khachthue.profile.update', $khachThue->id)}}" method="POST">
                @csrf {{-- CSRF token để bảo vệ form --}}
                @method('PUT') {{-- Giả lập phương thức PUT --}}

                <div class="mb-4">
                    <label for="tenkhachthue" class="block text-gray-700 text-sm font-bold mb-2">Họ và tên:</label>
                    <input type="text" name="tenkhachthue" id="tenkhachthue"
                           class="shadow appearance-none border rounded-lg w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline transition duration-150 ease-in-out"
                           value="{{ old('tenkhachthue', $khachThue->tenkhachthue) }}" required>
                    {{-- Hiển thị lỗi validation nếu có --}}
                    @error('tenkhachthue')
                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="sodienthoai" class="block text-gray-700 text-sm font-bold mb-2">Số điện thoại:</label>
                    <input type="text" name="sodienthoai" id="sodienthoai"
                           class="shadow appearance-none border rounded-lg w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline transition duration-150 ease-in-out"
                           value="{{ old('sodienthoai', $khachThue->sodienthoai) }}">
                    @error('sodienthoai')
                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="ngaysinh" class="block text-gray-700 text-sm font-bold mb-2">Ngày sinh:</label>
                    <input type="date" name="ngaysinh" id="ngaysinh"
                           class="shadow appearance-none border rounded-lg w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline transition duration-150 ease-in-out"
                           value="{{ old('ngaysinh', $khachThue->ngaysinh) }}">
                    @error('ngaysinh')
                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="gioitinh" class="block text-gray-700 text-sm font-bold mb-2">Giới tính:</label>
                    <select name="gioitinh" id="gioitinh"
                            class="shadow appearance-none border rounded-lg w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline transition duration-150 ease-in-out">
                        <option value="0" {{ old('gioitinh', $khachThue->gioitinh) == 0 ? 'selected' : '' }}>Nam</option>
                        <option value="1" {{ old('gioitinh', $khachThue->gioitinh) == 1 ? 'selected' : '' }}>Nữ</option>
                        <option value="2" {{ old('gioitinh', $khachThue->gioitinh) == 2 ? 'selected' : '' }}>Khác</option>
                    </select>
                    @error('gioitinh')
                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                </div>


               <div class="mb-6">
                    <label for="cccd" class="block text-gray-700 text-sm font-bold mb-2">CCCD/CMND:</label>
                    <input type="text" name="cccd" id="cccd"
                           class="shadow appearance-none border rounded-lg w-full py-2 px-3 leading-tight focus:outline-none focus:shadow-outline transition duration-150 ease-in-out
                                  bg-gray-100 text-gray-600 cursor-not-allowed" {{-- Thêm các lớp này --}}
                           value="{{ old('cccd', $khachThue->cccd) }}" readonly>
                    @error('cccd')
                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div class="flex items-center justify-between">
                    <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg focus:outline-none focus:shadow-outline transition duration-150 ease-in-out">
                        Cập nhật hồ sơ
                    </button>
                </div>
            </form>
        </div>

        {{-- Phần danh sách phòng trọ đã thuê (Đã xóa theo yêu cầu) --}}
    </div>
</div>
@endsection
