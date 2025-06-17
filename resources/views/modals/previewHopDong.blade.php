<!-- Modal Preview Hợp Đồng -->
<div class="modal fade" id="previewModal{{ $hopdong->id }}" tabindex="-1" role="dialog" aria-labelledby="previewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
      <div class="modal-content p-4">
        <div class="modal-header border-bottom-0">
        @if (!isset($is_pdf))
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        @endif
        </div>
        <div class="modal-body text-dark" style="font-family: 'DejaVu Sans', sans-serif;">
  
          <div class="text-center mb-3">
            <strong>CỘNG HÒA XÃ HỘI CHỦ NGHĨA VIỆT NAM</strong><br>
            <span><i>Độc lập - Tự do - Hạnh phúc</i></span>
            <hr style="width: 200px; border-top: 1px solid #000;">
          </div>
  
          <h4 class="text-center mb-4"><strong>HỢP ĐỒNG THUÊ PHÒNG TRỌ</strong></h4>
  
          <p><strong>Căn cứ:</strong></p>
          <ul>
            <li>Bộ luật Dân sự năm 2015;</li>
            <li>Luật Nhà ở năm 2014;</li>
            <li>Nhu cầu và thỏa thuận của hai bên.</li>
          </ul>
  
          <p>Hôm nay, ngày {{ $hopdong->created_at->format('d/m/Y') }}, chúng tôi gồm:</p>
  
          <p><strong>BÊN CHO THUÊ (Bên A):</strong></p>
          <p>Họ và tên: {{ $hopdong->khachthue_phongtro->phongtro->daytro->chutro->ho_ten ?? '_____________' }}</p>
          <p>Số điện thoại: {{ $hopdong->khachthue_phongtro->phongtro->daytro->chutro->sodienthoai ?? '_____________' }}</p>
          
          @if (session()->has('user_type') && $hopdong->khachthue_phongtro->phongtro->daytro->quanly)
           <p><strong>BÊN ĐẠI DIỆN CHO BÊN A (QUẢN LÝ):</strong></p>
          <p>Họ tên: {{ $hopdong->khachthue_phongtro->phongtro->daytro->quanly->ho_ten ?? '_____________' }}</p>
          <p>Số điện thoại: {{ $hopdong->khachthue_phongtro->phongtro->daytro->quanly->sodienthoai ?? '_____________' }}</p>
          @endif


          <p><strong>BÊN THUÊ (Bên B):</strong></p>
          <p>Họ và tên: {{ $hopdong->khachthue_phongtro->khachthue->tenkhachthue }}</p>
          <p>Số điện thoại: {{ $hopdong->khachthue_phongtro->khachthue->sodienthoai ?? '_____________' }}</p>
  
          <p><strong>Hai bên cùng thống nhất ký kết hợp đồng thuê phòng trọ với các điều khoản sau:</strong></p>
  
          <ol>
            <li><strong>Địa điểm thuê:</strong> {{ $hopdong->khachthue_phongtro->phongtro->daytro->tendaytro }} - Phòng {{ $hopdong->khachthue_phongtro->phongtro->sophong }}</li>
            <li><strong>Thời hạn thuê:</strong> từ ngày {{ \Carbon\Carbon::parse($hopdong->ngaybatdau)->format('d/m/Y') }} đến {{ \Carbon\Carbon::parse($hopdong->ngayketthuc)->format('d/m/Y') }}</li>
            <li><strong>Tiền cọc:</strong> {{ number_format($hopdong->tiencoc, 0, ',', '.') }} VNĐ</li>
            <li><strong>Số xe:</strong> {{ $hopdong->soxe }} chiếc</li>
          </ol>
  
          <p><strong>Điều khoản chung:</strong> Hai bên cam kết thực hiện đúng nội dung hợp đồng. Trường hợp vi phạm sẽ cùng nhau giải quyết theo quy định pháp luật.</p>
  
          <table style="width: 100%; margin-top: 60px; margin-bottom: 30px;">
            <tr>
              <td style="width: 50%; text-align: center;">
                @if (session()->has('user_type'))
                <strong>ĐẠI DIỆN BÊN A</strong><br>(Ký tên)
                @else
                <strong>BÊN A</strong><br>(Ký tên)
                @endif
              </td>
              <td style="width: 50%; text-align: center;">
                <strong>BÊN B</strong><br>(Ký tên)
              </td>
            </tr>
          </table>
          
        </div>

        @if (!isset($is_pdf))
        <div class="modal-footer">
            <a href="{{route('HopDong.exportPdf', $hopdong->id)}}" class="btn btn-primary" target="_blank">
                <i class="fas fa-file-pdf"></i> Xuất PDF
              </a>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
        </div>
          @endif

       
      </div>
    </div>
  </div>


  <style>
    body {
        font-family: 'DejaVu Sans', sans-serif;
    }

    .text-center {
        text-align: center;
    }

    .text-justify {
        text-align: justify;
    }

    ul {
        padding-left: 20px;
    }

    .modal-body {
        font-size: 14px;
        line-height: 1.6;
    }
</style>
