@extends('layouts.index')

@section('content')
<div class="container mt-5">
    <h3 class="mb-4"><i class="fas fa-paper-plane"></i> Gửi liên hệ đến quản lý</h3>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('lienhe.email') }}" method="POST">
        @csrf

        {{-- Danh sách quản lý --}}
        <div class="mb-3">
            <label for="quanly_id" class="form-label">Chọn quản lý:</label>
            <select class="form-select @error('quanly_id') is-invalid @enderror" id="quanly_id" name="quanly_id">
                <option value="">-- Chọn quản lý --</option>
                @foreach ($quanLys as $ql)
                    <option value="{{ $ql->id }}" {{ old('quanly_id') == $ql->id ? 'selected' : '' }}>
                        {{ $ql->ho_ten }} ({{ $ql->email }})
                    </option>
                @endforeach
            </select>
            @error('quanly_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Nội dung --}}
        <div class="mb-3">
            <label for="noidung" class="form-label">Nội dung liên hệ:</label>
            <textarea class="form-control @error('noidung') is-invalid @enderror" id="noidung" name="noidung" rows="8" placeholder="Nhập nội dung bạn muốn gửi...">{{ old('noidung') }}</textarea>
            @error('noidung')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane"></i> Gửi liên hệ</button>
    </form>
</div>
@endsection
