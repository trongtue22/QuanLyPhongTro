@extends('layouts.index')

{{-- Phần Heading --}}
@section('heading')
  Danh sách quản lý
@endsection

@section('breadcrumb')
<a href="{{route('QuanLy.view')}}">
    Quản lý
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
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addBlockModal">
                Thêm Mới
            </button>
        </div>
    
        {{-- Search form --}}
        <div>
            <form action="{{ route('QuanLy.search') }}" method="GET" class="form-inline">
                <input type="text" name="query" class="form-control mr-sm-2" placeholder="Tìm kiếm..." onkeydown="if(event.key === 'Enter'){ this.form.submit(); }">
                <button type="submit" class="btn btn-outline-success d-none">Tìm kiếm</button>
            </form>
        </div>

    </div>

     {{-- Form read data --}}
    <table class="table mt-3">
        <thead>
            <tr>
                <th>Tên</th>
                <th>Số điện thoại</th>
                <th>Giới tính</th>
                <th>CCCD</th>
                <th>Hành Động</th>
            </tr>
        </thead>
        <tbody>
            {{-- Nếu sau khi search tìm ra data --}}
            @if($quanlys->isNotEmpty())
                @foreach ($quanlys as $quanly)
                <tr>
                    <td>{{ $quanly->ho_ten }}</td>
                    <td>{{  $quanly->sodienthoai }}</td>
                    <td class="pl-4">{{ $quanly->gioitinh == 0 ? 'Nam' : 'Nữ' }}</td>
                    <td>{{ $quanly->cccd }}</td>
                    
                    <td>
                        <button class="btn btn-warning" data-toggle="modal" data-target="#editModal{{ $quanly->id }}">
                            <i class="fas fa-edit"></i>
                        </button>

                        <button class="btn btn-danger" data-toggle="modal" data-target="#deleteModal{{ $quanly->id }}">
                              <i class="fas fa-trash-alt"></i>
                        </button>

                         <!-- Thẻ để chuyển qua view theo id của Dãy Trọ -->
                         {{-- <a class="btn btn-info" href="{{ route('phongtroDayTro.view', $daytro->id) }}">
                            <i class="fas fa-eye"></i> <!-- Biểu tượng xem -->
                         </a> --}}


                        {{-- Gọi modal xác nhận xóa theo ID của từng Dãy Trọ --}}
                        @include('modals.deleteQuanLy')
                        
                        {{-- Gọi modal chỉnh sửa theo ID của từng Dãy Trọ , ['daytro' => $daytro]--}}
                        @include('modals.updateQuanLy')            
                        
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
            {{$quanlys->onEachSide(1)->links('pagination::bootstrap-4')}}
        </div>    
</div>

@endsection


{{-- Phần ModalThêm Dãy Trọ --}}
@section('addModal')
  @include('modals.addQuanLy') 
@endsection





