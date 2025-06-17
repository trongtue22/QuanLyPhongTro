@extends('layouts.index')

{{-- Phần Heading --}}
@section('heading')
  Danh sách hợp đồng
@endsection

@section('breadcrumb')
<a href="{{route('daytro')}}">
    Hợp đồng 
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
            <a href="{{ route('HopDong.viewAdd') }}" class="btn btn-primary">
                Thêm Mới
            </a>
        </div>
        
    
        {{-- Search form --}}
        <div>
            <form action="{{ route('HopDong.search') }}" method="GET" class="form-inline">
                <input type="text" name="query" class="form-control mr-sm-2" placeholder="Tìm kiếm..." onkeydown="if(event.key === 'Enter'){ this.form.submit(); }">
                <button type="submit" class="btn btn-outline-success d-none">Tìm kiếm</button>
            </form>
        </div>

    </div>

     {{-- Form read data --}}
    <table class="table mt-3">
        <thead>
            <tr>
                {{-- Phòng được đăng ký hopdong thue phong --}}
                <th>Dãy trọ</th> 
                <th>Số phòng</th> 
                {{-- Người đại diện cho hop dong đó --}}
                <th>Khách thuê</th> 
                <th>Ngày bắt đầu</th>
                <th>Ngày hết hạn</th>
                <th>Số người thuê</th>
                <th>Số Xe</th>
                <th>Tiền cọc</th>
                <th>Trạng thái</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            {{-- Nếu sau khi search tìm ra data --}}
            @if($hopdongs->isNotEmpty())
                 @foreach ($hopdongs as $hopdong)
                <tr>
                     {{-- Hiển thị số phòng --}}
                     <td class="pl-4">{{ $hopdong->khachthue_phongtro->phongtro->daytro->tendaytro }}</td>
                     <td class="pl-5">{{ $hopdong->khachthue_phongtro->phongtro->sophong ?? 'N/A' }}</td>
                    
                    

                     <td class="pl-4">{{ $hopdong->khachthue_phongtro->khachthue->tenkhachthue }}</td>



                     {{-- Ngày bắt đầu hợp đồng --}}
                     <td>{{ $hopdong->ngaybatdau }}</td>
 
                     {{-- Ngày hết hạn hợp đồng --}}
                     <td>{{ $hopdong->ngayketthuc }}</td>
                    
                     {{-- Số người thuê --}}
                     <td class="pl-5">{{ $hopdong->songuoithue }}</td>
 
                     {{-- Số xe --}}
                     <td class="pl-4">{{ $hopdong->soxe }}</td>
 
                     {{-- Tiền cọc --}}
                     <td>{{ number_format($hopdong->tiencoc, 0, ',', '.') }}</td>

                    
                     <td class="pl-4">
                        @if (now()->greaterThan($hopdong->ngayketthuc))
                            Hết hạn
                        @else
                            Còn hạn
                        @endif
                    </td>
                    {{-- Các chức năng --}}
                    <td>
                        <a class="btn btn-warning" href="{{ route('HopDong.viewUpdate',  $hopdong->id) }}">
                            <i class="fas fa-edit"></i>
                        </a>

                        <button class="btn btn-danger" data-toggle="modal" data-target="#deleteModal{{$hopdong->id}}">
                              <i class="fas fa-trash-alt"></i>
                        </button> 

                        {{-- Gọi modal xác nhận xóa theo ID của từng Dãy Trọ --}}
                        @include('modals.deleteHopDong')
                                    
                        
                        <button class="btn btn-info" data-toggle="modal" data-target="#previewModal{{$hopdong->id}}">
                            <i class="fas fa-file-pdf"></i> 
                        </button>

                        @include('modals.previewHopDong', ['hopdong' => $hopdong])


                    </td>

                    
                </tr>
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
            {{$hopdongs->onEachSide(1)->links('pagination::bootstrap-4')}}
        </div>    
</div>

@endsection


{{-- Phần ModalThêm Dãy Trọ --}}
@section('addModal')
  {{-- @include('modals.addHopDong') --}}
@endsection





