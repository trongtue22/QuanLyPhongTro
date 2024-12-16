<div class="modal fade" id="addBlockModal" tabindex="-1" role="dialog" aria-labelledby="addBlockModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addBlockModalLabel">Thêm phòng trọ</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

                {{-- Form create dãy trọ => action="{{route('daytro.store')}}" --}}
                <form action="{{ route('phongtro.store') }}" id="addBlockForm" method="POST">
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
                        <label for="daytro">Dãy trọ:</label>
                        <select name="daytro_id" class="form-control" id="daytro" required>
                            <option value="">Chọn dãy trọ</option>
                            {{-- @foreach ($phongtros->pluck('daytro')->unique('id') as $daytro)
                            <option value="{{ $daytro->id }}">{{ $daytro->tendaytro }}</option>
                            @endforeach --}}
                            @foreach ($daytros as $daytro)
                            <option value="{{ $daytro->id }}">{{ $daytro->tendaytro }}</option>
                            @endforeach
                        
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
    document.addEventListener("DOMContentLoaded", function() {
        // Kiểm tra nếu tồn tại thẻ chứa lỗi
@if ($errors->any())
     $('#addBlockModal').modal('show');
 @endif

    });
</script>

{{-- Sử dụng axious để truyền --}}
{{-- <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
    const form = document.getElementById('addBlockForm');
    form.addEventListener('submit', async (event) => {
        // Ngăn chặn hành vi submit mặc định
        event.preventDefault();

        const formData = new FormData(form);

        try {
            // Gửi request POST bằng Axios tới route lưu trữ phòng trọ
            const response = await axios.post('{{ route('phongtro.store') }}', formData);
            
            // Hiển thị thông báo thành công
            // alert(response.data.message);

            // Reset form sau khi thành công
            form.reset();
            // Trong JavaScript của bạn
            window.location.reload();

            // Ẩn modal sau khi lưu
            $('#addBlockModal').modal('hide');

        } 
        catch (error) 
        {
            // Hiển thị lỗi nếu có
            if (error.response && error.response.data) {
                console.error('Lỗi:', error.response.data);
                alert('Số phòng này đã tồn tại! Vui lòng chọn số phòng khác');
            }
        }
    });
</script> --}}
