
<div class="modal fade" id="addBlockModal" tabindex="-1" role="dialog" aria-labelledby="addBlockModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addBlockModalLabel">Thêm quản lý:</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

                {{-- Form create dãy trọ => action="{{route('daytro.store')}}" --}}
                <form action="{{route('QuanLy.add')}}" id="addBlockForm" method="POST">
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
                        <label for="room">Tên quản lý:</label>
                        <input name="ho_ten" type="text" class="form-control" id="room" placeholder="Nhập tên quản lý" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="price">Số điện thoại:</label>
                        <input name="sodienthoai" type="text" class="form-control" id="price" placeholder="Nhập số điện thoại" required>
                    </div>


                    <div class="form-group">
                        <label for="price">CCCD:</label>
                        <input name="cccd" type="text" class="form-control" id="price" placeholder="Nhập CCCD" required>
                    </div>

                    <div class="form-group">
                        <label for="password">Mật khẩu:</label>
                        <input name="password" type="password" class="form-control" id="password" placeholder="Nhập mật khẩu" required>
                    </div>
                    
                     <div class="form-group">
                        <label for="gender">Giới tính:</label>
                        <select name="gioitinh" class="form-control" id="gender" required>
                            <option value="0" selected>Nam</option>
                            <option value="1">Nữ</option>
                        </select>
                    </div>
                
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
document.addEventListener("DOMContentLoaded", function() 
{
        // Kiểm tra nếu tồn tại thẻ chứa lỗi
@if($errors->any())
     $('#addBlockModal').modal('show');
@endif
});
</script>
