@extends('layouts.index')

{{-- Phần Heading --}}
@section('heading')
   Thông tin dịch vụ
@endsection

@section('breadcrumb')
<a href="{{ route('DichVu.view') }}">
    Dịch vụ
</a> >
@endsection

{{-- Phần content --}}
@section('content')

<div class="container mt-1 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card-body">

                {{-- Thông báo flash --}}
                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                @if($daytros->count() > 0)

                {{-- Select Dãy trọ --}}
                <div class="form-group mb-4">
                    <label for="daytro_id"><strong>Chọn Dãy trọ:</strong></label>
                    <form method="GET" action="{{ route('DichVu.view') }}">
                        <select name="daytro_id" id="daytro_id" class="form-control" onchange="this.form.submit()">
                            <option disabled selected>-- Chọn dãy trọ --</option>
                            @foreach($daytros as $daytro)
                                <option value="{{ $daytro->id }}" {{ request('daytro_id') == $daytro->id ? 'selected' : '' }}>
                                    {{ $daytro->tendaytro }} ({{ $daytro->xa }}, {{ $daytro->huyen }})
                                </option>
                            @endforeach
                        </select>
                    </form>
                </div>

                {{-- Nếu đã chọn dãy trọ thì mới hiển thị form dịch vụ --}}
                @if($dichvu && request('daytro_id'))
                <form action="{{ route('DichVu.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="dichvu_id" value="{{ $dichvu->id }}">
                    <input type="hidden" name="daytro_id" value="{{ request('daytro_id') }}">

                    {{-- Tiền điện --}}
                    <div class="form-group">
                        <label for="dien">Giá điện</label>
                        <input type="text" name="dien" id="dien" class="form-control" 
                            value="{{ number_format($dichvu->dien, 0, ',', '.') }}" required>
                        @error('dien')
                            <div style="color: red;">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Tiền nước --}}
                    <div class="form-group">
                        <label for="nuoc">Giá nước</label>
                        <input type="text" name="nuoc" id="nuoc" class="form-control" 
                            value="{{ number_format($dichvu->nuoc, 0, ',', '.') }}" required>
                        @error('nuoc')
                            <div style="color: red;">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Tiền Wifi --}}
                    <div class="form-group">
                        <label for="wifi">Wifi</label>
                        <input type="text" name="wifi" id="wifi" class="form-control" 
                            value="{{ number_format($dichvu->wifi, 0, ',', '.') }}" required>
                        @error('wifi')
                            <div style="color: red;">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Tiền gửi xe --}}
                    <div class="form-group">
                        <label for="guixe">Giá gửi xe</label>
                        <input type="text" name="guixe" id="guixe" class="form-control" 
                            value="{{ number_format($dichvu->guixe, 0, ',', '.') }}" required>
                        @error('guixe')
                            <div style="color: red;">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Tiền rác --}}
                    <div class="form-group">
                        <label for="rac">Rác</label>
                        <input type="text" name="rac" id="rac" class="form-control" 
                            value="{{ number_format($dichvu->rac, 0, ',', '.') }}" required>
                        @error('rac')
                            <div style="color: red;">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Nút Submit (chỉ hiện nếu không phải là quản lý) --}}
                    @if(!session()->has('user_type'))
                    <div class="form-group mt-4">
                        <button type="submit" class="btn btn-primary">Cập nhật</button>
                    </div>
                    @endif
                </form>
                @endif

                @else
                <div class="alert alert-warning">
                    <strong>Không có dãy trọ để thêm dịch vụ!</strong> Vui lòng <a href="{{ route('daytro') }}">thêm dãy trọ</a> trước.
                </div>
                @endif

            </div>
        </div>
    </div>
</div>

@endsection