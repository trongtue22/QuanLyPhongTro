{{-- Modal Xác Nhận Xóa theo ID của các data --}}
<div class="modal fade" id="deleteModal{{ $phongtro->id  }}" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel{{ $phongtro->id }}" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content"> 
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel{{$phongtro->id}}">Xác nhận xóa phòng trọ: {{$phongtro->sophong }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            {{-- Phần xóa phòng --}}
            <div class="modal-body">
                Bạn có chắc chắn muốn xóa dãy trọ này?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
                
                {{-- Form Xóa --}}
                @if(!$khachthuePhongTro)
                    <form action="{{ route('phongtroDayTro.destroy', $phongtro->id) }}" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Xóa</button>
                    </form>
                @else
                    <form method="POST" action="{{ route('phongtroKhachThue.destroy', ['khachthue_id' => $khachthue->id, 'phongtro_id' => $phongtro->id]) }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Xóa</button>
                    </form>
                @endif
                

            </div>
        </div>
    </div>
</div>
