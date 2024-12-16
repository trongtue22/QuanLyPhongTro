@extends('layouts.index')

{{-- Phần Heading --}}
@section('heading')
  Danh sách thống kê
@endsection

@section('breadcrumb')
<a href="{{route('Dashboard.view')}}">
    Dashboard 
</a> >
@endsection



{{-- Phần content --}}
@section('content')

<div class="container mt-1">
    <div class="container mt-1">
        {{-- First Row (Two Cards) --}}
        <div class="row">

            <div class="col-xl-6 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Dãy trọ</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $daytroCount ?? 0 }}</div> 
                            </div>
                            <div class="col-auto">
                                <!-- Changed icon to building -->
                                <i class="fas fa-building fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-6 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Phòng trọ</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{$phongtroCount ?? 0}}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-home fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- Second Row (Two Cards) info--}}
        <div class="row">

            <div class="col-xl-6 col-md-6 mb-4">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Khách thuê</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{$khachthueCount ?? 0}}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-user fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-6 col-md-6 mb-4">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Hợp đồng</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{$hopdongCount ?? 0}}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-file-signature fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>

        <div class="row">
            <div class="col-xl-6 col-md-6 mb-4">
                <div class="card border-left-dark shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-dark text-uppercase mb-1">Hóa đơn</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{$hoadonCount}}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-receipt fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-6 col-md-6 mb-4">
                <div class="card border-left-danger shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Tổng thu nhập</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($hoadonSum, 0, ',', '.')}} VNĐ</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-coins fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="container mt-4">
            <h3 class="text-center">Tổng thu nhập theo năm</h3>
            <form id="yearForm">
                <div class="mb-3">
                    <label for="yearSelect" class="form-label">Chọn năm:</label>
                    <select id="yearSelect" name="year" class="form-select">
                        @foreach ($years as $year)
                        {{-- Lần đầu hiện thị thì luôn hiện thị năm hiện tại --}}
                            <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}> 
                                {{ $year }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </form>
        
            <canvas id="incomeChart"></canvas>
        </div>
        
        <script>
            const ctx = document.getElementById('incomeChart').getContext('2d');
            let incomeChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: @json($months), // Hiển thị tháng
                    datasets: [{
                        label: 'Thu nhập theo tháng (VND)',
                        data: @json($incomeDataByMonth), // Dữ liệu thu nhập
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        
            document.getElementById('yearSelect').addEventListener('change', function () {
                const selectedYear = this.value; // Nếu user chọn năm mới
                
                // Truyền data năm user chọn vào router view lại (với biến truyền là selectedYear ) 
                fetch(`{{ route('Dashboard.view') }}?year=${selectedYear}`, 
                {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
            .then(data => {
                    // Cập nhật biểu đồ với dữ liệu mới
                    incomeChart.data.datasets[0].data = data.incomeDataByMonth;
                    incomeChart.update(); // Update với data mới trả về từ view do user chọn (ko bị reload lại trang) 
                })
                .catch(error => console.error('Error fetching data:', error));
            });
        </script>
        
        
        
        
        
        


      

    </div>
</div>

@endsection




























