
<div class="modal fade" id="addBlockModal" tabindex="-1" role="dialog" aria-labelledby="addBlockModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                @if(isset($phongtro))
                <h5 class="modal-title" id="addBlockModalLabel">Thêm khách thuê vào phòng: {{ $phongtro->sophong }}</h5>
                @else
                <h5 class="modal-title" id="addBlockModalLabel">Thêm thông tin khách thuê
                @endif
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

                {{-- Form add khách thuê --}}
                <form action="{{ isset($phongtro) 
                ? route('khachthuePhongTro.stored', ['phongtro_id' => $phongtro->id]) 
                : route('KhachThue.stored') }}" 
                method="POST" id="addBlockForm">
                    @csrf
                    
                    {{-- Thông báo lỗi --}}
                    @if ($errors->any())
                        @foreach ($errors->all() as $error)
                            <div class="alert alert-danger small" role="alert" style="margin-bottom: 8px;">
                                <i class="fas fa-exclamation-circle"></i>
                                <strong>Thông báo!</strong> {{$error}} 
                            </div>
                        @endforeach
                    @endif

                    
                    <div class="form-group">
                        <label for="card">CCCD:</label>
                        <input type="text" id="card" name="cccd" class="form-control" placeholder="Nhập CCCD" onfocus="showSuggestions()" oninput="filterSuggestions()">
                        <ul id="suggestions" class="list-group" style="display: none;"></ul>
                    </div>
                    
                    
                    <div class="form-group">
                        <label for="khachthue">Tên khách thuê:</label>
                        <input type="text" id="khachthue" name="tenkhachthue" class="form-control" placeholder="Nhập tên khách thuê" required>
                        {{-- <ul id="suggestions" class="list-group" style="display: none;"></ul> --}}
                    </div>
                                            

                    <div class="form-group">
                        <label for="phone">Số điện thoại:</label>
                        <input name="sodienthoai" type="text" class="form-control" id="phone" placeholder="Nhập số điện thoại" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="born">Ngày sinh:</label>
                        <input type="date" name="ngaysinh" class="form-control" id="born" placeholder="Nhập ngày sinh" required
                               min="1900-01-01" max="2023-12-31">
                    </div>
                    

                    {{-- <div class="form-group">
                        <label for="card">CCCD:</label>
                        <input type="text" name="cccd" type="text" class="form-control" id="card" placeholder="Nhập căn cước công dân" required>
                    </div> --}}

                    <div class="form-group">
                        <label for="gender">Giới tính:</label>
                        <select name="gioitinh" id="gender" class="form-control" required>
                            <option value="0">Nam</option>
                            <option value="1">Nữ</option>
                        </select>
                    </div>

                    @if(isset($phongtro))
                    <div class="form-group">   
                        <input type="hidden" name="phongtro_id" type="text" class="form-control" id="daytro" value="{{$phongtro->id}}" required>
                    </div>
                    @endif
                </form>
                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                <button type="submit" class="btn btn-primary" id="saveChanges" form="addBlockForm">Lưu Thay Đổi</button>
            </div>
        </div>
    </div>
</div>




<script>
@if ($errors->any())
$('#addBlockModal').modal('show');
@endif
</script>


@if(isset($khachthuekhac) && count($khachthuekhac) > 0)
{{-- <script>
    // Danh sách các khách thuê có sẵn với thông tin chi tiết
    const khachThueKhac = @json($khachthuekhac);

    // Hàm hiển thị danh sách gợi ý
    function showSuggestions() {
        const suggestions = document.getElementById('suggestions');
        suggestions.style.display = 'block';
        suggestions.innerHTML = ''; // Xóa các gợi ý trước đó
        khachThueKhac.forEach(khachthue => {
            const li = document.createElement('li');
            li.textContent = khachthue.tenkhachthue; // Hiển thị tên khách thuê
            li.classList.add('list-group-item');
            li.onclick = function() {
                fillTenantInfo(khachthue);
                suggestions.style.display = 'none';
            };
            suggestions.appendChild(li);
        });
    }

    // Hàm điền thông tin khách thuê vào các trường khi tên khớp
    function fillTenantInfo(khachthue) {
        document.getElementById('khachthue').value = khachthue.tenkhachthue;
        document.getElementById('phone').value = khachthue.sodienthoai;
        document.getElementById('born').value = khachthue.ngaysinh;
        document.getElementById('card').value = khachthue.cccd;
        document.getElementById('gender').value = khachthue.gioitinh;

        // Set readonly
        document.getElementById('phone').readOnly = true;
        document.getElementById('born').readOnly = true;
        document.getElementById('card').readOnly = true;
        document.getElementById('gender').disabled = true;
    }

    // Hàm lọc danh sách gợi ý dựa trên giá trị input
    function filterSuggestions() {
        const input = document.getElementById('khachthue').value.toLowerCase();
        const suggestions = document.getElementById('suggestions');
        const items = suggestions.getElementsByTagName('li');

        let matchFound = false;

        for (let i = 0; i < items.length; i++) {
            const text = items[i].textContent.toLowerCase();
            if (text.includes(input)) {
                items[i].style.display = 'block';
                if (text === input) {
                    matchFound = true;
                }
            } else {
                items[i].style.display = 'none';
            }
        }

        const originalTenantSelected = khachThueKhac.find(khachthue => khachthue.tenkhachthue.toLowerCase() === input);

        if (originalTenantSelected && matchFound) {
            fillTenantInfo(originalTenantSelected);
        } else {
            document.getElementById('phone').readOnly = false;
            document.getElementById('born').readOnly = false;
            document.getElementById('card').readOnly = false;
            document.getElementById('gender').disabled = false;

            // Xóa giá trị của các trường nếu tên khách thuê không khớp hoàn toàn
            document.getElementById('phone').value = '';
            document.getElementById('born').value = '';
            document.getElementById('card').value = '';
            document.getElementById('gender').value = '';
        }
    }

    // Ẩn danh sách gợi ý khi nhấn ngoài
    document.addEventListener('click', function(event) {
        if (!event.target.closest('#khachthue') && !event.target.closest('#suggestions')) {
            document.getElementById('suggestions').style.display = 'none';
        }
    });
</script> --}}
<script>
    // Danh sách các khách thuê có sẵn với thông tin chi tiết
    const khachThueKhac = @json($khachthuekhac);

    // Hàm hiển thị danh sách gợi ý
    function showSuggestions() {
        const suggestions = document.getElementById('suggestions');
        suggestions.innerHTML = ''; // Xóa các gợi ý trước đó
        suggestions.style.display = 'block'; // Hiển thị danh sách gợi ý

        khachThueKhac.forEach(khachthue => {
            const li = document.createElement('li');
            li.textContent = khachthue.cccd; // Hiển thị CMND (CCCD)
            li.classList.add('list-group-item');
            li.onclick = function() {
                fillTenantInfo(khachthue);
                suggestions.style.display = 'none';
            };
            suggestions.appendChild(li);
        });
    }

    // Hàm điền thông tin khách thuê vào các trường khi CMND (CCCD) khớp
    function fillTenantInfo(khachthue) {
        document.getElementById('khachthue').value = khachthue.tenkhachthue;
        document.getElementById('phone').value = khachthue.sodienthoai;
        document.getElementById('born').value = khachthue.ngaysinh;
        document.getElementById('card').value = khachthue.cccd;
        document.getElementById('gender').value = khachthue.gioitinh;

        // Khóa các trường khác
        document.getElementById('phone').readOnly = true;
        document.getElementById('born').readOnly = true;
        document.getElementById('khachthue').readOnly = true;
        document.getElementById('gender').disabled = true;
    }

    // Hàm lọc danh sách gợi ý dựa trên giá trị input
    function filterSuggestions() {
        const input = document.getElementById('card').value.toLowerCase();
        const suggestions = document.getElementById('suggestions');
        const items = suggestions.getElementsByTagName('li');

        let matchFound = false;

        for (let i = 0; i < items.length; i++) {
            const text = items[i].textContent.toLowerCase();
            if (text.includes(input)) {
                items[i].style.display = 'block';
                if (text === input) {
                    matchFound = true;
                }
            } else {
                items[i].style.display = 'none';
            }
        }

        const originalTenantSelected = khachThueKhac.find(khachthue => khachthue.cccd.toLowerCase() === input);

        if (originalTenantSelected && matchFound) {
            fillTenantInfo(originalTenantSelected);
        } else {
            // Nếu không khớp, mở khóa các trường khác để người dùng có thể điền
            document.getElementById('phone').readOnly = false;
            document.getElementById('born').readOnly = false;
            document.getElementById('khachthue').readOnly = false;
            document.getElementById('gender').disabled = false;

            // Xóa giá trị của các trường nếu CMND không khớp hoàn toàn
            document.getElementById('khachthue').value = '';
            document.getElementById('phone').value = '';
            document.getElementById('born').value = '';
            document.getElementById('gender').value = '';
        }
    }

    // Ẩn danh sách gợi ý khi nhấn ngoài
    document.addEventListener('click', function(event) {
        if (!event.target.closest('#khachthue') && !event.target.closest('#suggestions') && event.target.id !== 'card') {
            document.getElementById('suggestions').style.display = 'none';
        }
    });

    // Gọi filterSuggestions khi nhập vào ô CCCD
    document.getElementById('card').addEventListener('input', filterSuggestions);
    document.getElementById('card').addEventListener('focus', showSuggestions);
</script>






@endif






<style>
    .list-group-item {
    padding: 10px 15px; /* Thêm khoảng cách bên trong để các mục dễ nhấn */
    border-radius: 5px; /* Bo tròn góc các mục */
    margin-bottom: 5px; /* Khoảng cách giữa các mục */
    background-color: #f8f9fa; /* Màu nền mặc định */
    border: 1px solid #e2e6ea; /* Đường viền nhẹ xung quanh các mục */
    transition: all 0.3s ease; /* Hiệu ứng chuyển đổi mượt mà */
}

.list-group-item:hover {
    background-color: #1768c0; /* Màu xanh khi hover */
    color: white; /* Màu chữ khi hover */
    cursor: pointer;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Hiệu ứng đổ bóng khi hover */
    transform: translateY(-2px); /* Hiệu ứng nâng cao khi hover */
}

#suggestions.show .list-group-item {
    opacity: 1; /* Đưa độ trong suốt về 1 để hiện lên hoàn toàn */
    transform: translateY(0); /* Đưa mục về vị trí ban đầu */
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Đổ bóng khi mục hiện lên */
}

#suggestions {
    list-style-type: none; /* Loại bỏ dấu chấm trước các mục */
    padding: 0; /* Loại bỏ padding mặc định */
    margin: 0; /* Loại bỏ margin mặc định */
    max-height: 200px; /* Giới hạn chiều cao để tránh danh sách quá dài */
    overflow-y: auto; /* Thêm thanh cuộn nếu danh sách quá dài */
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1); /* Đổ bóng cho toàn bộ danh sách */
    border-radius: 5px; /* Bo tròn góc danh sách */
    background-color: #fff; /* Nền trắng cho danh sách */
}   
</style>

