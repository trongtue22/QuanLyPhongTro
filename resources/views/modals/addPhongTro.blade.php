
<div class="modal fade" id="addBlockModal" tabindex="-1" role="dialog" aria-labelledby="addBlockModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addBlockModalLabel">Thêm phòng trọ vào dãy: {{ $daytro->tendaytro }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

                {{-- Form create dãy trọ => action="{{route('daytro.store')}}" --}}
                <form action="{{route('phongtroDayTro.store')}}" id="addBlockForm" method="POST">
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
                        <label for="room">Số phòng:</label>
                        <input name="sophong" type="text" class="form-control" id="room" placeholder="Nhập số phòng" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="price">Tiền phòng:</label>
                        <input name="tienphong" type="text" class="form-control" id="price" placeholder="Nhập tiền phòng" required>
                    </div>
                    
                    <div class="form-group">
                        <input type="hidden" name="daytro" type="text" class="form-control" id="daytro" value="{{$daytro->id}}" required>
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
