<!-- Modal for adding new room -->
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
                <form action="{{ route('phongtroKhachThue.stored') }}" id="addBlockForm" method="POST">
                    @csrf

                    <input type="hidden" name="khachthue_id" value="{{ $khachthue->id }}">
                    
                    <!-- Display Errors if any -->
                    @if ($errors->any())
                        @foreach ($errors->all() as $error)
                            <div class="alert alert-danger small" role="alert" style="margin-bottom: 8px;">
                                <i class="fas fa-exclamation-circle"></i>
                                <strong>Thông báo!</strong> {{$error}}
                            </div>
                        @endforeach
                    @endif

                    <!-- Dãy trọ Dropdown -->
                    <div class="form-group">
                        <label for="daytro">Dãy trọ:</label>
                        <select name="daytro_id" class="form-control" id="daytro" required>
                            <option value="">Chọn dãy trọ</option>
                            @foreach($daytros as $daytro)
                                <option value="{{ $daytro->id }}">{{ $daytro->tendaytro }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Phòng trọ Dropdown (Initially Disabled) -->
                    <div class="form-group">
                        <label for="phongtro">Phòng trọ:</label>
                        <select name="phongtro_id" class="form-control" id="phongtro" disabled required>
                            <option value="">Chọn phòng trọ</option>
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


<!-- Script to Handle Phongtro Dropdown Population -->
{{-- <script>
    document.addEventListener('DOMContentLoaded', function () {
        var daytroSelect = document.getElementById('daytro');
        var phongtroSelect = document.getElementById('phongtro');

        console.log(phongtroSelect);

        // Fetch the list of rooms passed from the controller
        var phongtros = @json($daytros->pluck('phongtros')->flatten());  // Flatten to get all rooms in a single array
        console.log("Data: ",phongtros);
        
        
        // When the user selects a Dãy trọ (daytro)
        daytroSelect.addEventListener('change', function () 
        {
            
            var daytroId = daytroSelect.value;

            // Reset phongtro options
            phongtroSelect.innerHTML = '<option value="">Chọn phòng trọ</option>';
            phongtroSelect.disabled = false;

            if (daytroId) 
            {
                console.log("Selected Dãy trọ ID: ", daytroId);
                // Filter rooms by the selected dãy trọ ID
                var filteredPhongtros = phongtros.filter(function (phongtro) 
                {
                    return phongtro.daytro_id == daytroId;
                });
                
                console.log(filteredPhongtros);

                // Add the filtered rooms to the phongtro select element
                if (filteredPhongtros.length > 0) 
                {
                    filteredPhongtros.forEach(function (phongtro) {
                        var option = document.createElement('option');
                        option.value = phongtro.id;
                        option.text = 'Phòng số: ' + phongtro.sophong;
                        phongtroSelect.appendChild(option);
                    });
                    phongtroSelect.disabled = false;
                } else 
                {
                    console.log('No rooms available for the selected daytro');
                }
            }
        });
    });



</script> --}}

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var daytroSelect = document.getElementById('daytro');
        var phongtroSelect = document.getElementById('phongtro');

        // Fetch the list of daytro and their associated rooms passed from the controller
        var daytros = @json($daytros); // Dữ liệu dãy trọ đã bao gồm phòng trọ
        console.log("Daytro Data: ", daytros);

        // When the user selects a Dãy trọ (daytro)
        daytroSelect.addEventListener('change', function () {
            var daytroId = daytroSelect.value;

            // Reset phongtro options
            phongtroSelect.innerHTML = '<option value="">Chọn phòng trọ</option>';
            phongtroSelect.disabled = true;

            if (daytroId) {
                console.log("Selected Dãy trọ ID: ", daytroId);

                // Find the selected daytro and its associated rooms
                var selectedDaytro = daytros.find(function (daytro) {
                    return daytro.id == parseInt(daytroId); // Ensure type consistency
                });

                if (selectedDaytro && selectedDaytro.phongtros.length > 0) {
                    console.log("Phòng trọ thuộc dãy trọ đã chọn: ", selectedDaytro.phongtros);

                    // Add the filtered rooms to the phongtro select element
                    selectedDaytro.phongtros.forEach(function (phongtro) {
                        var option = document.createElement('option');
                        option.value = phongtro.id;
                        option.text = 'Phòng số: ' + phongtro.sophong;
                        phongtroSelect.appendChild(option);
                    });

                    // Enable the phongtro dropdown
                    phongtroSelect.disabled = false;
                } else {
                    console.log('Không có phòng trọ nào thuộc dãy trọ đã chọn.');
                }
            }
        });
    });
</script>