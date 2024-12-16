<!-- Đường dẫn của CSS -->
<link rel="stylesheet" href="https://unpkg.com/bootstrap@5.3.3/dist/css/bootstrap.min.css">

<!-- Form sign-in -->
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
            <h2 class="fs-6 fw-normal text-center text-secondary mb-4">Nhập các thông tin để đăng ký tài khoản</h2>
            
            <!-- Display Validation Errors -->
            @if ($errors->any())
          <div class="alert alert-danger" id="error-alert">
            <ul class="mb-0">
              @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
             </ul>
          </div>
          
          <!-- Tồn tại 3s sau đó biến mất -->
          <script>
            setTimeout(function(){
            document.getElementById('error-alert').style.display = 'none';
             }, 5000); // 3 giây
           </script>
            @endif

            <!-- Form đăng ký -->
             <!-- enctype giúp lưu ảnh dc chọn vào form  -->
            <form action="{{route('auth.register')}}" method="POST" enctype="multipart/form-data"> 
              @csrf
              <div class="row gy-2">
                <div class="col-12">
                  <div class="form-floating mb-3">
                    <input type="text" class="form-control" name="ho_ten" id="fullName" placeholder="Full Name" required>
                    <label for="fullName" class="form-label">Họ và Tên</label>
                  </div>
                </div>
                <div class="col-12">
                  <div class="form-floating mb-3">
                    <input type="email" class="form-control" name="email" id="email" placeholder="name@example.com" required>
                    <label for="email" class="form-label">Email</label>
                  </div>
                </div>
                <div class="col-12">
                  <div class="form-floating mb-3">
                    <input type="password" class="form-control" name="mat_khau" id="password" placeholder="Password" required>
                    <label for="password" class="form-label">Mật khẩu</label>
                  </div>
                </div>
                <div class="col-12">
                  <div class="form-floating mb-3">
                    <input type="password" class="form-control" name="confirmPassword" id="confirmPassword" placeholder="Confirm Password" required>
                    <label for="confirmPassword" class="form-label">Nhập lại mật khẩu</label>
                  </div>
                </div>

                <!-- Hình ảnh -->
                <div class="col-12">
                <div class="form-floating mb-3">
                  <input type="file" class="form-control" name="hinh_anh" id="picture" required>
                <label for="picture" class="form-label">Chọn hình ảnh</label>
                </div>
                </div>

                <!-- Đồng ý với các điều kiện -->
                <div class="col-12">
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="iAgree" id="iAgree" required>
                    <label class="form-check-label text-secondary" for="iAgree">
                      Tôi đồng ý<a href="#!" class="link-primary text-decoration-none"> với các điều kiện</a>
                    </label>
                  </div>
                </div>
                <div class="col-12">
                  <div class="d-grid my-3">
                    <button class="btn btn-primary btn-lg" type="submit">Đăng ký</button>
                  </div>
                </div>
                <div class="col-12">
                  <p class="m-0 text-secondary text-center">Đã có tài khoản ? <a href="login" class="link-primary text-decoration-none">Đăng nhập</a></p>
                </div>
              </div>
            </form>
            
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

