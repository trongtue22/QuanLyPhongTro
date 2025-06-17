<div class="modal fade" id="previewModal{{$hoadon->id}}" tabindex="-1" aria-labelledby="previewModalLabel{{$hoadon->id}}" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                @if (!isset($is_pdf))
                    <h5 class="modal-title" id="previewModalLabel{{$hoadon->id}}">Xem trước hóa đơn</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                @endif
            </div>
            <div class="modal-body">
                <div class="container border p-4">
                    <!-- Header -->
                    <div class="d-flex justify-content-between">
                        <div>Ngày lập: <strong>{{ date('d/m/Y', strtotime($hoadon->created_at)) }}</strong></div>
                    </div>

                    <h4 style="text-align: center; margin-top: 20px;">HÓA ĐƠN TIỀN NHÀ</h4>
                    <p class="text-center">Tháng {{ date('m/Y', strtotime($hoadon->created_at)) }}</p>
                    <p><strong>Dãy:</strong> <span class="fs-5">{{ $hoadon->hopdong->khachthue_phongtro->phongtro->daytro->tendaytro }}</span></p>
                    <p><strong>Phòng:</strong> <span class="fs-5">{{ $hoadon->hopdong->khachthue_phongtro->phongtro->sophong }}</span></p>

                    <!-- Bảng hiển thị chi tiết hóa đơn -->
                    <table class="invoice-table" style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
                        <thead style="background-color: #f2f2f2;">
                            <tr>
                                <th style="border: 1px solid #000; padding: 8px;">#</th>
                                <th style="border: 1px solid #000; padding: 8px; text-align: left">Nội dung</th>
                                <th style="border: 1px solid #000; padding: 8px; text-align: right;">Số tiền (VNĐ)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td style="border: 1px solid #000; padding: 8px;">1</td>
                                <td style="border: 1px solid #000; padding: 8px;">Tiền phòng</td>
                                <td style="border: 1px solid #000; padding: 8px; text-align: right;">
                                    {{ number_format($hoadon->hopdong->khachthue_phongtro->phongtro->tienphong, 0, ',', '.') }}
                                </td>
                            </tr>
                            <tr>
                                <td style="border: 1px solid #000; padding: 8px;">2</td>
                                <td style="border: 1px solid #000; padding: 8px;">
                                    Điện (Số cũ: {{ $hoadon->sodiencu }}, Số mới: {{ $hoadon->sodienmoi }})<br>
                                    <small>
                                        Đơn giá: {{ number_format(optional($hoadon->dv)->dien, 0, ',', '.') }} đ/kWh<br>
                                        Tiêu thụ: {{ $hoadon->sodienmoi - $hoadon->sodiencu }} kWh
                                    </small>
                                </td>
                                <td style="border: 1px solid #000; padding: 8px; text-align: right;">
                                    {{ number_format(($hoadon->sodienmoi - $hoadon->sodiencu) * optional($hoadon->dv)->dien, 0, ',', '.') }}
                                </td>
                            </tr>
                            <tr>
                                <td style="border: 1px solid #000; padding: 8px;">3</td>
                                <td style="border: 1px solid #000; padding: 8px;">
                                    Nước (Số cũ: {{ $hoadon->sonuoccu }}, Số mới: {{ $hoadon->sonuocmoi }})<br>
                                    <small>
                                        Đơn giá: {{ number_format(optional($hoadon->dv)->nuoc, 0, ',', '.') }} đ/m³<br>
                                        Tiêu thụ: {{ $hoadon->sonuocmoi - $hoadon->sonuoccu }} m³
                                    </small>
                                </td>
                                <td style="border: 1px solid #000; padding: 8px; text-align: right;">
                                    {{ number_format(($hoadon->sonuocmoi - $hoadon->sonuoccu) * optional($hoadon->dv)->nuoc, 0, ',', '.') }}
                                </td>
                            </tr>
                            <tr>
                                <td style="border: 1px solid #000; padding: 8px;">4</td>
                                <td style="border: 1px solid #000; padding: 8px;">
                                    Wifi<br>
                                    <small>Đơn giá: {{ $hoadon->dv ? number_format($hoadon->dv->wifi, 0, ',', '.') : '' }} đ</small>
                                </td>
                                <td style="border: 1px solid #000; padding: 8px; text-align: right;">
                                    {{ $hoadon->dv ? number_format($hoadon->dv->wifi, 0, ',', '.') : '' }}
                                </td>
                            </tr>
                            <tr>
                                <td style="border: 1px solid #000; padding: 8px;">5</td>
                                <td style="border: 1px solid #000; padding: 8px;">
                                    Rác<br>
                                    <small>Đơn giá: {{ $hoadon->dv ? number_format($hoadon->dv->rac, 0, ',', '.') : '' }} đ</small>
                                </td>
                                <td style="border: 1px solid #000; padding: 8px; text-align: right;">
                                    {{ $hoadon->dv ? number_format($hoadon->dv->rac, 0, ',', '.') : '' }}
                                </td>
                            </tr>
                            <tr>
                                <td style="border: 1px solid #000; padding: 8px;">6</td>
                                <td style="border: 1px solid #000; padding: 8px;">
                                    Gửi xe ({{ $hoadon->hopdong->soxe ?? 0 }} xe)<br>
                                    <small>
                                        Đơn giá: {{ $hoadon->dv ? number_format($hoadon->dv->guixe, 0, ',', '.') : '' }} đ/xe<br>
                                        Tổng: {{ $hoadon->hopdong->soxe ?? 0 }} x {{ $hoadon->dv ? number_format($hoadon->dv->guixe, 0, ',', '.') : '' }}
                                    </small>
                                </td>
                                <td style="border: 1px solid #000; padding: 8px; text-align: right;">
                                    {{ $hoadon->dv ? number_format($hoadon->dv->guixe * ($hoadon->hopdong->soxe ?? 0), 0, ',', '.') : '' }}
                                </td>
                            </tr>
                            <tr>
                                <td style="border: 1px solid #000; padding: 8px;">7</td>
                                <td style="border: 1px solid #000; padding: 8px;">Cọc, phụ phí khác</td>
                                <td style="border: 1px solid #000; padding: 8px; text-align: right;">
                                    {{ number_format($hoadon->phikhac, 0, ',', '.') }}
                                </td>
                            </tr>
                            <tr style="background-color: #fff3cd;">
                                <td colspan="2" style="border: 1px solid #000; padding: 8px; text-align: right; font-weight: bold;">TỔNG CỘNG</td>
                                <td style="border: 1px solid #000; padding: 8px; text-align: right; font-weight: bold; font-size: 16px;">
                                    {{ number_format($hoadon->tongtien, 0, ',', '.') }}
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <table class="mt-4" style="width: 100%; text-align: center;">
                        <tr>
                            <td style="width: 50%;">Người lập phiếu</td>
                            <td style="width: 50%;">Người thanh toán</td>
                        </tr>
                    </table>
                </div>
            </div>
            @if (!isset($is_pdf))
                <div class="modal-footer">
                    <a href="{{route('HoaDon.exportPDF', $hoadon->id)}}" class="btn btn-success">
                        <i class="fas fa-download"></i> Xuất PDF
                    </a>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                </div>
            @endif
        </div>
    </div>
</div>


<style>
    body {
        font-family: "DejaVu Sans", sans-serif;
    }
</style>

