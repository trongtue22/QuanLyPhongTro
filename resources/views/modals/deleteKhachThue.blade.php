    {{-- Modal Xác Nhận Xóa theo ID của các data --}}
    <div class="modal fade" id="deleteModal{{ $khachthue->id  }}" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel{{ $khachthue->id  }}" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content"> 
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel{{ $khachthue->id  }}">Xác nhận xóa khách thuê: {{ $khachthue->tenkhachthue}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Bạn có chắc chắn muốn xóa khách thuê này?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
                    
                    {{-- Form Xóa khách thuê ra khỏi phòng trọ theo id--}}
                    {{-- Nếu cái trên ko tồn tại thì chuyển qua xóa trong DB của khachthue  --}}
                    <form action="{{ isset($phongtro) 
                      ? route('khachthuePhongTro.destroy', ['phongtro_id' => $phongtro->id, 'khachthue_id' => $khachthue->id]) 
                      : route('KhachThue.delete', ['khachthue_id' => $khachthue->id]) }}" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE') <!-- Phải có dòng này -->
                      <button type="submit" class="btn btn-danger">Xóa</button>
                    </form>
    
                

                    
                    
                </div>
            </div>
        </div>
    </div>


    
