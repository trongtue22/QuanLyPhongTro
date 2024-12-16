@extends('layouts.index')

{{-- Phần Heading --}}
@section('heading')
  @if(isset($phongtro))
     Danh sách khách thuê trong phòng trọ: {{$phongtro->sophong}}
  @else
     Danh sách khách thuê 
  @endif
@endsection

@section('breadcrumb')
    {{-- Khách thuê chi tiết của dãy trọ và phòng trọ --}}
    @if(!$khachthuetongquat)
      <  <a href="{{ session('prev_url') }}">
            Quay về
        </a>
    @else
        <a href="{{ url()->current() }}">
            Khách thuê  
        </a> >
   @endif

@endsection



{{-- Phần content --}}
@section('content')

{{-- mt-1 để nó gần hới header --}}
<div class="container mt-1">
    {{-- mt-1 để nó gần hới header --}}
    <div class="container mt-1 d-flex justify-content-between align-items-center">
        
        {{-- Nút thêm mới data vào Table --}}
        @if($khachthues->count() >= 4 && isset($phongtro))
            <div class="d-flex align-items-center">
                <!-- Nút Thêm Mới -->
                <button type="button" class="btn btn-primary mr-3" data-toggle="modal" data-target="#addBlockModal" disabled>
                    Thêm Mới
                </button>
            
                <!-- Thông báo lỗi -->
                <div class="alert alert-danger mb-0" role="alert" style="display: inline-block;">
                    <i class="fas fa-exclamation-triangle"></i> <!-- Icon cảnh báo -->
                    Số lượng khách thuê đã đạt tối đa (4/4), cần xóa bớt để thêm mới
                </div>
            </div>
        @else
         <div>
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addBlockModal">
                Thêm Mới
            </button>
          </div>
        @endif
        
        {{-- Search form --}}
        <form action="{{ isset($phongtro) 
            ? route('khachthuePhongTro.search') 
            : route('KhachThue.search') }}" 
            method="GET" class="form-inline">
            <input type="text" name="query" class="form-control mr-sm-2" placeholder="Tìm kiếm..." 
             onkeydown="if(event.key === 'Enter'){ this.form.submit(); }">
            @if(isset($phongtro))
            <input type="hidden" name="phongtro_id" value="{{ $phongtro->id }}">
            @endif
            <button type="submit" class="btn btn-outline-success d-none">Tìm kiếm</button>
        </form>


    </div>

     {{-- Form read data --}}
    <table class="table mt-3">
        <thead>
            <tr>
                <th>Tên khách thuê</th>
                <th>Số điện thoại</th>
                <th>Ngày sinh </th>
                <th>CCCD</th> 
                <th>Giới tính</th>
                @if($khachthuetongquat)
                <th>Trạng thái</th>
                @endif
                <th>Hành động</th>
            </tr>
        </thead>
        {{-- Thành phần table đê fetch data ra --}}
        <tbody>
            @if($khachthues->isNotEmpty())
              @foreach ($khachthues as $khachthue)
                <tr>
                    <td class="pl-4">{{ $khachthue->tenkhachthue }}</td>
                    <td class="pl-4">{{ $khachthue->sodienthoai }}</td>
                    <td class="pl-3">{{ $khachthue->ngaysinh }}</td>
                    <td class="pl-2">{{ $khachthue->cccd }}</td>
                    <td class="pl-4">{{ $khachthue->gioitinh == 0 ? 'Nam' : 'Nữ' }}</td>
                   
                    <!-- Hiển thị số lượng phòng thuê -->
                    @if($khachthuetongquat)
                    <td class="pl-4">
                        {{ $khachthue->phongtros_count > 0 ? 'Đã thuê' : 'Chưa thuê' }}
                    </td>
                    @endif
                    
                    <td>

                        <button class="btn btn-warning" data-toggle="modal" data-target="#editModal{{ $khachthue->id }}">
                            <i class="fas fa-edit"></i>
                        </button>
                       
                        <button class="btn btn-danger" data-toggle="modal" data-target="#deleteModal{{ $khachthue->id }}">
                            <i class="fas fa-trash-alt"></i>
                        </button>

                      
                        @include('modals.updateKhachThue')  

                        
                        @include('modals.deleteKhachThue')
                        
                        {{-- Phòng của khách thuê --}}
                        @if($khachthuetongquat)
                        <a class="btn btn-info" href="{{route('phongtroKhachThue.view', $khachthue->id)}}">
                            <i class="fas fa-eye"></i> <!-- Biểu tượng xem -->
                        </a>
                        @endif
                    </td>
                 </tr>
                @endforeach
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
                {{$khachthues->onEachSide(1)->links('pagination::bootstrap-4')}}
            </div>  
</div>

@endsection



{{-- Phần Modal Thêm Dãy Trọ (add view)--}}
@section('addModal')
 
  @include('modals.addKhachThue')

@endsection


<style>
.text-danger {
    font-weight: bold;
    margin-left: 10px;
    font-size: 14px;
}
</style>




