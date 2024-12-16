<!DOCTYPE html>
<html lang="en">
<head>
    @include('partials.head')
    
</head>
<body id="page-top"> 
    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar bg-gradient-primary -->
        <ul class="navbar-nav sidebar sidebar-dark accordion" style="background-color: #212631" id="accordionSidebar">
            @include('partials.sidebar')
        </ul>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                    @include('partials.header')
                </nav>
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">

                    <!-- Page Heading -->
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        {{-- Thông báo hiện thị --}}
                        <h1 class="h3 mb-0 text-gray-800"> @yield('heading') </h1>
                        
                        {{-- Chỉ mục pages  --}}
                        {{-- <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm" style="margin-right: 70px;"> --}}
                            {{-- <i class="fas fa-download fa-sm text-white-50"></i> Generate Report --}}
                            {{-- Chỉ mục pages --}}
                             <nav aria-label="breadcrumb"  style="margin-right: 90px;">
                                 <ol class="breadcrumb mb-0">
                                     <li class="breadcrumb-item">
                                        @yield('breadcrumb')
                                     </li>
                                 </ol>
                             </nav>
                            {{-- @yield('breadcrumb')  --}}
                        {{-- </a> --}}
                    </div>

                    <!-- Content Row -->
                    <div class="row">
                        @yield('content')
                    </div>

                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            <footer class="sticky-footer bg-white">
                @include('partials.footer')     
            </footer>
            <!-- End of Footer -->

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Logout Modal(các Modal tĩnh) -->
    @include('modals.logout')
    
    {{-- Thêm mới Modal (các Modal động) --}}
    @yield('addModal')

    <!-- Bootstrap core JavaScript-->
    {{-- <script src="{{ asset('js/sb-admin-2.min.js') }}"></script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script> --}}

    {{-- <script src="{{ asset('js/sb-admin-2.min.js') }}"></script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"> --}}
    {{-- <script src="{{ asset('js/sb-admin-2.min.js') }}"></script> --}}

    {{-- <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script> --}}
    <!-- Tải jQuery trước -->
        <!-- ... existing code ... -->

    {{-- <script src="{{ asset('js/sb-admin-2.min.js') }}"></script>  --}}


<script src="{{ asset('js/sb-admin-2.min.js') }}"></script>

{{-- Phần giúp mở thành phần Logout ra --}}
<script>
    $(document).ready(function() {
        // Initialize all dropdowns
        $('.dropdown-toggle').dropdown();

        // Handle clicks on dropdown toggles
        $(document).on('click', '.nav-link.dropdown-toggle', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            var $dropdownMenu = $(this).next('.dropdown-menu');
            $('.dropdown-menu').not($dropdownMenu).removeClass('show');
            $dropdownMenu.toggleClass('show');
        });

        // Close dropdown when clicking outside
        $(document).on('click', function(e) {
            if (!$(e.target).closest('.dropdown').length) {
                $('.dropdown-menu').removeClass('show');
            }
        });
    });
</script>

</body>
</html>




</body>
</html>
