
            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.html">
                <div class="sidebar-brand-icon">
                    <i class="fas fa-house-user"></i>
                </div>
                <div class="sidebar-brand-text mx-3">Quản lý phòng trọ<sup></sup></div>
            </a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <!-- Nav Item - Dashboard -->
            <li class="nav-item">
                <a class="nav-link" href="{{route('Dashboard.view')}}">
                    <i class="fas fa-tachometer-alt"></i> <!-- Cập nhật biểu tượng đây -->
                    <span>Dashboard</span> <!-- Thay đổi tiêu đề nếu cần -->
                </a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Heading -->
            <div class="sidebar-heading">
                Danh sách
            </div>

            <!-- Nav Item - Pages Collapse Menu -->
            <li class="nav-item">
                
                {{-- <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseTwo"
                    aria-expanded="true" aria-controls="collapseTwo">
                    <i class="fas fa-fw fa-cog"></i>
                    <span>Components</span>
                </a> --}}
                
                {{-- <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Custom Components:</h6>
                        <a class="collapse-item" href="buttons.html">Buttons</a>
                        <a class="collapse-item" href="cards.html">Cards</a>
                    </div>
                </div> --}}

                <li class="nav-item">
                    <a class="nav-link" href="{{route('daytro')}}">
                        <i class="fas fa-building"></i> <!-- Biểu tượng nhà -->
                        <span>Dãy Trọ</span>
                    </a>
                </li>

            </li>

            <!-- Nav Item - Utilities Collapse Menu -->
            <li class="nav-item">
                
                {{-- <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseUtilities"
                    aria-expanded="true" aria-controls="collapseUtilities">
                    <i class="fas fa-fw fa-wrench"></i>
                    <span>Utilities</span>
                </a> --}}

                {{-- <div id="collapseUtilities" class="collapse" aria-labelledby="headingUtilities"
                    data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Custom Utilities:</h6>
                        <a class="collapse-item" href="utilities-color.html">Colors</a>
                        <a class="collapse-item" href="utilities-border.html">Borders</a>
                        <a class="collapse-item" href="utilities-animation.html">Animations</a>
                        <a class="collapse-item" href="utilities-other.html">Other</a>
                    </div>
                </div>
                 --}}

                <li class="nav-item">
                    <a class="nav-link" href="{{route('PhongTro.view')}}">
                        <i class="fas fa-home"></i> <!-- Biểu tượng nhà -->
                        <span>Phòng Trọ</span>
                    </a>
                </li>

            </li>

            {{-- Khách thuê --}}
            <li class="nav-item">
                <li class="nav-item">
                    <a class="nav-link" href="{{route('KhachThue.view')}}">
                        <i class="fas fa-user"></i> <!-- Biểu tượng nhà -->
                        <span>Khách Thuê</span>
                    </a>
                </li>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Heading -->
            <div class="sidebar-heading">
                Tính năng
            </div>

            <!-- Nav Item - Pages Collapse Menu -->
            {{-- <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsePages"
                    aria-expanded="true" aria-controls="collapsePages">
                    <i class="fas fa-fw fa-folder"></i>
                    <span>Pages</span>
                </a>
                <div id="collapsePages" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Login Screens:</h6>
                        <a class="collapse-item" href="login.html">Login</a>
                        <a class="collapse-item" href="register.html">Register</a>
                        <a class="collapse-item" href="forgot-password.html">Forgot Password</a>
                        <div class="collapse-divider"></div>
                        <h6 class="collapse-header">Other Pages:</h6>
                        <a class="collapse-item" href="404.html">404 Page</a>
                        <a class="collapse-item" href="blank.html">Blank Page</a>
                    </div>
                </div>
            </li> --}}

            <!-- Nav Item - Charts -->
            <li class="nav-item">
                <a class="nav-link" href="{{route('HopDong.view')}}">
                    <i class="fas fa-file-signature"></i>
                    <span>Hợp Đồng</span></a>
            </li>


             <!-- Nav Item - Tables -->
             <li class="nav-item">
                <a class="nav-link" href="{{route('DichVu.view')}}">
                    <i class="fas fa-tools"></i>

                    <span>Dịch Vụ</span></a>
            </li>

            
            <!-- Nav Item - Charts -->
            <li class="nav-item">
                <a class="nav-link" href="{{route('HoaDon.view')}}">
                    <i class="fas fa-receipt"></i>
                    <span>Hóa Đơn</span></a>
            </li>

            @if(!session()->has('user_type'))
            <li class="nav-item">
                <a class="nav-link" href="{{route('QuanLy.view')}}">
                  <i class="fas fa-user-cog"></i> <!-- Đổi icon ở đây -->
                  <span>Quản Lý</span>
                </a>
              </li>
            @endif
           





            <!-- Divider -->
            <hr class="sidebar-divider d-none d-md-block">

            <!-- Sidebar Toggler (Sidebar) -->
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>

            <!-- Sidebar Message -->
            {{-- <div class="sidebar-card d-none d-lg-flex">
                <img class="sidebar-card-illustration mb-2" src="img/undraw_rocket.svg" alt="...">
                <p class="text-center mb-2"><strong>SB Admin Pro</strong> is packed with premium features, components, and more!</p>
                <a class="btn btn-success btn-sm" href="https://startbootstrap.com/theme/sb-admin-pro">Upgrade to Pro!</a>
            </div> --}}

