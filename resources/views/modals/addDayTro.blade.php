
<div class="modal fade" id="addBlockModal" tabindex="-1" role="dialog" aria-labelledby="addBlockModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addBlockModalLabel">Thêm Dãy Trọ Mới</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

                {{-- Form create dãy trọ => action="{{route('daytro.store')}}" --}}
                <form action="{{ route('daytro.store') }}" id="addBlockForm" method="POST">
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
                        <label for="name">Tên dãy trọ:</label>
                        <input name="tendaytro" type="text" class="form-control" id="name" placeholder="Nhập tên dãy trọ" required>
                    </div>
                    <div class="form-group">
                        <label for="province">Tỉnh:</label>
                        <select class="form-control" id="province" name="tinh" required>
                            <option value="">Chọn Tỉnh</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="district">Huyện:</label>
                        <select class="form-control" id="district"name="huyen" required>
                            <option value="">Chọn Huyện</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="commune">Xã:</label>
                        <select class="form-control" id="commune" name="xa" required>
                            <option value="">Chọn Xã</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="address">Số Nhà:</label>
                        <input type="text" class="form-control" id="address" placeholder="Nhập số nhà" name="sonha" required>
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

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>


<script>
    $(document).ready(function() 
    {

        // Mở modal nếu có lỗi validation
        @if ($errors->any())
            $('#addBlockModal').modal('show');
        @endif

        // Lấy tỉnh thành
        $.getJSON('https://esgoo.net/api-tinhthanh/1/0.htm', function(data_tinh) {
            if (data_tinh.error == 0) {
                $.each(data_tinh.data, function(key_tinh, val_tinh) {
                    $("#province").append('<option value="'+val_tinh.full_name+'" data-id="'+val_tinh.id+'">'+val_tinh.full_name+'</option>');
                });
            }
        });

        // Khi tỉnh được chọn
        $("#province").change(function() {
            var idtinh = $("#province option:selected").data('id');
            $("#district").html('<option value="">Chọn Huyện</option>');  
            $("#commune").html('<option value="">Chọn Xã</option>');   

            // Lấy quận huyện
            $.getJSON('https://esgoo.net/api-tinhthanh/2/' + idtinh + '.htm', function(data_quan) {
                if (data_quan.error == 0) {
                    $.each(data_quan.data, function(key_quan, val_quan) {
                        $("#district").append('<option value="'+val_quan.full_name+'" data-id="'+val_quan.id+'">'+val_quan.full_name+'</option>');
                    });
                }
            });
        });

        // Khi huyện được chọn
        $("#district").change(function() {
            var idquan = $("#district option:selected").data('id');
            $("#commune").html('<option value="">Chọn Xã</option>');   

            // Lấy phường xã
            $.getJSON('https://esgoo.net/api-tinhthanh/3/' + idquan + '.htm', function(data_phuong) {
                if (data_phuong.error == 0) {
                    $.each(data_phuong.data, function(key_phuong, val_phuong) {
                        $("#commune").append('<option value="'+val_phuong.full_name+'" data-id="'+val_phuong.id+'">'+val_phuong.full_name+'</option>');
                    });
                }
            });
        });

        // Lưu thông tin
        $("#saveChanges").click(function() {
            // Xử lý lưu thông tin
            var formData = {
                name: $("#name").val(),
                province: $("#province").val(),
                district: $("#district").val(),
                commune: $("#commune").val(),
                address: $("#address").val()
            };

            // Gửi dữ liệu tới server (thay đổi URL tới route của bạn)
            $.post('/store-location', formData, function(response) {
                // Xử lý phản hồi
                console.log(response);
                // Đóng modal và làm mới trang hoặc thông báo thành công
                $('#addBlockModal').modal('hide');
                location.reload();
            }).fail(function(xhr) {
                // Xử lý lỗi
                console.log(xhr.responseText);
            });
        });
    });
</script>

</body>
</html>
