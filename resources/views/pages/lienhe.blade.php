@extends('layouts.index') {{-- Hoặc layout của bạn --}}

@section('content')
<div class="container mt-5">
    <h3 class="mb-4"><i class="fas fa-paper-plane"></i> Gửi liên hệ đến chủ trọ: {{ $quanLy->chutro->ho_ten }}</h3>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('lienhe.send') }}" method="POST">
        @csrf
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
