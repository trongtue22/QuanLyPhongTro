{{-- Modal Xác Nhận Xóa theo ID của các data --}}
<div class="modal fade" id="deleteModal{{ $hopdong->id }}" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel{{ $hopdong->id }}" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content"> 
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel{{ $hopdong->id }}">Xác nhận xóa hợp đồng: Số phòng {{ $hopdong->khachthue_phongtro->phongtro->sophong }} - Dãy trọ {{$hopdong->khachthue_phongtro->phongtro->daytro->tendaytro}} </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Bạn có chắc chắn muốn xóa hợp đồng này ?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
                
                {{-- Form Xóa --}}
                <form action="{{route('HopDong.delete', $hopdong->id  )}}" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Xóa</button>
                </form>
                
            </div>
        </div>
    </div>
</div>
