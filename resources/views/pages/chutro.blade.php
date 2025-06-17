@extends('layouts.index')

{{-- Phần Heading --}}
@section('heading')
  Thông tin cá nhân 
@endsection

@section('breadcrumb')
< <a href="{{ route('daytro') }}">
    Quay về
</a>
@endsection

{{-- Phần content --}}
@section('content')

<div class="container mt-1 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                {{-- <div class="card-header"></div> --}}
                <div class="card-body">
                    <div class="row">
                        
                        {{-- Cột Avatar --}}
                        
                        <div class="col-md-4 text-center">
                                                               {{-- Cách để hiện thị ảnh view từ DB ra   --}}
                            <img id="avatarPreview" src="{{asset( session('imageUrl'))}}" alt="Avatar" class="img-thumbnail rounded-circle" width="300" height="300">
                                <div class="mt-3">
                                    <!-- Nút bấm Thay đổi ảnh đại diện -->
                                    <button class="btn btn-secondary btn-sm" type="button" onclick="document.getElementById('avatarInput').click()">Thay đổi ảnh đại diện</button>

                                </div>

                        </div>

                        {{-- Cột Form --}}
                        <div class="col-md-8">
                            <form method="POST" action="{{ route('user.update', $chutro->id) }}" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')

                                {{-- Lấy file ảnh user đã chọn --}}
                                <input type="file" id="avatarInput" name="avatar" style="display: none;" accept="image/*" onchange="previewImage(event)">
                                
                                {{-- Tên tài khoản --}}
                                <div class="form-group">
                                    <label for="name">Tên tài khoản</label>
                                    <input type="text" name="ho_ten" id="name" class="form-control" 
                                           value="{{$chutro->ho_ten}}" placeholder="Tên tài khoản" required>
                                           @error('ho_ten')
                                           <div style="color: red;">{{ $message }}</div>
                                            @enderror
                                        
                                </div>

                                {{-- Email --}}
                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input type="email" name="email" id="email" class="form-control" 
                                           value="{{$chutro->email}}" placeholder="Địa chỉ Email" required>
                                           @error('email')
                                           <div style="color: red;">{{ $message }}</div>
                                            @enderror
                                </div>

                                {{-- Số điện thoại --}}
                                <div class="form-group">
                                    <label for="sodienthoai">Số điện thoại</label>
                                    <input type="text" name="sodienthoai" id="sodienthoai" class="form-control" 
                                           value="{{ $chutro->sodienthoai }}" placeholder="Số điện thoại" required>
                                    @error('sodienthoai')
                                    <div style="color: red;">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- CCCD --}}
                                <div class="form-group">
                                    <label for="cccd">CCCD</label>
                                    <input type="text" name="cccd" id="cccd" class="form-control" 
                                           value="{{ $chutro->cccd }}" placeholder="Số CCCD" required>
                                    @error('cccd')
                                    <div style="color: red;">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Mật khẩu --}}
                                <div class="form-group">
                                    <label for="password">Mật khẩu cũ</label>
                                    <input type="password" name="old_password" id="old_password" class="form-control" 
                                           placeholder="Nhập mật khẩu cũ">
                                           {{-- Hiện thị lỗi --}}
                                           @error('old_password')
                                           <div style="color: red;">{{ $message }}</div>
                                            @enderror
                                </div>

                                <div class="form-group">
                                    <label for="password">Mật khẩu mới</label>
                                    <input type="password" name="new_password" id="new_password" class="form-control" 
                                           placeholder="Nhập mật khẩu mới ">
                                </div>

                                <div class="form-group">
                                    <label for="password">Nhập lại mật khẩu mới</label>
                                    <input type="password" name="confirm_password" id="confirm_password" class="form-control" 
                                           placeholder="Nhập lại mật khẩu mới ">
                                           @error('confirm_password')
                                           <div style="color: red;">{{ $message }}</div>
                                            @enderror
                                </div>

                                <input type="hidden" name="chutro_id" id="id" class="form-control" value="{{$chutro->id}}">

                                {{-- Nút Submit --}}
                                <div class="form-group mt-4">
                                    <button type="submit" class="btn btn-primary">Cập nhật thông tin</button>

                                      <!-- Nút Xóa tài khoản -->
                                    <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#confirmDeleteModal">
                                        Xóa tài khoản
                                    </button>

                                    @include('modals.deleteTaiKhoan') <!-- Gọi modal xác nhận xóa tài khoản -->
                                </div>

                               
                            </form>
                        </div> <!-- End Form Column -->
                    </div> <!-- End Row -->
                </div> <!-- End Card Body -->
            </div> <!-- End Card -->
        </div> <!-- End Column -->
    </div> <!-- End Row -->
</div> <!-- End Container -->
@endsection


{{-- Xử lí ảnh được chọn để hiện thị review ra --}}
<script>
    // Hàm hiển thị ảnh preview sau khi chọn file
    function previewImage(event) {
        const reader = new FileReader();
        const imageField = document.getElementById('avatarPreview');

        reader.onload = function() {
            if (reader.readyState == 2) {
                imageField.src = reader.result; // Gán ảnh đã chọn vào thẻ img
            }
        }

        reader.readAsDataURL(event.target.files[0]);
    }
</script>
