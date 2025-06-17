@extends('layouts.index')

{{-- Phần Heading --}}
@section('heading')
Cập nhật hóa đơn
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

                <form action="{{ route('HoaDon.update', $hoadonUpdate->id) }}" method="POST">
                    @csrf
                    @method('PUT')  <!-- This will change the method to PUT -->
                    <input type="hidden" name='hopdong_id' id="hopdong_id" value="{{$hoadonUpdate->hopdong_id}}">
                    <input type="hidden" id="khachthue_phongtro_id" name="khachthue_phongtro_id" value="">
                    <input type="hidden" name="dichvu_id" id="dichvu_id" value="">

                    {{-- Dãy trọ, Số Phòng, Tên Khách Thuê (Cùng 1 hàng ngang) --}}
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="daytro">Dãy trọ</label>
                                <select name="daytro" id="daytro" class="form-control" readonly disabled>
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
                                <select name="phongtro" id="phongtro" class="form-control" readonly disabled>
                                    {{-- <option value="">Chọn số phòng</option> --}}
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
                      
                
                    <div class="card mb-3"> {{-- Thẻ div 'card' bao bọc toàn bộ phần thông tin hóa đơn --}}
                        <div class="card-header bg-primary text-white">
                            <strong>Thông tin hóa đơn</strong>
                        </div>
                        <div class="card-body p-4"> {{-- Thẻ div 'card-body' để chứa nội dung của hóa đơn. Added p-4 for padding. --}}
                    
                            {{-- Tiền Phòng & Ngày tạo hóa đơn --}}
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
                                        <input type="date" name="ngaybatdau" id="ngaybatdau" class="form-control"
                                            value="{{ $hoadonUpdate->created_at->format('Y-m-d') }}" required>
                                        @error('ngaybatdau')
                                            <div class="text-danger mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                    
                            {{-- Electricity Section: Số điện cũ, Số điện mới, Tiêu thụ, Giá điện --}}
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
                                            <input type="number" name="tieuthudien" id="tieuthudien" class="form-control" min="0" required
                                                readonly>
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
                    
                            {{-- Water Section: Số nước cũ, Số nước mới, Tiêu thụ, Giá nước --}}
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
                                            <input type="number" name="thieuthucnuoc" id="thieuthucnuoc" class="form-control" min="0" required
                                                readonly>
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
                    
                            {{-- Parking Section: Giá gửi xe, Số xe, Tiền gửi xe --}}
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
                                            <input type="number" name="soxe" id="soxe" class="form-control" min="0" required>
                                            <span class="input-group-text">xe</span>
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
                            <div class="row mb-3">
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
                    
                        </div> {{-- Đóng thẻ div 'card-body' --}}
                    </div> {{-- Đóng thẻ div 'card' --}}
                
                    {{-- Tổng Tiền (input ngắn) --}}
                    <div class="row">
                        <div class="col-md-6">
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
                        <button type="submit" class="btn btn-primary" id="nutThemHoaDon" disabled>Cập nhật hóa đơn</button>
                    </div>
                </form>
                
                
                
                

            </div> <!-- End Card Body -->
        </div> <!-- End Column -->
    </div> <!-- End Row -->
</div> <!-- End Container -->


{{-- sự kiện xảy ra trước trạng thái update để hiện thị data cũ --}}

{{-- JavaScript để quản lý trạng thái --}}

<script>
    document.addEventListener('DOMContentLoaded', function () {
        function formatCurrency(value) {
            if (!value || isNaN(value)) return '0';
            return new Intl.NumberFormat('vi-VN').format(Number(value));
        }
    
        var daytroSelect    = document.getElementById('daytro');
        var phongtroSelect  = document.getElementById('phongtro');
        var khachthueInput  = document.getElementById('khachthue');
        var tienphongInput  = document.getElementById('tienphong');
        var nuoccuInput     = document.getElementById('nuoccu');
        var diencuInput     = document.getElementById('diencu');
        var tienwifiInput   = document.getElementById('tienwifi');
        var tienracInput    = document.getElementById('tienrac');
        var tienguixeInput  = document.getElementById('tienguixe');
        var nuocmoiInput    = document.getElementById('nuocmoi');
        var dienmoiInput    = document.getElementById('dienmoi');
        var tongtienInput   = document.getElementById('tongtien');
        var nutThemHoaDon   = document.getElementById('nutThemHoaDon');
        var hopdong_id      = document.getElementById('hopdong_id');
        var dichvu_id_input = document.getElementById('dichvu_id');
    
        // Các input dịch vụ dãy trọ
        var dienInput  = document.getElementById('dien');
        var nuocInput  = document.getElementById('nuoc');
        var wifiInput  = document.getElementById('wifi');
        var guixeInput = document.getElementById('guixe');
        var racInput   = document.getElementById('rac');
        var giaguixeInput = document.getElementById('giaguixe');
        var soXeInput = document.getElementById('soxe');
        var phongtros          = @json($phongtros);
        var khachthues         = @json($khachthues->values());
        var hopdongs           = @json($hopdongs);
        var khachthue_phongtro = @json($khachthue_phongtro);
        var maxSodienMoi       = @json($maxSodienMoi);
        var maxSonuocMoi       = @json($maxSonuocMoi);
        var dichvu             = @json($dichvu);
    
        var selectedDaytroId   = @json($hoadonUpdate->hopdong->khachthue_phongtro->phongtro->daytro->id ?? null);
        var selectedPhongtroId = @json($hoadonUpdate->hopdong->khachthue_phongtro->phongtro->id ?? null);
        var sodienmoiUpdate    = @json($hoadonUpdate->sodienmoi);
        var sonuocmoiUpdate    = @json($hoadonUpdate->sonuocmoi);
        var sodiencuUpdate     = @json($hoadonUpdate->sodiencu);
        var sonuoccuUpdate     = @json($hoadonUpdate->sonuoccu);
    
        // Khi chọn Dãy trọ
        daytroSelect.addEventListener('change', function () {
            var daytroId = this.value;
            phongtroSelect.innerHTML = '';
            phongtroSelect.disabled = false;
    
            // Reset
            khachthueInput.value = '';
            tienphongInput.value = '';
            nuoccuInput.value    = '';
            diencuInput.value    = '';
            tienwifiInput.value  = '';
            tienracInput.value   = '';
            tienguixeInput.value = '';
            nuocmoiInput.value   = '';
            dienmoiInput.value   = '';
            tongtienInput.value  = '';
            nutThemHoaDon.disabled = true;
            dichvu_id_input.value  = '';
    
            // Reset dịch vụ dãy trọ
            if (dienInput) dienInput.value = '';
            if (nuocInput) nuocInput.value = '';
            if (wifiInput) wifiInput.value = '';
            if (guixeInput) guixeInput.value = '';
            if (racInput) racInput.value = '';
    
            if (!daytroId) return;
    
            // Populate phòng trọ
            phongtros.filter(p => p.daytro.id == daytroId)
                     .forEach(p => {
                         var o = document.createElement('option');
                         o.value = p.id;
                         o.text   = 'Phòng số: ' + p.sophong;
                         phongtroSelect.appendChild(o);
                     });
    
            // Dịch vụ dãy trọ
            if (dichvu[daytroId]) {
                if (dienInput)  dienInput.value  = formatCurrency(dichvu[daytroId].dien);
                if (nuocInput)  nuocInput.value  = formatCurrency(dichvu[daytroId].nuoc);
                if (wifiInput)  wifiInput.value  = formatCurrency(dichvu[daytroId].wifi);
                if (guixeInput) guixeInput.value = formatCurrency(dichvu[daytroId].guixe);
                if (racInput)   racInput.value   = formatCurrency(dichvu[daytroId].rac);
                if(giaguixeInput) giaguixeInput.value = formatCurrency(dichvu[daytroId].guixe);
    
                tienwifiInput.value = formatCurrency(dichvu[daytroId].wifi);
                tienracInput.value  = formatCurrency(dichvu[daytroId].rac);
                dichvu_id_input.value = dichvu[daytroId].id;
              
            } else {
                if (dienInput)  dienInput.value  = '';
                if (nuocInput)  nuocInput.value  = '';
                if (wifiInput)  wifiInput.value  = '';
                if (guixeInput) guixeInput.value = '';
                if (racInput)   racInput.value   = '';
    
                tienwifiInput.value = '';
                tienracInput.value  = '';
                dichvu_id_input.value = '';
            }
        });
    
        // Khi chọn Phòng trọ
        phongtroSelect.addEventListener('change', function () {
            var phongtroId = this.value;
    
            // Reset
            khachthueInput.value = '';
            tienphongInput.value = '';
            nuoccuInput.value    = '';
            diencuInput.value    = '';
            tienwifiInput.value  = '';
            tienracInput.value   = '';
            tienguixeInput.value = '';
            nuocmoiInput.value   = '';
            dienmoiInput.value   = '';
            tongtienInput.value  = '';
            nutThemHoaDon.disabled = true;
            dichvu_id_input.value  = '';
    
            // Reset dịch vụ dãy trọ
            if (dienInput) dienInput.value = '';
            if (nuocInput) nuocInput.value = '';
            if (wifiInput) wifiInput.value = '';
            if (guixeInput) guixeInput.value = '';
            if (racInput) racInput.value = '';
    
            if (!phongtroId) return;
    
            var pt = phongtros.find(p => p.id == phongtroId);
            tienphongInput.value = formatCurrency(pt?.tienphong);
    
            // Khách thuê + hợp đồng
            var kp = khachthue_phongtro.find(k => k.phongtro_id == phongtroId);
            if (kp) {
                var kt = khachthues.find(k => k.id == kp.khachthue_id);
                khachthueInput.value = kt?.tenkhachthue || '';
                var hd = hopdongs.find(h => h.khachthue_phongtro_id == kp.id);
                hopdong_id.value = hd?.id || '';
    
                // Chỉ số cũ
                if (selectedDaytroId == daytroSelect.value) {
                    diencuInput.value = sodiencuUpdate;
                    nuoccuInput.value = sonuoccuUpdate;
                } else {
                    var ms = maxSodienMoi.find(x => x.hopdong_id == hd.id);
                    var mn = maxSonuocMoi.find(x => x.hopdong_id == hd.id);
                    diencuInput.value = ms?.max_sodienmoi || 0;
                    nuoccuInput.value = mn?.max_sonuocmoi || 0;
                }
                // Chỉ số mới edit
                if (selectedDaytroId == daytroSelect.value) {
                    if (sodienmoiUpdate) nuocmoiInput.value = sonuocmoiUpdate;
                    if (sonuocmoiUpdate) nuocmoiInput.value = sonuocmoiUpdate;
                    dienmoiInput.value = sodienmoiUpdate || '';
                    nuocmoiInput.value = sonuocmoiUpdate || '';
                }
    
                // Tiền gửi xe
                var soXe = Number(hd?.soxe) || 0;
                soXeInput.value = soXe;
                var giaXe = Number(dichvu[daytroSelect.value]?.guixe) || 0;
                tienguixeInput.value = formatCurrency(soXe * giaXe);
            }
    
            // Dịch vụ dãy trọ
            var dId = pt?.daytro.id;
            if (dichvu[dId]) {
                if (dienInput)  dienInput.value  = formatCurrency(dichvu[dId].dien);
                if (nuocInput)  nuocInput.value  = formatCurrency(dichvu[dId].nuoc);
                if (wifiInput)  wifiInput.value  = formatCurrency(dichvu[dId].wifi);
                if (guixeInput) guixeInput.value = formatCurrency(dichvu[dId].guixe);
                if (racInput)   racInput.value   = formatCurrency(dichvu[dId].rac);
    
                tienwifiInput.value = formatCurrency(dichvu[dId].wifi);
                tienracInput.value  = formatCurrency(dichvu[dId].rac);
                dichvu_id_input.value = dichvu[dId].id;
            } else {
                if (dienInput)  dienInput.value  = '';
                if (nuocInput)  nuocInput.value  = '';
                if (wifiInput)  wifiInput.value  = '';
                if (guixeInput) guixeInput.value = '';
                if (racInput)   racInput.value   = '';
    
                tienwifiInput.value = '';
                tienracInput.value  = '';
                dichvu_id_input.value = '';
            }
        });
    
        // Trigger mặc định khi edit
        if (selectedDaytroId) {
            daytroSelect.value = selectedDaytroId;
            daytroSelect.dispatchEvent(new Event('change'));
            if (selectedPhongtroId) {
                phongtroSelect.value = selectedPhongtroId;
                phongtroSelect.dispatchEvent(new Event('change'));
            }
        }
    });
</script>
    
    
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Hàm định dạng tiền tệ Việt Nam
        function formatCurrency(value) {
            if (!value || isNaN(value)) return '0';
            return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' })
                .format(value)
                .replace('₫', '')
                .trim();
        }

        var nuocmoiError = document.getElementById('nuocmoiError');
        var nuocmoiValueError = document.getElementById('nuocmoiValueError');
        var dienmoiError = document.getElementById('dienmoiError');
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
        var tongtienInput = document.getElementById('tongtien');
        var thieuthucnuocInput = document.getElementById('thieuthucnuoc');
        var tieuthudienInput  = document.getElementById('tieuthudien');
        var tongtiendienInput = document.getElementById('tongtiendien');
        var tongtiennuocInput = document.getElementById('tongtiennuoc');

        var dichvu = @json($dichvu);
        var nutThemHoaDon = document.getElementById('nutThemHoaDon');

        tongtienBtn.addEventListener('click', function () {
            var hasError = false;

            // Kiểm tra số nước mới
            if (nuocmoiInput.value.trim() === '') {
                nuocmoiError.style.display = 'block';
                nuocmoiValueError.style.display = 'none';
                tongtienInput.value = '';
                hasError = true;
            } else if (nuoccuInput.value > nuocmoiInput.value) {
                nuocmoiValueError.style.display = 'block';
                nuocmoiError.style.display = 'none';
                tongtienInput.value = '';
                hasError = true;
            } else {
                nuocmoiError.style.display = 'none';
                nuocmoiValueError.style.display = 'none';
            }

            // Kiểm tra số điện mới
            if (dienmoiInput.value.trim() === '') {
                dienmoiError.style.display = 'block';
                dienmoiValueError.style.display = 'none';
                tongtienInput.value = '';
                hasError = true;
            } else if (diencuInput.value > dienmoiInput.value) {
                dienmoiValueError.style.display = 'block';
                dienmoiError.style.display = 'none';
                tongtienInput.value = '';
                hasError = true;
            } else {
                dienmoiError.style.display = 'none';
                dienmoiValueError.style.display = 'none';
            }

            // Kiểm tra dãy trọ
            if (daytroSelect.value.trim() === '') {
                daytroError.style.display = 'block';
                tongtienInput.value = '';
                hasError = true;
            } else {
                daytroError.style.display = 'none';
            }

            // Kiểm tra phòng trọ
            if (phongtroSelect.value.trim() === '') 
            {
                phongtroError.style.display = 'block';
                tongtienInput.value = '';
                hasError = true;
            } else {
                phongtroError.style.display = 'none';
            }

            // Nếu không có lỗi, tính tổng tiền
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

                var totalAmount = electricityCost + waterCost + parkingFee + wifiFee + garbageFee + roomFee;

                // Hiển thị tổng tiền điện và nước
                tieuthudienInput.value = dienMoi - dienCu; // Ko phải tiền nên ko dùng hàm tiền ở đây làm gì cả 
                thieuthucnuocInput.value = nuocMoi - nuocCu; // Ko phải tiền nên ko dùng hàm tiền ở đây làm gì cả
                if (tongtiendienInput) tongtiendienInput.value = formatCurrency(electricityCost);
                if (tongtiennuocInput) tongtiennuocInput.value = formatCurrency(waterCost);

                // Định dạng tổng tiền theo VNĐ
                tongtienInput.value = formatCurrency(totalAmount);
                nutThemHoaDon.disabled = false;
            }
        });
    });
</script>


@endsection




