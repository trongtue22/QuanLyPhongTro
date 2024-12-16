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

<div class="container mt-1 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card-body">

                <form action="{{route('HoaDon.stored')}}" method="POST">
                    @csrf
                    
                    <input type="hidden" name='hopdong_id' id="hopdong_id" value="">
                    <input type="hidden" id="khachthue_phongtro_id" name="khachthue_phongtro_id" value="">
                    <input type="hidden" name="dichvu_id" id="dichvu_id" value="{{$dichvu->id}}">

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
                                <input type="date" name="ngaybatdau" id="ngaybatdau" class="form-control" required>
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

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="status">Trạng thái</label>
                                <select name="status" id="status" class="form-control" required>
                                    <option value="0">Chưa thanh toán</option>
                                    <option value="1">Đã thanh toán</option>
                                </select>
                            </div>
                        </div>

                    </div>

                 
                
                    {{-- Tổng Tiền (input ngắn) --}}
                    <div class="row">
                        <div class="col-md-4">
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

    // Dữ liệu phòng trọ, khách thuê và hợp đồng từ controller
    var phongtros = @json($phongtros);
    var khachthues = @json($khachthues);
    var hopdongs = @json($hopdongs);
    var khachthue_phongtro = @json($khachthue_phongtro);
    var maxSodienMoi = @json($maxSodienMoi); // Thêm mảng maxSodienMoi
    var maxSonuocMoi = @json($maxSonuocMoi); 
    var dichvu = @json($dichvu); 

    console.log("Phòng trọ:", phongtros);
    console.log("Khách thuê:", khachthues);
    console.log("Hợp đồng:", hopdongs);
    console.log("Khách thuê - Phòng trọ:", khachthue_phongtro);
    console.log("Số điện mới max:", maxSodienMoi); // Kiểm tra mảng maxSodienMoi
    console.log("Số điện nước max:", maxSonuocMoi);
    console.log("Danh sách dịch vụ:", dichvu);

    // Xử lý khi chọn Dãy trọ
    daytroSelect.addEventListener('change', function () {
        var daytroId = daytroSelect.value;
        console.log("Dãy trọ ID được chọn:", daytroId);
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
        
        if (daytroId) {
            var filteredPhongtros = phongtros.filter(function (phongtro) {
                return phongtro.daytro.id == daytroId; // cái này rất dễ sai 
            });

            console.log("Danh sách phòng trọ lọc được:", filteredPhongtros);
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
        dienmoiInput.value = '';

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
                            console.log("ID của khachthue_phongtro là:", khachthuePhongtroId.id);

                            var relatedHopdong = hopdongs.find(function (hd) {
                                return hd.khachthue_phongtro_id == khachthuePhongtroId.id;
                            });

                            if (relatedHopdong) 
                            {
                                
                                hopdong_id.value = relatedHopdong.id;
                                console.log("ID của hop dong là:", hopdong_id.value);
                                // Tìm max_sodienmoi tương ứng với hopdong_id
                                var maxSodien = maxSodienMoi.find(function (item) {
                                    return item.hopdong_id == relatedHopdong.id;
                                });
                                
                                if (maxSodien) {
                                    diencuInput.value = maxSodien.max_sodienmoi; // Nếu có max số điện mới
                                    console.log("Max số điện mới:", maxSodien.max_sodienmoi);
                                } else {
                                    diencuInput.value = 0; // Không có max số điện mới
                                    console.log("Không tìm thấy max số điện mới, đặt về 0.");
                                }

                                // Tìm max_sonuocmoi tương ứng với hopdong_id
                                var maxSonuoc = maxSonuocMoi.find(function (item) {
                                    return item.hopdong_id == relatedHopdong.id;
                                });

                                if (maxSonuoc) {
                                    nuoccuInput.value = maxSonuoc.max_sonuocmoi; // Nếu có max số nước mới
                                    console.log("Max số nước mới:", maxSonuoc.max_sonuocmoi);
                                } else {
                                    nuoccuInput.value = 0; // Không có max số nước mới
                                    console.log("Không tìm thấy max số nước mới, đặt về 0.");
                                }

                                
                            } else {
                                console.log("Không tìm thấy hợp đồng liên quan đến khachthue_phongtro_id.");
                            }

                        } else {
                            console.log("Không tìm thấy ID của khachthue_phongtro.");
                        }
                    } else {
                        console.log("Không tìm thấy khách thuê liên quan.");
                    }
                } else {
                    console.log("Không tìm thấy liên kết giữa khách thuê và phòng trọ.");
                }
                
                tienwifiInput.value = dichvu.wifi; 
                tienracInput.value = dichvu.rac; 
                
                // Tính toán tiền gửi xe 
                var tienguixe = parseFloat(dichvu.guixe) * parseInt(relatedHopdong.soxe);
                tienguixeInput.value = tienguixe.toFixed(2);

                console.log('Số lượng xe:', relatedHopdong.soxe);
               
                console.log("Giá tiền phòng:", selectedPhongtro.tienphong);

               
            } else {
                console.log("Không tìm thấy thông tin cho phòng trọ này.");
            }
        }
    });

    // Hàm kiểm tra tất cả các input 
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




