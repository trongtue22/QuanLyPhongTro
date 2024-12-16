@extends('layouts.index')

{{-- Phần Heading --}}
@section('heading')
   Thêm hợp đồng thuê phòng
@endsection

@section('breadcrumb')
< <a href="{{ route('HopDong.view') }}">
    Quay về
</a>
@endsection

{{-- Phần content --}}
@section('content')

<div class="container mt-1 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card-body">

                <form action="{{route('HopDong.stored')}}" method="POST">
                    @csrf

                    {{-- Sử dụng .row và .col để chia cột --}}
                    <div class="row">
                        <div class="col-md-6">
                            {{-- Số phòng --}}
                            <div class="form-group">
                                <label for="phongtro">Số phòng</label>
                                <select name="phongtro" id="phongtro" class="form-control" required>
                                    <option value="">Chọn số phòng</option>
                                    @foreach ($phongtros as $phongtro)
                                        <option value="{{ $phongtro->id }}">Phòng số: {{$phongtro->sophong}} - Thuộc dãy: {{$phongtro->daytro->tendaytro}}</option>
                                    @endforeach
                                </select>
                                <!-- Thông báo lỗi cho phongtro -->
                                    @error('phongtro')
                                        <div style="color: red;">{{ $message }}</div>
                                    @enderror
                            </div>
                        </div>
                    
                        <div class="col-md-6">
                            {{-- Khách thuê --}}
                            <div class="form-group">
                                <label for="khachthue">Khách thuê (đại diện)</label>
                                <select name="khachthue" id="khachthue" class="form-control" disabled required>
                                    <option value="">Chọn khách thuê</option>
                                </select>
                                <!-- Thông báo lỗi cho khachthue -->
                                    @error('khachthue')
                                        <div style="color: red;">{{ $message }}</div>
                                    @enderror

                            </div>
                        </div>
                    </div>
                    


                    <div class="row">
                       
                        {{-- Ngày Bắt Đầu --}}
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="ngaybatdau">Ngày bắt đầu</label>
                                <input type="date" name="ngaybatdau" id="ngaybatdau" class="form-control" required>

                                @error('ngaybatdau')
                                    <div style="color: red;">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                       
                       
                        {{-- Ngày Kết Thúc --}}
                        <div class="col-md-6">
                          <div class="form-group">
                              <label for="ngayketthuc">Ngày kết thúc</label>
                              <input type="date" name="ngayketthuc" id="ngayketthuc" class="form-control" required>
                              @error('ngayketthuc')
                                   <div style="color: red;">{{ $message }}</div>
                              @enderror
                          </div>
                        </div>
        
                    </div>


                    <div class="row">

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="songuoithue">Số người thuê</label>
                                <input type="number" name="songuoithue" id="songuoithue" class="form-control" readonly>
                            </div>
                        </div>
                        
                        {{-- Tiền Cọc --}}
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="tiencoc">Tiền cọc</label>
                                <input type="number" name="tiencoc" id="tiencoc" class="form-control" required>
                                @error('tiencoc')
                                <div style="color: red;">{{ $message }}</div>
                                 @enderror
                            </div>
                        </div>
                    </div>


                    <div class="row">
                        {{-- Số Xe --}}
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="soxe">Số xe</label>
                                <input type="number" name="soxe" id="soxe" class="form-control" required>
                                @error('soxe')
                                <div style="color: red;">{{ $message }}</div>
                                 @enderror
                            </div>
                        </div>

                        {{-- Ô trống để căn giữa nếu cần --}}
                        <div class="col-md-6">
                            {{-- Có thể chèn thêm nội dung nếu cần --}}
                        </div>
                    </div>

                    {{-- Nút Submit --}}
                    <div class="form-group mt-4">
                        <button type="submit" class="btn btn-primary">Lưu Hợp Đồng</button>
                    </div>
                </form>
                

            </div> <!-- End Card Body -->
        </div> <!-- End Column -->
    </div> <!-- End Row -->
</div> <!-- End Container -->


{{-- JavaScript để quản lý trạng thái --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Lấy các phần tử cần thiết
        var phongtroSelect = document.getElementById('phongtro');
        var khachthueSelect = document.getElementById('khachthue');
        var songuoithueInput = document.getElementById('songuoithue'); // Lấy trường "Số Người Thuê"

        // Gắn sự kiện change cho phần tử "Số phòng"
        phongtroSelect.addEventListener('change', function () {
            // Kiểm tra xem người dùng đã chọn một phòng trọ chưa
            if (phongtroSelect.value) {
                // Mở khóa trường "Khách thuê"
                khachthueSelect.disabled = false;

                // Xóa tất cả các tùy chọn hiện có trong trường "Khách thuê"
                khachthueSelect.innerHTML = '<option value="">Chọn khách thuê</option>';

                // Lấy danh sách khách thuê tương ứng với phòng trọ được chọn
                var phongtroId = phongtroSelect.value;
                var phongtros = @json($phongtros); // Lấy dữ liệu phòng trọ từ server
                var selectedPhongtro = phongtros.find(pt => pt.id == phongtroId);

                // Thêm tùy chọn khách thuê vào select
                if (selectedPhongtro && selectedPhongtro.khachthues.length > 0) {
                    selectedPhongtro.khachthues.forEach(function (khachthue) {
                        var option = document.createElement('option');
                        option.value = khachthue.id;
                        option.text = khachthue.tenkhachthue;
                        khachthueSelect.appendChild(option);
                    });

                    // Cập nhật số lượng khách thuê sau khi khách thuê đã được chọn
                    songuoithueInput.value = selectedPhongtro.khachthues.length;
                } else {
                    // Nếu không có khách thuê nào, đặt giá trị là 0
                    songuoithueInput.value = 0;
                }
            } else {
                // Khóa trường "Khách thuê" nếu không có phòng trọ được chọn
                khachthueSelect.disabled = true;
                khachthueSelect.innerHTML = '<option value="">Chọn khách thuê</option>';
                
                // Đặt giá trị "Số Người Thuê" thành rỗng
                songuoithueInput.value = '';
            }
        });
    });
</script>

@endsection


