{{-- Modal Xác Nhận Xóa theo ID của các data --}}
<div class="modal fade" id="deleteModal{{ $hoadon->id }}" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel{{ $hoadon->id }}" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content"> 
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel{{ $hoadon->id }}">Xác nhận xóa hóa đơn: {{$hoadon->hopdong->khachthue_phongtro->khachthue->tenkhachthue}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Bạn có chắc chắn muốn xóa hóa đơn này?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
                
                {{-- Form Xóa {{ route('daytro.destroy', $daytro->id) }} --}}
                <form action=" {{ route('HoaDon.delete', $hoadon->id) }}" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Xóa</button>
                </form>
                
            </div>
        </div>
    </div>
</div>
