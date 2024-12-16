@extends('layouts.index')

{{-- Phần Heading --}}
@section('heading')
   @if(isset($daytro))
   Danh sách phòng trọ trong dãy trọ: {{ $daytro->tendaytro }}
   @elseif($showContent)
   Danh sách phòng trọ
   @else
   Danh sách phòng trọ của khách thuê: {{$khachthue->tenkhachthue}}
   @endif
@endsection

@section('breadcrumb')
@if(isset($daytro))
<a href="{{route('daytro')}}" class="text-dark">
    Dãy trọ &nbsp; 
</a> >
<a href="{{url()->current()}}">
    Phòng trọ trong dãy trọ 
</a> >
@elseif($showContent)
<a href="{{url()->current()}}">
    Phòng trọ 
</a> >
@else
< <a href="{{route('KhachThue.view')}}">
     Quay về 
</a>
@endif
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
        <form action="{{ isset($daytro) ? route('phongtroDayTro.search') : (isset($khachthue) ? route('phongtroKhachThue.search') : route('PhongTro.search')) }}" method="GET" class="form-inline">
            <input type="text" name="query" class="form-control mr-sm-2" placeholder="Tìm kiếm..." onkeydown="if(event.key === 'Enter'){ this.form.submit(); }">
            @if(isset($daytro))
                <input type="hidden" name="daytro_id" value="{{ $daytro->id }}"> {{-- Giá trị của daytro_id nếu có --}}
            @endif
            
            @if(isset($khachthue))
                <input type="hidden" name="khachthue_id" value="{{ $khachthue->id }}"> {{-- Giá trị của khachthue_id nếu có --}}
            @endif   
            <button type="submit" class="btn btn-outline-success d-none">Tìm kiếm</button>
        </form>
        
        

    </div>

     {{-- Form read data --}}
    <table class="table mt-3">
        <thead>
            <tr>
                <th>Phòng số</th>
                <th>Tiền phòng</th>
                
                @if(!$khachthuePhongTro)
                <th>Trạng thái</th>
                @endif

                @if($showContent)
                <th>Số người thuê</th>
                @endif  
                
                @if($showContent || $khachthuePhongTro)
                <th>Dãy trọ </th>
                @endif             
                <th>Hành động</th>

            </tr>
        </thead>
        {{-- Thành phần table đê fetch data ra --}}
        <tbody>
            @if($phongtros->isNotEmpty())
                {{-- Biến phongtro này chứa các data theo từng trạng thái khác nhau --}}
              @foreach ($phongtros as $phongtro)
                <tr>
                    <td class="pl-5">{{ $phongtro->sophong}}</td>
                    <td class="pl-4">{{ number_format($phongtro->tienphong, 0, ',', '.') }}</td>

                    {{-- Trạng thái của phòng --}}
                    @if(!$khachthuePhongTro)
                    <td>{{ $phongtro->status == 0 ? 'Phòng trống' : 'Đã thuê' }}</td>
                    @endif
                    
                    <!-- Hiển thị số lượng khách thuê -->
                    @if($showContent)
                    <td class="pl-5">{{ $phongtro->khachthues->count()}}/4</td>
                    @endif

                    @if($showContent || $khachthuePhongTro)
                    <td class="pl-4">{{ $phongtro->daytro->tendaytro}}</td>
                    @endif 

                    <td>

                        <button class="btn btn-warning" data-toggle="modal" data-target="#editModal{{ $phongtro->id }}">
                            <i class="fas fa-edit"></i>
                        </button>


                        <button class="btn btn-danger" data-toggle="modal" data-target="#deleteModal{{ $phongtro->id }}">
                            <i class="fas fa-trash-alt"></i>
                        </button>

                        {{-- {{ route('khachthuePhongTro.view', $phongtro->id) }} --}}
                        @if(!$khachthuePhongTro)
                        <a class="btn btn-info" href="{{route('khachthuePhongTro.view', $phongtro->id)}}">
                            <i class="fas fa-eye"></i> <!-- Biểu tượng xem -->
                        </a>
                        @endif

                        @include('modals.deletePhongTro')

                        @include('modals.editPhongTro') 
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
                {{$phongtros->onEachSide(1)->links('pagination::bootstrap-4')}}
            </div>  
</div>
@endsection


{{-- Phần Modal Thêm Phòng Trọ --}}
@section('addModal')
@if(isset($daytro))
  @include('modals.addPhongTro')
{{-- Thêm một Modal ở đây nữa => Phòng trọ của khách thuê --}}
@elseif($khachthuePhongTro)
  @include('modals.addPhongTroKhachThue')
@else
  @include('modals.addPhongTroTongQuat');
@endif


@endsection



