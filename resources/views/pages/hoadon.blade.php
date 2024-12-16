@extends('layouts.index')

{{-- Phần Heading --}}
@section('heading')
  Danh sách hóa đơn
@endsection

@section('breadcrumb')
<a href="{{route('HoaDon.view')}}">
    Hóa đơn
</a> >
@endsection



{{-- Phần content --}}
@section('content')

{{-- mt-1 để nó gần hới header --}}
<div class="container mt-1">
    
    
    {{-- mt-1 để nó gần hới header --}}
    <div class="container mt-1 d-flex justify-content-between align-items-center">
        
        

        {{-- Nút thêm mới data vào Table --}}
        <div>
            <a href="{{ route('HoaDonAdd.view') }}" class="btn btn-primary">
                Thêm Mới
            </a>
        </div>
    
        {{-- Search form --}}
        <div>
            <form action="{{route('HoaDon.search') }}" method="GET" class="form-inline">
                <input type="text" name="query" class="form-control mr-sm-2" placeholder="Tìm kiếm..." onkeydown="if(event.key === 'Enter'){ this.form.submit(); }">
                <button type="submit" class="btn btn-outline-success d-none">Tìm kiếm</button>
            </form>
        </div>

    </div>

     {{-- Form read data --}}
<div class="table-responsive">
    <table class="table mt-2" style="white-space: nowrap;">
        <thead>
            <tr>
                <th>Dãy Trọ</th>
                <th>Số phòng</th>
                <th>Khách thuê</th>
                <th>Ngày tạo hóa đơn</th>
                <th>Tiền phòng</th>
                <th>Điện cũ</th>
                <th>Điện mới</th>
                <th>Nước cũ</th>
                <th>Nước mới</th>
                <th>Tiền wifi</th>
                <th>Tiền rác</th>
                <th>Tiền gửi xe</th>
                <th>Tổng tiền</th>
                <th>Trạng thái</th>
                <th>Hành động</th>
                

            </tr>
        </thead>
        <tbody>
            {{-- Nếu sau khi search tìm ra data --}}
            @if($hoadons->isNotEmpty())
            @foreach ($hoadons as $hoadon)
            <tr>
                {{-- Dãy trọ --}}
                <td class="pl-4">{{ $hoadon->hopdong->khachthue_phongtro->phongtro->daytro->tendaytro }}</td>
                
                {{-- Số phòng --}}
                <td class="pl-5">{{ $hoadon->hopdong->khachthue_phongtro->phongtro->sophong }}</td>
                
                {{-- Khách thuê --}}
                <td class="pl-4">{{ $hoadon->hopdong->khachthue_phongtro->khachthue->tenkhachthue }}</td>
                
                {{-- Ngày tạo hóa đơn --}}
                <td class="pl-5">
                    @if($hoadon->created_at)
                        {{ $hoadon->created_at->format('d-m-Y') }}
                    @else
                        N/A
                    @endif
                </td>
                
                {{-- Tiền phòng --}}
                <td class="pl-4">{{ number_format($hoadon->hopdong->khachthue_phongtro->phongtro->tienphong,0, ',', '.') }} </td>
                
                {{-- Điện cũ --}}
                <td class="pl-4">{{ number_format($hoadon->sodiencu, 0, ',', '.') }}</td>

                {{-- Điện mới --}}
                <td class="pl-4">{{ number_format($hoadon->sodienmoi, 0, ',', '.') }}</td>

                {{-- Nước cũ --}}
                <td class="pl-4">{{ number_format($hoadon->sonuoccu, 0, ',', '.') }}</td>

                {{-- Nước mới --}}
                <td class="pl-4">{{ number_format($hoadon->sonuocmoi, 0, ',', '.') }}</td>
                
                {{-- Tiền wifi --}}
                <td class="pl-5">{{ number_format($hoadon->dichvu->wifi,0, ',', '.') }}</td>
                
                {{-- Tiền rác --}}
                <td class="pl-5">{{ number_format($hoadon->dichvu->rac,0, ',', '.') }}</td>
                
                {{-- Tiền gửi xe --}}
                <td class="pl-5"> {{number_format($hoadon->dichvu->guixe * $hoadon->hopdong->soxe) }}</td>
                
                {{-- Tổng tiền --}}
                <td class="pl-4">{{ number_format($hoadon->tongtien,0, ',', '.') }} </td>
                
                {{-- Trạng thái --}}
                <td class="">
                    <form action="{{route('HoaDon.updateStatus', $hoadon->id)}}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <select name="status" class="form-control" onchange="this.form.submit()">
                                <option value="0" {{ $hoadon->status == 0 ? 'selected' : '' }}>Chưa thanh toán</option>
                                <option value="1" {{ $hoadon->status == 1 ? 'selected' : '' }}>Đã thanh toán</option>
                            </select>
                        </div>
                    </form>
                </td>

                

                {{-- Hành động --}}

                <td>
                    {{-- Chức năng udpate --}}
                    @if ($hoadon->status == 1)
                    <button class="btn btn-warning" disabled>
                        <i class="fas fa-edit"></i>
                    </button>
                     @else
                        <a class="btn btn-warning" href="{{ route('HoaDon.viewUpdate', $hoadon->id) }}">
                            <i class="fas fa-edit"></i>
                        </a>
                    @endif

                    <button class="btn btn-danger" data-toggle="modal" data-target="#deleteModal{{$hoadon->id }}">
                          <i class="fas fa-trash-alt"></i>
                    </button> 

                    {{-- Gọi modal xác nhận xóa theo ID của từng Dãy Trọ --}}
                    @include('modals.deleteHoaDon')

                    
                    
                    {{-- Gọi modal chỉnh sửa theo ID của từng Dãy Trọ , ['daytro' => $daytro]--}}
                    {{-- @include('')             --}}
                    
                </td>

            <td>

            @endforeach 
            {{-- Nếu sau khi search không tìm ra data --}}
            @else 
                <tr>
                    <td colspan="6" class="text-center">
                        <h4>Không có dữ liệu</h4>
                    </td>
                </tr>
            @endif
           
        </tbody>
    </table>
            <!-- Hiển thị các liên kết phân trang -->
        <div> 
            {{$hoadons->onEachSide(1)->links('pagination::bootstrap-4')}}
        </div>    
</div>
</div>



@endsection







