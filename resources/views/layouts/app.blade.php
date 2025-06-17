<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title', 'Quản Lý Phòng Trọ')</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    {{-- Navbar --}}
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#">Quản Lý Phòng Trọ</a>
           
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu" aria-controls="navMenu" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            
            <ul class="navbar-nav ms-auto">
                <a href="#" class="btn btn-info mb-3" data-bs-toggle="modal" data-bs-target="#contactManagerModal">
                    <i class="fas fa-envelope"></i> Liên hệ quản lý
                </a>
                
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" id="userDropdown" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false" aria-labelledby="userDropdown">
                            <div class="d-flex flex-column align-items-end me-2">
                                <span class="text-muted small">Chức vụ: khách thuê</span>
                                <span class="d-none d-lg-inline text-dark fw-bold small">{{ session('khachthue_name') }}</span>
                            </div>
                        </a>

                        <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="userDropdown">
                            <li>
                                <a class="dropdown-item" href="{{route('khachthue.profile')}}">
                                    <i class="fas fa-user fa-sm fa-fw me-2 text-gray-400" aria-hidden="true"></i>
                                    Hồ sơ
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item text-danger" href="#" data-bs-toggle="modal" data-bs-target="#logoutModal">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw me-2 text-gray-400" aria-hidden="true"></i>
                                    Đăng xuất
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>

               
                   
              
            
        </div>
    </nav>

    {{-- Content chính --}}
    <main class="container my-4">
        @yield('content')
    </main>

    <!-- Modal logout -->
<div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="logoutModalLabel">Đăng xuất</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <div class="modal-body" id="logoutModalBody">
                Bạn có chắc chắn muốn đăng xuất không?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <form action="{{ route('khachthue.logout') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-danger">Đăng xuất</button>
                </form>
            </div>
        </div>
    </div>
</div>


<!-- Modal liên hệ quản lý -->
<div class="modal fade" id="contactManagerModal" tabindex="-1" aria-labelledby="contactManagerModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('khachthue.send') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title" id="contactManagerModalLabel">Liên hệ quản lý</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <div class="modal-body">

                <select class="form-select" name="recipient" id="recipient" required>
                 @if(!empty($quanLys) && $quanLys->count())
                    <option value="" disabled selected>-- Chọn quản lý --</option>
                    @foreach($quanLys as $quanLy)
                        <option value="manager_{{ $quanLy->id }}">{{ $quanLy->ho_ten }} ({{ $quanLy->email }})</option>
                    @endforeach
                        <option value="chutro_{{ $chuTroEmail }}">Chủ trọ: {{ $chuTroEmail }}</option>
                    @else
                        <option value="chutro_{{ $chuTroEmail }}" selected>Chủ trọ: {{ $chuTroEmail }}</option>
                    @endif
                </select>

                <div class="mb-3">
                    <label for="subject" class="form-label">Tiêu đề</label>
                    <input type="text" class="form-control" id="subject" name="subject" required>
                </div>
                <div class="mb-3">
                    <label for="message" class="form-label">Nội dung</label>
                    <textarea class="form-control" id="message" name="message" rows="4" required></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="submit" class="btn btn-primary">Gửi</button>
            </div>
        </form>
    </div>
</div>



    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>