@extends('layouts.index')

{{-- Phần Heading --}}
@section('heading')
   Thêm hóa đơn 
@endsection

@section('breadcrumb')
< <a href="{{route('HoaDon.view')}}">
    Quay về
</a>
@endsection

{{-- Phần content --}}
@section('content')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">


<div class="container mt-1 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card-body">

                <form action="{{route('HoaDon.stored')}}" method="POST">
                    @csrf
                    
                    <input type="hidden" name='hopdong_id' id="hopdong_id" value="">
                    <input type="hidden" id="khachthue_phongtro_id" name="khachthue_phongtro_id" value="">
                    <input type="hidden" name="dichvu_id" id="dichvu_id" value="">

                    {{-- Dãy trọ, Số Phòng, Tên Khách Thuê (Cùng 1 hàng ngang) --}}
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="daytro">Dãy trọ</label>
                                <select name="daytro" id="daytro" class="form-control" required>
                                    <option value="">Chọn dãy trọ</option>
                                    {{-- Populate Dãy trọ data --}}
                                    @foreach ($daytros as $daytro)
                                        <option value="{{ $daytro->id }}">{{ $daytro->tendaytro }}</option>
                                    @endforeach
                                </select>
                                <span id="daytroError" class="text-danger" style="display: none;">Vui lòng chọn giá trị!</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="phongtro">Số phòng</label>
                                <select name="phongtro" id="phongtro" class="form-control" required disabled>
                                    <option value="">Chọn số phòng</option>
                                </select>
                                <span id="phongtroError" class="text-danger" style="display: none;">Vui lòng chọn giá trị!</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="khachthue">Tên khách thuê (đại diện)</label>
                                <input type="text" name="khachthue" id="khachthue" class="form-control" readonly>
                            </div>
                        </div>
                    </div>

                    {{-- Dịch vụ theo dãy trọ --}}

                
                
                
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-primary text-white py-3">
                            <h5 class="mb-0">Thông tin hóa đơn</h5>
                        </div>
                        <div class="card-body p-4">
                            {{-- Tiền Phòng --}}
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="tienphong" class="form-label">Tiền phòng</label>
                                        <div class="input-group">
                                            <input type="text" name="tienphong" id="tienphong" class="form-control" readonly>
                                            <span class="input-group-text">VNĐ</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="ngaybatdau" class="form-label">Ngày tạo hóa đơn</label>
                                        <input type="date" name="ngaybatdau" id="ngaybatdau" class="form-control" required>
                                        @error('ngaybatdau')
                                            <div class="text-danger mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        
                            {{-- Số điện cũ, Số điện mới, Tiêu thụ, Giá điện --}}
                            <div class="row mb-3">
                                <div class="col-md-3 mb-3 mb-md-0">
                                    <div class="form-group">
                                        <label for="diencu" class="form-label">Số điện cũ</label>
                                        <div class="input-group">
                                            <input type="text" name="diencu" id="diencu" class="form-control" readonly>
                                            <span class="input-group-text">kWh</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 mb-3 mb-md-0">
                                    <div class="form-group">
                                        <label for="dienmoi" class="form-label">Số điện mới</label>
                                        <div class="input-group">
                                            <input type="text" name="dienmoi" id="dienmoi" class="form-control" required>
                                            <span class="input-group-text">kWh</span>
                                        </div>
                                        <div class="text-danger mt-1">
                                            <span id="dienmoiError" style="display: none;">Vui lòng nhập giá trị!</span>
                                            <span id="dienmoiValueError" style="display: none;">Vui lòng lớn hơn hoặc bằng giá trị điện cũ!</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 mb-3 mb-md-0">
                                    <div class="form-group">
                                        <label for="tieuthudien" class="form-label">Tiêu thụ điện</label>
                                        <div class="input-group">
                                            <input type="number" name="tieuthudien" id="tieuthudien" class="form-control" min="0" required readonly>
                                            <span class="input-group-text">kWh</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="dien" class="form-label">Giá điện</label>
                                        <div class="input-group">
                                            <input type="number" name="dien" id="dien" class="form-control" min="0" required>
                                            <span class="input-group-text">VNĐ/kWh</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        
                            {{-- Tổng tiền điện --}}
                            <div class="row mb-4">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="tongtiendien" class="form-label">Tổng tiền điện</label>
                                        <div class="input-group">
                                            <input type="text" name="tongtiendien" id="tongtiendien" class="form-control" readonly>
                                            <span class="input-group-text">VNĐ</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        
                            {{-- Số nước cũ, Số nước mới, Tiêu thụ, Giá nước --}}
                            <div class="row mb-3">
                                <div class="col-md-3 mb-3 mb-md-0">
                                    <div class="form-group">
                                        <label for="nuoccu" class="form-label">Số nước cũ</label>
                                        <div class="input-group">
                                            <input type="text" name="nuoccu" id="nuoccu" class="form-control" readonly>
                                            <span class="input-group-text">m³</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 mb-3 mb-md-0">
                                    <div class="form-group">
                                        <label for="nuocmoi" class="form-label">Số nước mới</label>
                                        <div class="input-group">
                                            <input type="text" name="nuocmoi" id="nuocmoi" class="form-control" required>
                                            <span class="input-group-text">m³</span>
                                        </div>
                                        <div class="text-danger mt-1">
                                            <span id="nuocmoiError" style="display: none;">Vui lòng nhập giá trị!</span>
                                            <span id="nuocmoiValueError" style="display: none;">Vui lòng lớn hơn hoặc bằng giá trị nước cũ!</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 mb-3 mb-md-0">
                                    <div class="form-group">
                                        <label for="thieuthucnuoc" class="form-label">Tiêu thụ nước</label>
                                        <div class="input-group">
                                            <input type="number" name="thieuthucnuoc" id="thieuthucnuoc" class="form-control" min="0" required readonly>
                                            <span class="input-group-text">m³</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="nuoc" class="form-label">Giá nước</label>
                                        <div class="input-group">
                                            <input type="number" name="nuoc" id="nuoc" class="form-control" min="0" required>
                                            <span class="input-group-text">VNĐ/m³</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        
                            {{-- Tổng tiền nước --}}
                            <div class="row mb-4">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="tongtiennuoc" class="form-label">Tổng tiền nước</label>
                                        <div class="input-group">
                                            <input type="text" name="tongtiennuoc" id="tongtiennuoc" class="form-control" readonly>
                                            <span class="input-group-text">VNĐ</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        
                            {{-- Tiền Wifi và Tiền rác --}}
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="tienwifi" class="form-label">Tiền Wifi</label>
                                        <div class="input-group">
                                            <input type="text" name="tienwifi" id="tienwifi" class="form-control" readonly>
                                            <span class="input-group-text">VNĐ</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="tienrac" class="form-label">Tiền rác</label>
                                        <div class="input-group">
                                            <input type="text" name="tienrac" id="tienrac" class="form-control" readonly>
                                            <span class="input-group-text">VNĐ</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        
                            {{-- Giá gửi xe, Số xe, Tiền gửi xe --}}
                            <div class="row mb-4">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="giaguixe" class="form-label">Giá gửi xe</label>
                                        <div class="input-group">
                                            <input type="number" name="giaguixe" id="giaguixe" class="form-control" min="0" required>
                                            <span class="input-group-text">VNĐ/xe</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="soxe" class="form-label">Số xe</label>
                                        <div class="input-group">
                                            <input type="number" name="soxe" id="soxe" class="form-control" min="0" required> {{-- REMOVED readonly --}}
                                            <span class="input-group-text">Xe</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="tienguixe" class="form-label">Tiền gửi xe</label>
                                        <div class="input-group">
                                            <input type="text" name="tienguixe" id="tienguixe" class="form-control" readonly>
                                            <span class="input-group-text">VNĐ</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        
                            {{-- Tổng tiền hóa đơn and Trạng thái --}}
                            <div class="row mb-3"> {{-- Added mb-3 for spacing below --}}
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="status" class="form-label">Trạng thái</label>
                                        <select name="status" id="status" class="form-select" required>
                                            <option value="0">Chưa thanh toán</option>
                                            <option value="1">Đã thanh toán</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        
                        </div>
                    </div>
                    
                    {{-- Tổng Tiền (input ngắn) --}}
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="tongtien">Tổng tiền</label>
                                <div class="input-group">
                                    <input type="text" name="tongtien" id="tongtien" class="form-control form-control-sm" readonly>
                                    <div class="ml-2">
                                        <button class="btn btn-danger" id="tongtienBtn" type="button" >Tổng tiền</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                                        
                
                    {{-- Nút Submit --}}
                    <div class="form-group mt-4">
                        <button type="submit" class="btn btn-primary" id="nutThemHoaDon" disabled>Thêm hóa đơn</button>
                    </div>
                </form>
                
                
                
                

            </div> <!-- End Card Body -->
        </div> <!-- End Column -->
    </div> <!-- End Row -->
</div> <!-- End Container -->


{{-- JavaScript để quản lý trạng thái --}}

<script>
    document.addEventListener('DOMContentLoaded', function () {
        function formatCurrency(value) {
            if (!value || isNaN(value)) return '0';
            return new Intl.NumberFormat('vi-VN').format(value);
        }
    
        var daytroSelect = document.getElementById('daytro');
        var phongtroSelect = document.getElementById('phongtro');
        var khachthueInput = document.getElementById('khachthue');
        var tienphongInput = document.getElementById('tienphong');
        var nuoccuInput = document.getElementById('nuoccu'); 
        var diencuInput = document.getElementById('diencu'); 
        var tienwifiInput = document.getElementById('tienwifi'); 
        var tienracInput = document.getElementById('tienrac'); 
        var tienguixeInput = document.getElementById('tienguixe');
        var nuocmoiInput = document.getElementById('nuocmoi');
        var dienmoiInput = document.getElementById('dienmoi');
        var tongtienInput = document.getElementById('tongtien');
        var nutThemHoaDon = document.getElementById('nutThemHoaDon');
        var hopdong_id = document.getElementById('hopdong_id');
        var dichvu_id_input = document.getElementById('dichvu_id');
    
        // Các input dịch vụ dãy trọ
        var dienInput = document.getElementById('dien');
        var nuocInput = document.getElementById('nuoc');
        var wifiInput = document.getElementById('wifi');
        var soXeInput = document.getElementById('soxe');
        var guixeInput = document.getElementById('guixe');
        var racInput = document.getElementById('rac');
        var giaguixeInput = document.getElementById('giaguixe');
        var phongtros = @json($phongtros);
        var khachthues = @json($khachthues->values());
        var hopdongs = @json($hopdongs);
        var khachthue_phongtro = @json($khachthue_phongtro);
        var maxSodienMoi = @json($maxSodienMoi);
        var maxSonuocMoi = @json($maxSonuocMoi);
        var dichvu = @json($dichvu);
        
        // Khi chọn dãy trọ
        daytroSelect.addEventListener('change', function () {
            var daytroId = daytroSelect.value;
            phongtroSelect.innerHTML = '<option value="">Chọn số phòng</option>';
            phongtroSelect.disabled = false;
            khachthueInput.value = ''; 
            tienphongInput.value = ''; 
            nuoccuInput.value = ''; 
            diencuInput.value = ''; 
            tienwifiInput.value = ''; 
            tienracInput.value = ''; 
            tienguixeInput.value = ''; 
            nuocmoiInput.value = '';
           
            dienmoiInput.value = '';
            tongtienInput.value = '';
            nutThemHoaDon.disabled = true;
    
            // Hiển thị dịch vụ dãy trọ vào các input dịch vụ
            if (dichvu[daytroId]) {
                if (dienInput) dienInput.value = formatCurrency(dichvu[daytroId].dien);
                if (nuocInput) nuocInput.value = formatCurrency(dichvu[daytroId].nuoc);
                if (wifiInput) wifiInput.value = formatCurrency(dichvu[daytroId].wifi);
             
                if (guixeInput) guixeInput.value = formatCurrency(dichvu[daytroId].guixe);
                if (racInput) racInput.value = formatCurrency(dichvu[daytroId].rac);
    
                tienwifiInput.value = formatCurrency(dichvu[daytroId].wifi);
                tienracInput.value = formatCurrency(dichvu[daytroId].rac);
                dichvu_id_input.value = dichvu[daytroId].id;
            } else {
                if (dienInput) dienInput.value = 0;
                if (nuocInput) nuocInput.value = 0;
                if (wifiInput) wifiInput.value = 0;
                if (guixeInput) guixeInput.value = 0;
                if (racInput) racInput.value = 0;
    
                tienwifiInput.value = '0';
                tienracInput.value = '0';
                dichvu_id_input.value = '';
            }
    
            if (daytroId) {
                var filteredPhongtros = phongtros.filter(function (phongtro) {
                    return phongtro.daytro.id == daytroId;
                });
                if (filteredPhongtros.length > 0) {
                    filteredPhongtros.forEach(function (phongtro) {
                        var option = document.createElement('option');
                        option.value = phongtro.id;
                        option.text = 'Phòng số: ' + phongtro.sophong;
                        phongtroSelect.appendChild(option);
                    });
                    phongtroSelect.disabled = false;
                }
            }
        });
    
        // Khi chọn phòng trọ
        phongtroSelect.addEventListener('change', function () {
            var phongtroId = phongtroSelect.value;
            khachthueInput.value = ''; 
            tienphongInput.value = ''; 
            nuoccuInput.value = ''; 
            diencuInput.value = ''; 
            tienwifiInput.value = ''; 
            tienracInput.value = ''; 
            tienguixeInput.value = '';
            nuocmoiInput.value = '';
            dienmoiInput.value = '';
            tongtienInput.value = '';
            nutThemHoaDon.disabled = true;
    
            var selectedPhongtro = phongtros.find(function (phongtro) {
                return phongtro.id == phongtroId;
            });
    
            if (selectedPhongtro) {
                tienphongInput.value = formatCurrency(selectedPhongtro.tienphong);
    
                var tenant = khachthue_phongtro.find(function (item) {
                    return item.phongtro_id == phongtroId;
                });
    
                if (tenant) {
                    var selectedKhachthue = khachthues.find(function (khachthue) {
                        return khachthue.id == tenant.khachthue_id;
                    });
    
                    if (selectedKhachthue) {
                        khachthueInput.value = selectedKhachthue.tenkhachthue;
    
                        var khachthuePhongtroId = khachthue_phongtro.find(function (item) {
                            return item.khachthue_id == selectedKhachthue.id && item.phongtro_id == phongtroId;
                        });
    
                        if (khachthuePhongtroId) {
                            var relatedHopdong = hopdongs.find(function (hd) {
                                return hd.khachthue_phongtro_id == khachthuePhongtroId.id;
                            });
    
                            if (relatedHopdong) {
                                hopdong_id.value = relatedHopdong.id;
    
                                var maxSodien = maxSodienMoi.find(function (item) {
                                    return item.hopdong_id == relatedHopdong.id;
                                });
                                diencuInput.value = maxSodien ? maxSodien.max_sodienmoi : 0;
    
                                var maxSonuoc = maxSonuocMoi.find(function (item) {
                                    return item.hopdong_id == relatedHopdong.id;
                                });
                                nuoccuInput.value = maxSonuoc ? maxSonuoc.max_sonuocmoi : 0;
    
                                var daytroId = selectedPhongtro.daytro.id;
                                if (dichvu[daytroId]) {
                                    if (dienInput) dienInput.value = formatCurrency(dichvu[daytroId].dien);
                                    if (nuocInput) nuocInput.value = formatCurrency(dichvu[daytroId].nuoc);
                                    if (wifiInput) wifiInput.value = formatCurrency(dichvu[daytroId].wifi);
                                    if (guixeInput) guixeInput.value = formatCurrency(dichvu[daytroId].guixe);
                                    if(giaguixeInput) giaguixeInput.value = formatCurrency(dichvu[daytroId].guixe);
                                    if (racInput) racInput.value = formatCurrency(dichvu[daytroId].rac);
    
                                    tienwifiInput.value = formatCurrency(dichvu[daytroId].wifi);
                                    tienracInput.value = formatCurrency(dichvu[daytroId].rac);
                                    dichvu_id_input.value = dichvu[daytroId].id;
    

                                    var soXe = Number(relatedHopdong.soxe) || 0;
                                    soXeInput.value = soXe;
                                    var tienguixe = parseFloat(dichvu[daytroId].guixe || 0) * soXe;
                                    tienguixeInput.value = formatCurrency(tienguixe);
                                } else {
                                    if (dienInput) dienInput.value = 0;
                                    if (nuocInput) nuocInput.value = 0;
                                    if (wifiInput) wifiInput.value = 0;
                                    if (guixeInput) guixeInput.value = 0;
                                    if (racInput) racInput.value = 0;
    
                                    tienwifiInput.value = '0';
                                    tienracInput.value = '0';
                                    tienguixeInput.value = '0';
                                    dichvu_id_input.value = '';
                                }
                            }
                        }
                    }
                }
            }
        });
    });
</script>


<!-- Phần tính tổng tiền -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        function formatCurrency(value) {
            if (!value || isNaN(value)) return '';
            return new Intl.NumberFormat('vi-VN').format(value);
        }

        var nuocmoiError = document.getElementById('nuocmoiError');
        var nuocmoiValueError = document.getElementById('nuocmoiValueError');
        var dienmoiError = document.getElementById('dienmoiError');
        var dienmoiValueError = document.getElementById('dienmoiValueError');
        var daytroError = document.getElementById('daytroError');
        var phongtroError = document.getElementById('phongtroError');

        var daytroSelect = document.getElementById('daytro');
        var phongtroSelect = document.getElementById('phongtro');
        var tongtienBtn = document.getElementById('tongtienBtn');

        var dienmoiInput = document.getElementById('dienmoi');
        var diencuInput = document.getElementById('diencu'); 
        var nuocmoiInput = document.getElementById('nuocmoi');
        var nuoccuInput = document.getElementById('nuoccu'); 

        var tienguixeInput = document.getElementById('tienguixe');
        var tienwifiInput = document.getElementById('tienwifi'); 
        var tienracInput = document.getElementById('tienrac'); 
        var tienphongInput = document.getElementById('tienphong');

        // Khu vực tính tổng tiền
        var tongtiendienInput = document.getElementById('tongtiendien');
        var tongtiennuocInput = document.getElementById('tongtiennuoc');
        var tieuthudienInput  = document.getElementById('tieuthudien');
        var tongtienInput = document.getElementById('tongtien');
        var thieuthucnuocInput = document.getElementById('thieuthucnuoc');

        var dichvu = @json($dichvu); 
        var nutThemHoaDon = document.getElementById('nutThemHoaDon');

        tongtienBtn.addEventListener('click', function () {
            var hasError = false;

            if (nuocmoiInput.value.trim() === '') {
                nuocmoiError.style.display = 'block';
                nuocmoiValueError.style.display = 'none';
                tongtienInput.value = '';
                hasError = true;
            } else if (parseInt(nuoccuInput.value) > parseInt(nuocmoiInput.value)) {
             nuocmoiValueError.style.display = 'block';
            nuocmoiError.style.display = 'none';
            tongtienInput.value = '';
            hasError = true;
            } else {
                nuocmoiError.style.display = 'none';
                nuocmoiValueError.style.display = 'none';
            }

            if (dienmoiInput.value.trim() === '') {
                dienmoiError.style.display = 'block';
                dienmoiValueError.style.display = 'none';
                tongtienInput.value = '';
                hasError = true;
            } else if (parseInt(diencuInput.value) > parseInt(dienmoiInput.value)) 
            {
                dienmoiValueError.style.display = 'block';
                dienmoiError.style.display = 'none';
                tongtienInput.value = '';
                hasError = true;
            } else {
                dienmoiError.style.display = 'none';
                dienmoiValueError.style.display = 'none';
            }

            if (daytroSelect.value.trim() === '') {
                daytroError.style.display = 'block';
                tongtienInput.value = '';
                hasError = true;
            } else {
                daytroError.style.display = 'none';
            }

            if (phongtroSelect.value.trim() === '') {
                phongtroError.style.display = 'block';
                tongtienInput.value = '';
                hasError = true;
            } else {
                phongtroError.style.display = 'none';
            }

            if (!hasError) {
                var daytroId = daytroSelect.value;
                var dv = dichvu[daytroId];
                var dichvuDien = dv ? parseFloat(dv.dien) : 0;
                var dichvuNuoc = dv ? parseFloat(dv.nuoc) : 0;

                var dienMoi = parseInt(dienmoiInput.value) || 0;
                var dienCu = parseInt(diencuInput.value) || 0;
                var nuocMoi = parseInt(nuocmoiInput.value) || 0;
                var nuocCu = parseInt(nuoccuInput.value) || 0;

                var electricityCost = (dienMoi - dienCu) * dichvuDien;
                var waterCost = (nuocMoi - nuocCu) * dichvuNuoc;
                var parkingFee = parseFloat(tienguixeInput.value.replace(/\./g, '')) || 0;
                var wifiFee = parseFloat(tienwifiInput.value.replace(/\./g, '')) || 0;
                var garbageFee = parseFloat(tienracInput.value.replace(/\./g, '')) || 0;
                var roomFee = parseFloat(tienphongInput.value.replace(/\./g, '')) || 0;

                // Gán tổng tiền điện, nước vào input
                tieuthudienInput.value = dienMoi - dienCu; // Ko phải tiền nên ko dùng hàm tiền ở đây làm gì cả 
                thieuthucnuocInput.value = nuocMoi - nuocCu; // Ko phải tiền nên ko dùng hàm tiền ở đây làm gì cả
                tongtiendienInput.value = formatCurrency(electricityCost);
                tongtiennuocInput.value = formatCurrency(waterCost);
                
                var totalAmount = electricityCost + waterCost + parkingFee + wifiFee + garbageFee + roomFee;
                tongtienInput.value = formatCurrency(totalAmount);
                nutThemHoaDon.disabled = false;
            }
        });
    });
</script>



@endsection




