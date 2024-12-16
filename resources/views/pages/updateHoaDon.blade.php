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

<div class="container mt-1 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card-body">

                <form action="{{ route('HoaDon.update', $hoadonUpdate->id) }}" method="POST">
                    @csrf
                    @method('PUT')  <!-- This will change the method to PUT -->
                    <input type="hidden" name='hopdong_id' id="hopdong_id" value="{{$hoadonUpdate->hopdong_id}}">
                    <input type="hidden" id="khachthue_phongtro_id" name="khachthue_phongtro_id" value="">
                    <input type="hidden" name="dichvu_id" id="dichvu_id" value="{{$dichvu->id}}">

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
                
                    {{-- Tiền Phòng (input ngắn) --}}
                    <div class="row">

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="tienphong">Tiền phòng</label>
                                <input type="number" name="tienphong" id="tienphong" class="form-control" readonly>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="ngaybatdau">Ngày tạo hóa đơn</label>
                                <input type="date" name="ngaybatdau" id="ngaybatdau" class="form-control" value="{{$hoadonUpdate->created_at->format('Y-m-d')}}" equired>

                                @error('ngaybatdau')
                                    <div style="color: red;">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        
                    </div>
                
                    {{-- Số điện cũ và Số điện mới (Cùng 1 hàng ngang) --}}
                    <div class="row">
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="diencu">Số điện cũ</label>
                                <input type="number" name="diencu" id="diencu" class="form-control" readonly>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="dienmoi">Số điện mới</label>
                                <input type="number" name="dienmoi" id="dienmoi" class="form-control" required>
                                <div>
                                    <span id="dienmoiError" class="text-danger" style="display: none;">Vui lòng nhập giá trị!</span>
                                    <span id="dienmoiValueError" class="text-danger" style="display: none;">Vui lòng lớn hơn hoặc bằng giá trị điện cũ!</span>
                                    @error('dienmoi')
                                     <div style="color: red;">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                
                    {{-- Số nước cũ và Số nước mới (Cùng 1 hàng ngang) --}}
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nuoccu">Số nước cũ</label>
                                <input type="number" name="nuoccu" id="nuoccu" class="form-control" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nuocmoi">Số nước mới</label>
                                <input type="number" name="nuocmoi" id="nuocmoi" class="form-control" required>
                                <div>
                                    <span id="nuocmoiError" class="text-danger" style="display: none;">Vui lòng nhập giá trị!</span>
                                    <span id="nuocmoiValueError" class="text-danger" style="display: none;">Vui lòng lớn hơn hoặc bằng giá trị nước cũ!</span>
                                    @error('nuocmoi')
                                        <div style="color: red;">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                
                    {{-- Tiền Wifi và Tiền rác (Cùng 1 hàng ngang) --}}
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="tienwifi">Tiền Wifi</label>
                                <input type="number" name="tienwifi" id="tienwifi" class="form-control" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="tienrac">Tiền rác</label>
                                <input type="number" name="tienrac" id="tienrac" class="form-control" readonly>
                            </div>
                        </div>
                    </div>
                
                    {{-- Tiền Gửi Xe (input ngắn) --}}
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="tienguixe">Tiền gửi xe</label>
                                <input type="number" name="tienguixe" id="tienguixe" class="form-control" readonly>
                            </div>
                        </div>

                        {{-- <div class="col-md-6">
                            <div class="form-group">
                                <label for="status">Trạng thái</label>
                                <select name="status" id="status" class="form-control" required>
                                    <option value="0" {{ $hoadonUpdate->status == 0 ? 'selected' : '' }}>Chưa thanh toán</option>
                                    <option value="1" {{ $hoadonUpdate->status == 1 ? 'selected' : '' }}>Đã thanh toán</option>
                                </select>
                            </div>
                        </div> --}}

                    </div>
                
                    {{-- Tổng Tiền (input ngắn) --}}
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="tongtien">Tổng tiền</label>
                                <div class="input-group">
                                    <input type="number" name="tongtien" id="tongtien" class="form-control form-control-sm" readonly>
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

        // Dữ liệu từ controller
        var phongtros = @json($phongtros);
        var khachthues = @json($khachthues);
        var hopdongs = @json($hopdongs);
        var khachthue_phongtro = @json($khachthue_phongtro);
        var maxSodienMoi = @json($maxSodienMoi); 
        var maxSonuocMoi = @json($maxSonuocMoi); 
        var dichvu = @json($dichvu);

        // Lấy giá trị mặc định từ controller (nếu có)
        var selectedDaytroId = @json($hoadonUpdate->hopdong->khachthue_phongtro->phongtro->daytro->id ?? null); // Lấy giá trị đã chọn 
        var selectedPhongtroId = @json($hoadonUpdate->hopdong->khachthue_phongtro->phongtro->id ?? null);
        var sodienmoiUpdate = @json($hoadonUpdate->sodienmoi); 
        var sonuocmoiUpdate = @json($hoadonUpdate->sonuocmoi);

        var sodiencuUpdate = @json($hoadonUpdate->sodiencu); 
        var sonuoccuUpdate = @json($hoadonUpdate->sonuoccu);

        // Hiển thị thông tin debug (optional)
        console.log("Phòng trọ:", phongtros);
        console.log("Khách thuê:", khachthues);
        console.log("Hợp đồng:", hopdongs);
        console.log("Khách thuê - Phòng trọ:", khachthue_phongtro);
        console.log("Số điện mới max:", maxSodienMoi);
        console.log("Số nước mới max:", maxSonuocMoi);
        console.log("Danh sách dịch vụ:", dichvu);

        // Xử lý khi chọn Dãy trọ
        daytroSelect.addEventListener('change', function () {
            var daytroId = daytroSelect.value; // Lấy giá trị của mảng dãy trọ 
            // phongtroSelect.innerHTML = '<option value="">Chọn số phòng s</option>';
            phongtroSelect.disabled = false;
            khachthueInput.value = '';
            tienphongInput.value = ''; 
            nuoccuInput.value = ''; 
            diencuInput.value = '';
            tienwifiInput.value = '';
            tienracInput.value = '';
            tienguixeInput.value = '';
            nuocmoiInput.value = '';
            dienmoiInput.value = '';  // Reset giá trị điện mới
            tongtienInput.value = '';
            nutThemHoaDon.disabled = true;


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
                    phongtroSelect.disabled = true; // Thay đổi để tắt selector 
                }
            }
        });

        // Xử lý khi chọn Phòng trọ
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
            dienmoiInput.value = ''; // Reset giá trị điện mới
            tongtienInput.value = '';
            nutThemHoaDon.disabled = true;

            if (phongtroId) {
                var selectedPhongtro = phongtros.find(function (phongtro) {
                    return phongtro.id == phongtroId;
                });

                if (selectedPhongtro) {
                    tienphongInput.value = selectedPhongtro.tienphong;

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

                                   
                                    if(selectedDaytroId == daytroSelect.value)
                                    {
                                        diencuInput.value = sodiencuUpdate;
                                        nuoccuInput.value = sonuoccuUpdate;
                                    }
                                    else // Nếu ko đúng thì trường hợp này xảy ra 
                                    {
                                         // Max số điện mới
                                        var maxSodien = maxSodienMoi.find(function (item) {
                                            return item.hopdong_id == relatedHopdong.id;
                                        });
    
                                        if (maxSodien) {
                                            diencuInput.value = maxSodien.max_sodienmoi;
                                        } else {
                                            diencuInput.value = 0;
                                        }
    
                                        // Max số nước mới
                                        var maxSonuoc = maxSonuocMoi.find(function (item) {
                                            return item.hopdong_id == relatedHopdong.id;
                                        });
                                        if (maxSonuoc) {
                                            nuoccuInput.value = maxSonuoc.max_sonuocmoi;
                                        } else {
                                            nuoccuInput.value = 0;
                                        }
    
                                       
                                    }

                                     // Hiển thị số điện mới từ dữ liệu hợp đồng
                                     if (sodienmoiUpdate && selectedDaytroId == daytroSelect.value ) {
                                            dienmoiInput.value = sodienmoiUpdate; // Cập nhật giá trị điện mới
                                        }


                                    // Hiển thị số nước mới từ dữ liệu hợp đồng
                                    if (sonuocmoiUpdate && selectedDaytroId == daytroSelect.value) {
                                        nuocmoiInput.value = sonuocmoiUpdate; // Cập nhật giá trị nước mới
                                    }
                                }
                            }
                        }
                    }

                    // Hiển thị các dịch vụ => Fix cái này lại 
                    tienwifiInput.value = dichvu.wifi;
                    tienracInput.value = dichvu.rac;

                    // Tính tiền gửi xe
                    var tienguixe = parseFloat(dichvu.guixe) * parseInt(relatedHopdong.soxe);
                    tienguixeInput.value = tienguixe.toFixed(2);

                    console.log('Số lượng xe:', relatedHopdong.soxe);
                }
            }
        });

        // Nếu đã có giá trị mặc định, chọn daytro và phongtro
        if (selectedDaytroId) {
            daytroSelect.value = selectedDaytroId; // Đưa data dãy trọ đã chọn ra ngoài 
            daytroSelect.dispatchEvent(new Event('change'));  // Trigger the change event to load corresponding rooms
        }

        if (selectedPhongtroId) {
            phongtroSelect.value = selectedPhongtroId;
            phongtroSelect.dispatchEvent(new Event('change'));  // Trigger the change event to load related tenant and services
        }
    });
</script>



{{-- Xử lý phần tính tổng và validate data --}}
<script> 
   document.addEventListener('DOMContentLoaded', function () {
   
    // Validate data trước khi tính toán 
    var nuocmoiError = document.getElementById('nuocmoiError');
    var nuocmoiValueError = document.getElementById('nuocmoiValueError');
    var dienmoiError = document.getElementById('dienmoiError');
    var daytroError = document.getElementById('daytroError');
    var phongtroError = document.getElementById('phongtroError');

    var daytroSelect = document.getElementById('daytro');
    var phongtroSelect = document.getElementById('phongtro');
    // Thành phần tính toán 
    var tongtienBtn = document.getElementById('tongtienBtn');

    var dienmoiInput = document.getElementById('dienmoi'); // Change to dienmoiInput
    var diencuInput = document.getElementById('diencu'); 
    var nuocmoiInput = document.getElementById('nuocmoi');
    var nuoccuInput = document.getElementById('nuoccu'); 

    var tienguixeInput = document.getElementById('tienguixe');
    var tienwifiInput = document.getElementById('tienwifi'); 
    var tienracInput = document.getElementById('tienrac'); 
    var tienphongInput = document.getElementById('tienphong');
    // Biến để in ra kết quả
    var tongtienInput = document.getElementById('tongtien');

    
    var dichvu = @json($dichvu);

    var dichvuDien = dichvu.dien;
    var dichvuNuoc = dichvu.nuoc;

    var nutThemHoaDon = document.getElementById('nutThemHoaDon');

    // Add event listener for the button click
    tongtienBtn.addEventListener('click', function () 
    {
        var hasError = false;
        
        if (nuocmoiInput.value.trim() === '') {
            nuocmoiError.style.display = 'block';
            nuocmoiValueError.style.display = 'none';
            tongtienInput.value = '';
            hasError = true;
        }
        else if(nuoccuInput.value > nuocmoiInput.value )
        {
            nuocmoiValueError.style.display = 'block';
            nuocmoiError.style.display = 'none';
            tongtienInput.value = '';
            hasError = true;
        } 
        else {
            nuocmoiError.style.display = 'none';
            nuocmoiValueError.style.display = 'none';
        }

        if (dienmoiInput.value.trim() === '') {
            dienmoiError.style.display = 'block';
            dienmoiValueError.style.display = 'none';
            tongtienInput.value = '';
            hasError = true;
        }
        else if(diencuInput.value > dienmoiInput.value )
        {
            dienmoiValueError.style.display = 'block';
            dienmoiError.style.display = 'none';
            tongtienInput.value = '';
            hasError = true;
        }  
        else 
        {
            dienmoiError.style.display = 'none';
            dienmoiValueError.style.display = 'none';

        }

        if (daytroSelect.value.trim() === '') {
            daytroError.style.display = 'block';
            tongtienInput.value = '';
            hasError = true;
        }
        else {
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
            // console.log('Hiện thị dich vụ điện:',  dichvuDien);
           // Thêm lệnh này vào cuối mã JavaScript của bạn để sửa lỗi
            tongtienInput.value = (parseInt(dienmoiInput.value) - parseInt(diencuInput.value)) * dichvuDien + (parseInt(nuocmoiInput.value) - parseInt(nuoccuInput.value)) * dichvuNuoc + parseFloat(tienguixeInput.value) + parseFloat(tienwifiInput.value) + parseFloat(tienphongInput.value) + parseFloat(tienracInput.value);
            nutThemHoaDon.disabled = false;

            // Here you can proceed with your logic for calculating total amount
            // For example:
            // calculateTotal();
        }

    
    });
});
</script>



@endsection




