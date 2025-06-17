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
            @if(!session()->has('user_type'))
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addBlockModal">
                Thêm Mới
            </button>
            @endif
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
                <th>Sự cố </th>
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

                    {{-- Cột sự cố --}}
                    <td>
                        @php $soSuCo = $phongtro->sucophongtro->count(); @endphp
                        @if($soSuCo > 0)
                            <span class="badge badge-danger">
                                <i class="fas fa-exclamation-triangle"></i> {{ $soSuCo }}
                            </span>
                            <button class="btn btn-sm btn-warning ml-2" data-toggle="modal" data-target="#modalXemSuCo{{ $phongtro->id }}">Xem</button>
                        @else
                            <button class="btn btn-sm btn-secondary" data-toggle="modal" data-target="#modalBaoSuCo{{ $phongtro->id }}">Báo sự cố</button>
                        @endif
    
                        {{-- Modal Báo sự cố --}}
                        <div class="modal fade" id="modalBaoSuCo{{ $phongtro->id }}" tabindex="-1" role="dialog">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content">
                                   <form action="{{ route('suco.store', $phongtro->id) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="phongtro_id" value="{{ $phongtro->id }}">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Báo sự cố phòng {{ $phongtro->sophong }}</h5>
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        </div>
    
                                        <div class="modal-body">
                                            <div class="table-responsive">
                                                <table class="table table-bordered" id="suco-table">
                                                    <thead>
                                                        <tr>
                                                            <th style="width: 30%">Loại sự cố</th>
                                                            <th>Mô tả sự cố</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="suco-container">
                                                        <tr class="suco-row">
                                                            <td>
                                                                <select name="loai_suco[]" class="form-control" required>
                                                                    <option value="dien">Mất điện</option>
                                                                    <option value="nuoc">Mất nước</option>
                                                                    <option value="nha">Hỏng hóc nhà cửa</option>
                                                                </select>
                                                            </td>
                                                            <td>
                                                                <div class="input-group">
                                                                    <input type="text" name="mota[]" class="form-control" placeholder="Nhập mô tả sự cố..." required>
                                                                    <div class="input-group-append">
                                                                        <button type="button" class="btn btn-danger btn-remove-suco" onclick="xoa(this)">
                                                                            <i class="fas fa-trash"></i>
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
    
                                            <button type="button" class="btn btn-sm btn-success" onclick="themSuCo(this)">
                                                <i class="fas fa-plus"></i> Thêm sự cố
                                            </button>
                                        </div>
    
                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-primary">Gửi tất cả</button>
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
                                        </div>
                                    </form>
    
                                </div>
                            </div>
                        </div>


                        {{--  Hiện thị ra sau khi đã thêm sự cố --}}
                        <div class="modal fade" id="modalXemSuCo{{ $phongtro->id }}" tabindex="-1" role="dialog">
                            <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
                                <div class="modal-content">
                                    <form id="formSuCo{{ $phongtro->id }}" action="{{ route('suco.tonghop', $phongtro->id) }}" method="POST">
                                        @csrf
                                        <div class="modal-header">
                                            <h5 class="modal-title">Sự cố phòng {{ $phongtro->sophong }}</h5>
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="table-responsive">
                                                <table class="table table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th style="min-width:110px">Ngày báo</th>
                                                            <th style="min-width: 160px">Loại sự cố</th>
                                                            <th style="min-width:250px">Mô tả sự cố</th>
                                                            <th style="min-width:100px;">Hoàn tất</th>
                                                            <th style="min-width:100px;">Cập nhật</th>
                                                            <th style="min-width:80px;">Xóa</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="suco-table-{{ $phongtro->id }}">
                                                        {{-- Sự cố đã lưu --}}
                                                        @foreach($phongtro->sucophongtro as $suco)
                                                        <tr data-id="{{ $suco->id }}">
                                                            <td>{{ $suco->created_at->format('d/m/Y') }}</td>
                                                            <td>
                                                                <span class="loai-su-co-text">{{ $suco->loai_su_co == 'dien' ? 'Mất điện' : ($suco->loai_su_co == 'nuoc' ? 'Mất nước' : ($suco->loai_su_co == 'nha' ? 'Hỏng hóc nhà cửa' : $suco->loai_su_co)) }}</span>
                                                                <select name="loai_suco_update[{{ $suco->id }}]" class="form-control d-none loai-su-co-edit" required>
                                                                    <option value="dien" {{ $suco->loai_su_co == 'dien' ? 'selected' : '' }}>Điện</option>
                                                                    <option value="nuoc" {{ $suco->loai_su_co == 'nuoc' ? 'selected' : '' }}>Nước</option>
                                                                    <option value="nha" {{ $suco->loai_su_co == 'nha' ? 'selected' : '' }}>Hỏng hóc nhà cửa</option>
                                                                </select>
                                                            </td>
                                                            <td>
                                                                <span class="mo-ta-text">{{ $suco->mo_ta }}</span>
                                                                <input type="text" name="mota_update[{{ $suco->id }}]" class="form-control d-none mo-ta-edit" value="{{ $suco->mo_ta }}" required>
                                                            </td>
                                                            <td class="align-middle">
                                                                <div class="d-flex align-items-center justify-content-center">
                                                                    <input type="checkbox" name="hoantat_ids[]" value="{{ $suco->id }}" class="mb-0">
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <button type="button" class="btn btn-sm btn-info" onclick="toggleEditSuCo(this)">
                                                                    <i class="fas fa-edit"></i> Cập nhật
                                                                </button>
                                                            </td>
                                                            <td></td>
                                                        </tr>
                                                        @endforeach
                                                        {{-- Sự cố mới sẽ được JS thêm vào đây --}}
                                                    </tbody>
                                                </table>
                                            </div>
                                            <button type="button" class="btn btn-sm btn-success" onclick="themSuCoVaoBang({{ $phongtro->id }})">
                                                <i class="fas fa-plus"></i> Thêm sự cố
                                            </button>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-primary">Gửi</button>
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>


                        </td>




                    <td>
                        @if(!session()->has('user_type'))
                        <button class="btn btn-warning" data-toggle="modal" data-target="#editModal{{ $phongtro->id }}">
                            <i class="fas fa-edit"></i>
                        </button>
                        @endif

                        @if(!session()->has('user_type'))
                        <button class="btn btn-danger" data-toggle="modal" data-target="#deleteModal{{ $phongtro->id }}">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                        @endif

                        {{-- {{ route('khachthuePhongTro.view', $phongtro->id) }} --}}
                        @if(!$khachthuePhongTro)
                        <a class="btn btn-info" href="{{route('khachthuePhongTro.view', $phongtro->id)}}">
                            <i class="fas fa-eye"></i> <!-- Biểu tượng xem -->
                        </a>
                        @endif

                        {{-- Gọi modal xác nhận xóa theo ID của từng Phòng Trọ --}}


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

<script>
    function themSuCo(btn) {
        const container = btn.closest('form').querySelector('#suco-container');
        const newRow = document.createElement('tr');
        newRow.classList.add('suco-row');
        newRow.innerHTML = `
            <td>
                <select name="loai_suco[]" class="form-control" required>
                    <option value="dien">Mất điện</option>
                    <option value="nuoc">Mất nước</option>
                    <option value="nha">Hỏng hóc nhà cửa</option>
                </select>
            </td>
            <td>
                <div class="input-group">
                    <input type="text" name="mota[]" class="form-control" placeholder="Nhập mô tả sự cố..." required>
                    <div class="input-group-append">
                        <button type="button" class="btn btn-danger btn-remove-suco" onclick="xoa(this)">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </td>
        `;
        container.appendChild(newRow);
    }
    
    function xoa(btn) {
        const row = btn.closest('tr');
        if (row) row.remove();
    }
    </script>


<script>
    function themSuCoVaoBang(phongtroId) {
        const tbody = document.getElementById('suco-table-' + phongtroId);
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>--</td>
            <td>
                <select name="loai_suco_moi[]" class="form-control" required>
                    <option value="dien">Mất điện</option>
                    <option value="nuoc">Mất nước</option>
                    <option value="nha">Hỏng hóc nhà cửa</option>
                </select>
            </td>
            <td>
                <input type="text" name="mota_moi[]" class="form-control" required>
            </td>
            <td></td>
            <td></td>
            <td>
                <button type="button" class="btn btn-danger btn-sm" onclick="this.closest('tr').remove()">
                    <i class="fas fa-trash"></i> Xóa
                </button>
            </td>
        `;
        tbody.appendChild(row);
    }
    
    function toggleEditSuCo(btn) {
        const row = btn.closest('tr');
        row.querySelector('.loai-su-co-text').classList.toggle('d-none');
        row.querySelector('.loai-su-co-edit').classList.toggle('d-none');
        row.querySelector('.mo-ta-text').classList.toggle('d-none');
        row.querySelector('.mo-ta-edit').classList.toggle('d-none');
    }
    </script>
