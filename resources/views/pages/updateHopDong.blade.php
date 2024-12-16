@extends('layouts.index')

{{-- Phần Heading --}}
@section('heading')
   Cập nhật hợp đồng thuê phòng: số phòng {{$khachthuePhongTro->phongtro->sophong}} - dãy trọ {{$khachthuePhongTro->phongtro->daytro->tendaytro}} 
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

                <form action="{{ route('HopDong.update', $hopdong->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    {{-- Sử dụng .row và .col để chia cột --}}
                    <div class="row">
                        <div class="col-md-6">
                            {{-- Số phòng --}}
                            <div class="form-group">
                                <label for="phongtro">Số phòng</label>
                                <select name="phongtro" id="phongtro" class="form-control" required>
                                    <option value="">Chọn số phòng</option>
                                    @foreach ($phongtros as $phongtro)
                                        <option value="{{ $phongtro->id }}" 
                                            {{ $phongtro->id == $currentPhongTroId ? 'selected' : '' }}>
                                            Phòng số: {{ $phongtro->sophong }} - Thuộc dãy: {{ $phongtro->daytro->tendaytro }}
                                        </option>
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
                                <input type="date" name="ngaybatdau" id="ngaybatdau" class="form-control" required 
                                      value="{{ $hopdong->ngaybatdau}}">
                                @error('ngaybatdau')
                                    <div style="color: red;">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                       
                       
                        {{-- Ngày Kết Thúc --}}
                        <div class="col-md-6">
                          <div class="form-group">
                              <label for="ngayketthuc">Ngày kết thúc</label>
                              <input type="date" name="ngayketthuc" id="ngayketthuc" class="form-control" 
                                value="{{$hopdong->ngayketthuc}}" required>
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
                                <input type="number" name="tiencoc" id="tiencoc" class="form-control" 
                                value="{{number_format($hopdong->tiencoc, 0, ',', '.')}}" required>
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
                                <input type="number" name="soxe" id="soxe" class="form-control" 
                                value="{{$hopdong->soxe}}" required>
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

        // Get the current tenant ID passed from the server
        var currentKhachThueId = @json($currentKhachThueId);
        var currentPhongTroId = @json($currentPhongTroId);
        phongtroSelect.disabled= true;
        // Load tenants on page load if there's a current room
        if (currentPhongTroId) {
            // Enable the tenant select field
            khachthueSelect.disabled = false;
            loadTenants(currentPhongTroId, currentKhachThueId);
        }

        // Gắn sự kiện change cho phần tử "Số phòng"
        phongtroSelect.addEventListener('change', function () {
            var phongtroId = phongtroSelect.value;

            // Kiểm tra xem người dùng đã chọn một phòng trọ chưa
            if (phongtroId) {
                // Mở khóa trường "Khách thuê"
                khachthueSelect.disabled = false;
                
                // Load tenants for the selected room
                loadTenants(phongtroId);
            } else {
                // Khóa trường "Khách thuê" nếu không có phòng trọ được chọn
                khachthueSelect.disabled = true;
                khachthueSelect.innerHTML = '<option value="">Chọn khách thuê</option>';
                
                // Đặt giá trị "Số Người Thuê" thành rỗng
                songuoithueInput.value = '';
            }
        });

        // Function to load tenants for a selected room
        function loadTenants(phongtroId, selectedTenantId = null) {
            // Xóa tất cả các tùy chọn hiện có trong trường "Khách thuê"
            khachthueSelect.innerHTML = '<option value="">Chọn khách thuê</option>';

            // Lấy danh sách khách thuê tương ứng với phòng trọ được chọn
            var phongtros = @json($phongtros); // Lấy dữ liệu phòng trọ từ server
            var selectedPhongtro = phongtros.find(pt => pt.id == phongtroId);

            // Thêm tùy chọn khách thuê vào select
            if (selectedPhongtro && selectedPhongtro.khachthues.length > 0) {
                selectedPhongtro.khachthues.forEach(function (khachthue) {
                    var option = document.createElement('option');
                    option.value = khachthue.id;
                    option.text = khachthue.tenkhachthue;

                    // Set the option as selected if it matches the current tenant
                    if (khachthue.id == selectedTenantId) {
                        option.selected = true;
                    }

                    khachthueSelect.appendChild(option);
                });

                // Cập nhật số lượng khách thuê sau khi khách thuê đã được chọn
                songuoithueInput.value = selectedPhongtro.khachthues.length;
            } else {
                // Nếu không có khách thuê nào, đặt giá trị là 0
                songuoithueInput.value = 0;
            }
        }
    });
</script>




@endsection


