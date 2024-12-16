{{-- Modal Xác Nhận Xóa theo ID của các data --}}
<div class="modal fade" id="deleteModal{{ $quanly->id }}" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content"> 
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel{{ $quanly->id }}">Xác Nhận Xóa Dãy Trọ: {{$quanly->ho_ten}} </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Bạn có chắc chắn muốn xóa quản lý này?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
                
                {{-- Form Xóa --}}
                <form action="{{ route('QuanLy.delete', $quanly->id) }}" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Xóa</button>
                </form>
                
            </div>
        </div>
    </div>
</div>



