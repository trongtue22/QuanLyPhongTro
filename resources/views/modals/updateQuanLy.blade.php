{{-- Modal Chỉnh Sửa Dãy Trọ --}}
<div class="modal fade" id="editModal{{  $quanly->id }}" tabindex="-1" role="dialog" aria-labelledby="editModalLabel{{  $quanly->id }}" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content"> 
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel{{ $quanly->id }}">Update quản lý: {{  $quanly->ho_ten }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            {{-- Form edit  action="{{ route('phongtroDayTro.update',  $phongtro->id) }}" --}}
            <form  action="{{ route('QuanLy.update',  $quanly->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    
                         {{-- Thông báo lỗi --}}
                        @if ($errors->{'update_errors_' .  $quanly->id}->any())
                            @foreach ($errors->{'update_errors_' .  $quanly->id}->all() as $error)
                                <div class="alert alert-danger small" role="alert" style="margin-bottom: 8px;">
                                    <i class="fas fa-exclamation-circle"></i>
                                    <strong>Thông báo!</strong> {{ $error }}  
                                </div>
                            @endforeach
                        @endif
           
                    <div class="form-group">
                        <label for="room">Tên quản lý:</label>
                        <input type="text" class="form-control" id="room" name="ho_ten" value="{{$quanly->ho_ten}}" required>
                    </div>
                   
                    <div class="form-group">
                        <label for="price">Số điện thoại:</label>
                        <input type="text" class="form-control" id="price" name="sodienthoai" value="{{$quanly->sodienthoai}}" required>
                    </div>

                    <div class="form-group">
                        <label for="room">CCCD:</label>
                        <input type="text" class="form-control" id="room" name="cccd" value="{{$quanly->cccd}}" required>
                    </div>

                    
                    <div class="form-group">
                        <label for="editGender{{ $quanly->id }}">Giới tính:</label>
                        <select name="gioitinh" id="editGender{{ $quanly->id }}" class="form-control" required>
                            <option value="0" @if($quanly->gioitinh == 0) selected @endif>Nam</option>
                            <option value="1" @if($quanly->gioitinh == 1) selected @endif>Nữ</option>
                        </select>
                    </div>

                    
                    
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">Lưu Thay Đổi</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Check if there are validation errors
        @if ($errors->{'update_errors_' . $quanly->id}->any())
            // Show the modal if there are errors
            $('#editModal{{ $quanly->id }}').modal('show');
        @endif
    });
</script>