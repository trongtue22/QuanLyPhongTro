@extends('layouts.index')

{{-- Phần Heading --}}
@section('heading')
    Thông tin cá nhân
@endsection

@section('breadcrumb')
    <a href="{{ route('daytro') }}">
        Quay về
    </a>
@endsection

{{-- Phần content --}}
@section('content')

<div class="container mt-1 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-8"> {{-- Adjusted to col-md-8 for full width as avatar is removed --}}
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        {{-- Form Column - now full width --}}
                        <div class="col-md-12"> {{-- Changed to col-md-12 --}}
                            <form method="POST" action="{{ route('profile.update', $quanly->id) }}" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')

                                {{-- Note: Avatar input and preview script are removed as requested --}}

                                {{-- Tên tài khoản --}}
                                <div class="form-group mb-3">
                                    <label for="name" class="form-label">Tên tài khoản</label>
                                    <input type="text" name="ho_ten" id="name" class="form-control"
                                           value="{{ old('ho_ten', $quanly->ho_ten) }}" placeholder="Tên tài khoản" required readonly>
                                    @error('ho_ten')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Email --}}
                                <div class="form-group mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" name="email" id="email" class="form-control"
                                           value="{{ old('email', $quanly->email) }}" placeholder="Địa chỉ Email" required>
                                    @error('email')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Số điện thoại --}}
                                <div class="form-group mb-3">
                                    <label for="sodienthoai" class="form-label">Số điện thoại</label>
                                    <input type="text" name="sodienthoai" id="sodienthoai" class="form-control"
                                           value="{{ old('sodienthoai', $quanly->sodienthoai) }}" placeholder="Số điện thoại" required>
                                    @error('sodienthoai')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- CCCD --}}
                                <div class="form-group mb-3">
                                    <label for="cccd" class="form-label">CCCD</label>
                                    <input type="text" name="cccd" id="cccd" class="form-control"
                                           value="{{ old('cccd', $quanly->cccd) }}" placeholder="Số CCCD" required>
                                    @error('cccd')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Mật khẩu cũ --}}
                                <div class="form-group mb-3">
                                    <label for="old_password" class="form-label">Mật khẩu cũ</label>
                                    <input type="password" name="old_password" id="old_password" class="form-control"
                                           placeholder="Nhập mật khẩu cũ">
                                    @error('old_password')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Mật khẩu mới --}}
                                <div class="form-group mb-3">
                                    <label for="new_password" class="form-label">Mật khẩu mới</label>
                                    <input type="password" name="new_password" id="new_password" class="form-control"
                                           placeholder="Nhập mật khẩu mới">
                                </div>

                                {{-- Nhập lại mật khẩu mới --}}
                                <div class="form-group mb-4">
                                    <label for="confirm_password" class="form-label">Nhập lại mật khẩu mới</label>
                                    <input type="password" name="confirm_password" id="confirm_password" class="form-control"
                                           placeholder="Nhập lại mật khẩu mới">
                                    @error('confirm_password')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <input type="hidden" name="quanly_id" id="id" class="form-control" value="{{$quanly->id}}">

                                {{-- Nút Submit và Xóa tài khoản --}}
                                <div class="form-group d-flex justify-content-between">
                                    <button type="submit" class="btn btn-primary">Cập nhật thông tin</button>
                                </div>
    
                                    </div>
                                 </div>
                             </div> 
                        </div> 
                    </div>
                 </div>
                </div>
@endsection

