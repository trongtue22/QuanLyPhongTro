{{-- Modal Chỉnh Sửa Dãy Trọ --}}
<div class="modal fade" id="editModal{{  $phongtro->id }}" tabindex="-1" role="dialog" aria-labelledby="editModalLabel{{  $phongtro->id }}" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content"> 
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel{{ $phongtro->id }}">Chỉnh sửa phòng trọ: {{  $phongtro->sophong }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            {{-- Form edit  action="{{ route('phongtroDayTro.update',  $phongtro->id) }}" --}}
            <form  action="{{ route('phongtroDayTro.update',  $phongtro->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    
                         {{-- Thông báo lỗi --}}
                        @if ($errors->{'update_errors_' .  $phongtro->id}->any())
                            @foreach ($errors->{'update_errors_' .  $phongtro->id}->all() as $error)
                                <div class="alert alert-danger small" role="alert" style="margin-bottom: 8px;">
                                    <i class="fas fa-exclamation-circle"></i>
                                    <strong>Thông báo!</strong> {{ $error }}  
                                </div>
                            @endforeach
                        @endif
           
                    <div class="form-group">
                        <label for="room">Số phòng</label>
                        <input type="text" class="form-control" id="room" name="sophong" value="{{$phongtro->sophong}}" required>
                    </div>
                   
                    <div class="form-group">
                        <label for="price">Tiền phòng</label>
                        <input type="text" class="form-control" id="price" name="tienphong" value="{{ number_format($phongtro->tienphong, 0, '', '') }}" required>
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
        @if ($errors->{'update_errors_' . $phongtro->id}->any())
            // Show the modal if there are errors
            $('#editModal{{ $phongtro->id }}').modal('show');
        @endif
    });
</script>