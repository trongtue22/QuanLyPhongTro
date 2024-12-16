{{-- Modal Chỉnh Sửa Dãy Trọ --}}
<div class="modal fade" id="editModal{{ $daytro->id }}" tabindex="-1" role="dialog" aria-labelledby="editModalLabel{{ $daytro->id }}" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content"> 
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel{{ $daytro->id }}">Chỉnh Sửa Dãy Trọ: {{ $daytro->tendaytro }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('daytro.update', $daytro->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                    
                         {{-- Thông báo lỗi --}}
                        @if ($errors->{'update_errors_' . $daytro->id}->any())
                            @foreach ($errors->{'update_errors_' . $daytro->id}->all() as $error)
                                <div class="alert alert-danger small" role="alert" style="margin-bottom: 8px;">
                                    <i class="fas fa-exclamation-circle"></i>
                                    <strong>Thông báo!</strong> {{ $error }}  
                                </div>
                            @endforeach
                        @endif

                        
           
                    <div class="form-group">
                        <label for="tendaytro">Tên Dãy Trọ</label>
                        <input type="text" class="form-control" id="tendaytro" name="tendaytro" value="{{ $daytro->tendaytro }}" required>
                    </div>
                    <div class="form-group">
                        <label for="province{{ $daytro->id }}">Tỉnh</label>
                        <select class="form-control" id="province{{ $daytro->id }}" name="tinh" required>
                            <option value="{{ $daytro->tinh }}" selected>{{ $daytro->tinh }}</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="district{{ $daytro->id }}">Huyện</label>
                        <select class="form-control" id="district{{ $daytro->id }}" name="huyen" required>
                            <option value="{{ $daytro->huyen }}" selected>{{ $daytro->huyen }}</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="commune{{ $daytro->id }}">Xã</label>
                        <select class="form-control" id="commune{{ $daytro->id }}" name="xa" required>
                            <option value="{{ $daytro->xa }}" selected>{{ $daytro->xa }}</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="sonha">Số Nhà</label>
                        <input type="text" class="form-control" id="sonha" name="sonha" value="{{ $daytro->sonha }}" required>
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






<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

{{-- <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Kiểm tra xem biến redirectUrl có tồn tại hay không
        @if(!empty($redirectUrl))
            var redirectUrl = "{{ $redirectUrl }}";
            // Thay thế URL hiện tại bằng URL mới
            window.location.replace(redirectUrl);
        @endif
    });
</script> --}}



{{-- @if (session('success'))
    <script>
        if (window.history.replaceState) {
            // Replace the current history entry with the new one
            window.history.replaceState(null, null, window.location.href);
        }
    </script>
@endif --}}


<script>       
    $(document).ready(function() {
        
        // Mở modal nếu có lỗi validation
        @if ($errors->{'update_errors_' . $daytro->id}->any())
            $('#editModal{{ $daytro->id }}').modal('show');
        @endif


        let provincesData = null;
        let districtsData = {};
        let communesData = {};

        // Preload the provinces data when the page loads
        $.getJSON('https://esgoo.net/api-tinhthanh/1/0.htm', function(data_tinh) {
            if (data_tinh.error === 0) {
                provincesData = data_tinh.data;
            }
        });

        $('#editModal{{ $daytro->id }}').on('shown.bs.modal', function () {
            const daytroId = {{ $daytro->id }};
            const $provinceSelect = $('#province' + daytroId);
            const $districtSelect = $('#district' + daytroId);
            const $communeSelect = $('#commune' + daytroId);

            // Populate the province dropdown with preloaded data
            if (provincesData) {
                $provinceSelect.empty();
                $.each(provincesData, function(key, value) {
                    $provinceSelect.append('<option value="'+value.full_name+'" data-id="'+value.id+'">'+value.full_name+'</option>');
                });
                $provinceSelect.val('{{ $daytro->tinh }}').trigger('change');
            }

            // When province is selected, load districts (with caching)
            $provinceSelect.change(function() {
                const idtinh = $(this).find('option:selected').data('id');
                if (!districtsData[idtinh]) {
                    $.getJSON('https://esgoo.net/api-tinhthanh/2/' + idtinh + '.htm', function(data_quan) {
                        if (data_quan.error === 0) {
                            districtsData[idtinh] = data_quan.data;
                            populateDistricts(districtsData[idtinh]);
                        }
                    });
                } else {
                    populateDistricts(districtsData[idtinh]);
                }
            });

            function populateDistricts(districts) {
                $districtSelect.empty().append('<option value="">Chọn Huyện</option>');
                $.each(districts, function(key, value) {
                    $districtSelect.append('<option value="'+value.full_name+'" data-id="'+value.id+'">'+value.full_name+'</option>');
                });
                $districtSelect.val('{{ $daytro->huyen }}').trigger('change');
            }

            // When district is selected, load communes (with caching)
            $districtSelect.change(function() {
                const idquan = $(this).find('option:selected').data('id');
                if (!communesData[idquan]) {
                    $.getJSON('https://esgoo.net/api-tinhthanh/3/' + idquan + '.htm', function(data_phuong) {
                        if (data_phuong.error === 0) {
                            communesData[idquan] = data_phuong.data;
                            populateCommunes(communesData[idquan]);
                        }
                    });
                } else {
                    populateCommunes(communesData[idquan]);
                }
            });

            function populateCommunes(communes) {
                $communeSelect.empty().append('<option value="">Chọn Xã</option>');
                $.each(communes, function(key, value) {
                    $communeSelect.append('<option value="'+value.full_name+'" data-id="'+value.id+'">'+value.full_name+'</option>');
                });
                $communeSelect.val('{{ $daytro->xa }}');
            }

            // Trigger the initial change to load districts and communes
            $provinceSelect.trigger('change');
        });
    });
</script>

