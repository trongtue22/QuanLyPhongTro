{{-- resources/views/khachthue/hoadon_phong.blade.php --}}
@extends('layouts.app')

@section('title', 'Hóa Đơn Phòng Trọ')

@section('content')

<h2 class="mb-4 fw-semibold text-center display-6">

    <div class="d-flex align-items-center justify-content-between mb-4">
        <div style="width: 160px"></div> {{-- Để cân bằng với nút bên phải --}}
        <h2 class="fw-semibold text-center flex-grow-1 m-0 display-6">
            Hóa đơn phòng {{ $phong->sophong }} ({{ $phong->daytro->tendaytro }})
        </h2>
        <a href="{{ route('khachthue.dashboard') }}" class="btn btn-secondary btn-sm ms-0">
            ← Quay lại danh sách phòng
        </a>
    </div>

        <div class="border rounded px-2 py-1 mx-auto" style="max-width: 360px; background-color: #f9f9f9; font-size: 0.875rem;">
            <div class="fw-bold mb-2">Tổng kết hóa đơn</div>
            <div class="mb-1">Tổng số hóa đơn: <strong>{{ $hoadons->count() }}</strong></div>
            <div class="mb-1">
                Tổng số hóa đơn chưa thanh toán:
                <strong>{{ $hoadons->where('status', 0)->count() }}</strong>
            </div>
            <div class="mb-1 text-success">
                Tổng tiền đã thanh toán:
                <strong>{{ number_format($hoadons->where('status', 1)->sum('tongtien'), 0, ',', '.') }} VNĐ</strong>
            </div>
            
            <div class="mb-0 text-danger">
                Tổng tiền chưa thanh toán:
                <strong>{{ number_format($hoadons->where('status', 0)->sum('tongtien'), 0, ',', '.') }} VNĐ</strong>
            </div>
        </div>

    </div>




    </div>


</h2>

    @if ($hoadons->isEmpty())
        <div class="alert alert-info text-center">
            Phòng này hiện chưa có hóa đơn nào.
        </div>

    @else
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-success text-white fw-bold">
                Danh sách hóa đơn
            </div>
            <div class="card-body p-0">
                <table class="table table-bordered table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Tháng</th>
                            <th>Tiền phòng</th>
                            <th>Tiền dịch vụ</th>
                            <th>Tổng tiền</th>
                            <th>Ngày tạo</th>
                            <th>Trạng thái</th>
                            {{-- <th>Hành động</th> --}}
                        </tr>
                    </thead>
                    <tbody>
                        @if ($hoadons->isEmpty())
                            <tr>
                                <td colspan="6" class="text-center">Không có dữ liệu</td>
                            </tr>
                        @else
                            @foreach ($hoadons as $index => $hoadon)
                            <tr>
                                {{-- STT --}}
                                <td>{{ $index + 1 }}</td>
                            
                                {{-- Tháng --}}
                                <td>{{ \Carbon\Carbon::parse($hoadon->created_at)->format('m-Y') }}</td>
                            
                                {{-- Tiền phòng --}}
                                <td>{{ number_format(optional($hoadon->hopdong?->khachthue_phongtro?->phongtro)->tienphong ?? 0, 0, ',', '.') }} VNĐ</td>
                            
                                {{-- Tiền dịch vụ --}}
                                <td>
                                    {{
                                        number_format(
                                            // Tiền điện
                                            ($hoadon->sodienmoi - $hoadon->sodiencu) * (optional($hoadon->dv)->dien ?? 0)
                                            // Tiền nước
                                            + ($hoadon->sonuocmoi - $hoadon->sonuoccu) * (optional($hoadon->dv)->nuoc ?? 0)
                                            // Wifi
                                            + ($hoadon->dv->wifi ?? 0)
                                            // Rác
                                            + ($hoadon->dv->rac ?? 0)
                                            // Gửi xe
                                            + ($hoadon->dv->guixe ?? 0) * ($hoadon->hopdong->soxe ?? 0),
                                            0, ',', '.'
                                        )
                                    }} VNĐ
                                </td>
                            
                                {{-- Tổng tiền --}}
                                <td>{{ number_format($hoadon->tongtien, 0, ',', '.') }} VNĐ</td>
                            
                                {{-- Ngày tạo --}}
                                <td>{{ \Carbon\Carbon::parse($hoadon->created_at)->format('d-m-Y') }}</td>
                            
                                {{-- Trạng thái --}}
                                <td>
                                    @if ($hoadon->status == 1)
                                        <span class="badge bg-success">Đã thanh toán</span>
                                    @else
                                        <span class="badge bg-warning text-dark">Chưa thanh toán</span>
                                    @endif
                                </td>
                                
                                {{-- Hành động --}}
                                {{-- <td>...</td> --}}
                            </tr>
                            @endforeach
                        @endif
                    </tbody>
                    
                </table>
                <div> 
                   {{$hoadons->onEachSide(1)->links('pagination::bootstrap-4')}}
                </div>  
            </div>
        </div>
    @endif

@endsection
