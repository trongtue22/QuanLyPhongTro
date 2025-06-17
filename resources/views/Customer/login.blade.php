<link rel="stylesheet" href="https://unpkg.com/bootstrap@5.3.3/dist/css/bootstrap.min.css">
{{-- Thư viện hiện thị thông báo --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<section class="bg-light py-3 py-md-5">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-12 col-sm-10 col-md-8 col-lg-6 col-xl-5 col-xxl-4">
        <div class="card border border-light-subtle rounded-3 shadow-sm">
          <div class="card-body p-3 p-md-4 p-xl-5">
            <div class="text-center mb-3">
              <a href="#!">
                <img src="https://i.pinimg.com/564x/80/e8/a6/80e8a62cbd2780e7af5b2bbc887946cb.jpg" alt="BootstrapBrain Logo" width="175" height="57">
              </a>
            </div>
           
            <h2 class="fs-6 fw-normal text-center text-secondary mb-4">
              <i class="bi bi-person-fill-gear text-success me-2"></i>
              Điền thông tin đăng nhập <strong>khách thuê</strong>
            </h2>
          
           
             
            {{-- Hiện modal thông báo --}}
            @if (session('success'))
               <script>
                   Swal.fire({
                       icon: "success",
                       title: "Thông báo",
                       text: "{{ session('success') }}",
                   });
               </script>
               {{-- Xóa session sau khi thông báo được hiển thị để nó không hiện thị lại nữa --}}
               {{ session()->forget('success') }}
            @endif

            @if($errors->has('message'))
            <script> 
                Swal.fire({
                    icon: "error",
                    title: "Chờ đã...bạn chưa đăng nhập",
                    text: "{{ $errors->first('message') }}",
                });
            </script>
            @elseif($errors->any()) {{-- Chỉ hiển thị nếu không có lỗi 'message' --}}
            <!-- Hiển thị lỗi trên form login nếu có -->
            <div class="alert alert-danger" id="error-alert">
                <ul class="mb-0 list-unstyled"> 
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            
            <!-- Tồn tại 3s sau đó biến mất -->
            <script>
                setTimeout(function(){
                    document.getElementById('error-alert').style.display = 'none';
                }, 3000); // 3 giây
            </script>
            @endif

            <!-- Form login -->
              <form action="{{route('khachthue.login')}}" method="post">
              @csrf 
              <div class="row gy-2 overflow-hidden"> 
                <div class="col-12">
                    <div class="form-floating mb-3">
                        <input type="tel" class="form-control" name="phone" id="phone" placeholder="09xxxxxxxx" required>
                        <label for="phone" class="form-label">Số điện thoại</label>
                    </div>
                </div>
                
                <div class="col-12">
                    <div class="form-floating mb-3">
                        <input type="number" class="form-control" name="cccd" id="cccd" placeholder="CCCD/CMND" required>
                        <label for="cccd" class="form-label">Căn cước công dân</label>
                    </div>
                </div>                

                <div class="col-12">
                  <div class="d-grid my-3">
                    <button class="btn btn-primary btn-lg" type="submit">Đăng nhập</button>
                  </div>
                </div>

             
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
