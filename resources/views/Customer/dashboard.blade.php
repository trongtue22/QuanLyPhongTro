@extends('layouts.app')

@section('title', 'Danh sách phòng trọ đang thuê')

@section('content')
    <h2 class="mb-4 fw-semibold text-center display-4">Danh sách phòng trọ đang thuê</h2>
   
    @forelse($groupedByDaytro as $daytroName => $phongs)
        <div class="card mb-3 shadow-sm">
            <div class="card-header bg-primary text-white fw-bold d-flex align-items-center">
                <span>Dãy trọ {{ $daytroName }}</span>
            
                @if ($phongs->first()?->daytro?->quanly)
                    <span class="ms-2">(Quản lý: {{ $phongs->first()->daytro->quanly->ho_ten }})</span>
                @endif
            </div>

            <div class="card-body p-0">
                <table class="table mb-0 table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Phòng</th>
                            <th>Khách thuê đại diện</th>
                            <th>Số người thuê</th>
                            <th>Hóa đơn</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($phongs as $index => $phong)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $phong->sophong }}</td>
                                <td>{{ $khachThue->tenkhachthue }}</td>
                                <td class="pl-5">{{ $phong->khachthues->count() }}/4</td>
                                <td>
                                    <a href="{{ route('hoadon.view', $phong->id) }}" class="btn btn-sm btn-primary">Xem</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @empty
        <div class="alert alert-info">
            Bạn hiện không thuê phòng nào.
        </div>
    @endforelse

    <div class="mt-3 d-flex justify-content-center">
        {{ $groupedByDaytro->links('pagination::bootstrap-4') }}
    </div>
@endsection