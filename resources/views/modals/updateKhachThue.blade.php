<div class="modal fade" id="editModal{{ $khachthue->id }}" tabindex="-1" role="dialog" aria-labelledby="editModalLabel{{ $khachthue->id }}" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content"> 
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel{{ $khachthue->id }}">Chỉnh sửa khách thuê: {{ $khachthue->tenkhachthue }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('khachthuePhongTro.update', $khachthue->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    @if ($errors->{'update_errors_' . $khachthue->id}->any())
                        @foreach ($errors->{'update_errors_' . $khachthue->id}->all() as $error)
                            <div class="alert alert-danger small" role="alert" style="margin-bottom: 8px;">
                                <i class="fas fa-exclamation-circle"></i>
                                <strong>Thông báo!</strong> {{ $error }}  
                            </div>
                        @endforeach
                    @endif

                    <div class="form-group">
                        <label for="editKhachthue{{ $khachthue->id }}">Tên khách thuê:</label>
                        <input type="text" id="editKhachthue{{ $khachthue->id }}" name="tenkhachthue" class="form-control" placeholder="Nhập tên khách thuê" value="{{ $khachthue->tenkhachthue }}">
                    </div>

                    <div class="form-group">
                        <label for="editPhone{{ $khachthue->id }}">Số điện thoại:</label>
                        <input name="sodienthoai" type="text" class="form-control" id="editPhone{{ $khachthue->id }}" placeholder="Nhập số điện thoại" required value="{{ $khachthue->sodienthoai }}">
                    </div>
                    
                    <div class="form-group">
                        <label for="editBorn{{ $khachthue->id }}">Ngày sinh:</label>
                        <input type="date" name="ngaysinh" class="form-control" id="editBorn{{ $khachthue->id }}" placeholder="Nhập ngày sinh" required value="{{ $khachthue->ngaysinh }}">
                    </div>

                    <div class="form-group">
                        <label for="editCard{{ $khachthue->id }}">CCCD:</label>
                        <input type="text" name="cccd" class="form-control" id="editCard{{ $khachthue->id }}" placeholder="Nhập căn cước công dân" required value="{{ $khachthue->cccd }}">
                    </div>

                    <div class="form-group">
                        <label for="editGender{{ $khachthue->id }}">Giới tính:</label>
                        <select name="gioitinh" id="editGender{{ $khachthue->id }}" class="form-control" required>
                            <option value="0" @if($khachthue->gioitinh == 0) selected @endif>Nam</option>
                            <option value="1" @if($khachthue->gioitinh == 1) selected @endif>Nữ</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-primary">Lưu Thay Đổi</button>
                </div>
            </form>
        </div>
    </div>
</div>


<script>
    $(document).ready(function() {
        // Mở modal nếu có lỗi validation
        @if ($errors->{'update_errors_' . $khachthue->id}->any())
            $('#editModal{{ $khachthue->id }}').modal('show');
        @endif
    });
</script>
