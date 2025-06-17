<div class="modal fade" id="terminateModal{{ $hopdong->id }}" tabindex="-1" role="dialog" aria-labelledby="terminateModalLabel{{ $hopdong->id }}" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">

      {{-- Header --}}
      <div class="modal-header bg-secondary text-white">
        <h5 class="modal-title" id="terminateModalLabel{{ $hopdong->id }}">
          Xác nhận thanh lý hợp đồng
        </h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      {{-- Body --}}
      <div class="modal-body">
        Bạn có chắc chắn muốn <strong>thanh lý</strong> hợp đồng cho phòng 
        <strong>{{ $hopdong->khachthue_phongtro->phongtro->sophong ?? 'N/A' }}</strong>
        thuộc dãy <strong>{{ $hopdong->khachthue_phongtro->phongtro->daytro->tendaytro ?? '' }}</strong> 
        không?<br><br>
        Hành động này đẩy hết các khách thuê ra khỏi phòng. 
      </div>

      {{-- Footer --}}
      <div class="modal-footer">
        <form action="{{route('HopDong.thanhly',$hopdong->id)}}" method="POST">
          @csrf
          @method('POST') {{-- hoặc DELETE nếu bạn dùng HTTP verb đó --}}
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
          <button type="submit" class="btn btn-dark">Xác nhận thanh lý</button>
        </form>
      </div>

    </div>
  </div>
</div>
