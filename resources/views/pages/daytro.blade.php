@extends('layouts.index')

{{-- Phần Heading --}}
@section('heading')
  Danh sách dãy trọ
@endsection

@section('breadcrumb')
<a href="{{route('daytro')}}">
    Dãy trọ 
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
            <form action="{{ route('daytro.search') }}" method="GET" class="form-inline">
                <input type="text" name="query" class="form-control mr-sm-2" placeholder="Tìm kiếm..." onkeydown="if(event.key === 'Enter'){ this.form.submit(); }">
                <button type="submit" class="btn btn-outline-success d-none">Tìm kiếm</button>
            </form>
        </div>

    </div>

     {{-- Form read data --}}
    <table class="table mt-3">
        <thead>
            <tr>
                <th>Tên Dãy Trọ</th>
                <th>Tỉnh</th>
                <th>Huyện</th>
                <th>Xã</th>
                <th>Số Nhà</th>
                @if(!session()->has('user_type'))
                <th>Phân quyền</th>
                @endif
                <th>Hành Động</th>
            </tr>
        </thead>
        <tbody>
            {{-- Nếu sau khi search tìm ra data --}}
            @if($daytros->isNotEmpty())
                @foreach ($daytros as $daytro)
                <tr>
                    <td>{{ $daytro->tendaytro }}</td>
                    <td>{{ str_replace('Tỉnh ', '', $daytro->tinh) }}</td>
                    <td>{{ str_replace('Huyện ', '', $daytro->huyen) }}</td>
                    <td>{{ str_replace('Xã ', '', $daytro->xa) }}</td>
                    <td>{{ $daytro->sonha }}</td>

                    @if(!session()->has('user_type'))
                    <td>
                        <form action="{{ route('daytro.phanquyen', $daytro->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="form-group">
                                <select name="quanly_id" class="form-control" onchange="this.form.submit()">
                                    <!-- Option to select 'Chưa phân' if no manager is assigned -->
                                    <option value="">Chưa phân</option>
                
                                    <!-- Loop through quanlys to show each manager as an option -->
                                    @foreach ($quanlys as $quanly)
                                        <option value="{{ $quanly->id }}" 
                                            {{ $daytro->quanly_id == $quanly->id ? 'selected' : '' }}>
                                            {{ $quanly->ho_ten }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </form>
                    </td>
                    @endif
                    
                    <td>
                        <button class="btn btn-warning" data-toggle="modal" data-target="#editModal{{ $daytro->id }}">
                            <i class="fas fa-edit"></i>
                        </button>

                        <button class="btn btn-danger" data-toggle="modal" data-target="#deleteModal{{ $daytro->id }}">
                              <i class="fas fa-trash-alt"></i>
                        </button>

                         <!-- Thẻ để chuyển qua view theo id của Dãy Trọ -->
                         <a class="btn btn-info" href="{{ route('phongtroDayTro.view', $daytro->id) }}">
                            <i class="fas fa-eye"></i> <!-- Biểu tượng xem -->
                         </a>


                        {{-- Gọi modal xác nhận xóa theo ID của từng Dãy Trọ --}}
                        @include('modals.deleteDayTro')
                        
                        {{-- Gọi modal chỉnh sửa theo ID của từng Dãy Trọ , ['daytro' => $daytro]--}}
                        @include('modals.updateDayTro')            
                        
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
            {{$daytros->onEachSide(1)->links('pagination::bootstrap-4')}}
        </div>    
</div>

@endsection


{{-- Phần ModalThêm Dãy Trọ --}}
@section('addModal')
  @include('modals.addDayTro')
@endsection





