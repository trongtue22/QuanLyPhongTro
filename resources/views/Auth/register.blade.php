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
                <img src="https://i.pinimg.com/564x/80/e8/a6/80e8a62cbd2780e7af5b2bbc887946cb.jpg" alt="Logo" width="175" height="57">
              </a>
            </div>

            <h2 class="fs-6 fw-normal text-center text-secondary mb-4">Nhập các thông tin để đăng ký tài khoản</h2>

            <!-- Form đăng ký -->
            <form action="{{ route('auth.register') }}" method="POST" enctype="multipart/form-data">
              @csrf
              <div class="row gy-2">
                <!-- Họ và tên -->
                <div class="col-12">
                  <div class="form-floating mb-3">
                    <input type="text" class="form-control @error('ho_ten') is-invalid @enderror" name="ho_ten" id="fullName" placeholder="Full Name" value="{{ old('ho_ten') }}" required>
                    <label for="fullName">Họ và Tên</label>
                    @error('ho_ten')
                      <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                  </div>
                </div>

                <!-- Email -->
                <div class="col-12">
                  <div class="form-floating mb-3">
                    <input type="email" class="form-control @error('email') is-invalid @enderror" name="email" id="email" placeholder="name@example.com" value="{{ old('email') }}" required>
                    <label for="email">Email</label>
                    @error('email')
                      <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                  </div>
                </div>

                <!-- CCCD -->
                <div class="col-12">
                  <div class="form-floating mb-3">
                    <input type="text" class="form-control @error('cccd') is-invalid @enderror" name="cccd" id="cccd" placeholder="CCCD" value="{{ old('cccd') }}" required>
                    <label for="cccd">CCCD</label>
                    @error('cccd')
                      <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                  </div>
                </div>

                <!-- Số điện thoại -->
                <div class="col-12">
                  <div class="form-floating mb-3">
                    <input type="text" class="form-control @error('sodienthoai') is-invalid @enderror" name="sodienthoai" id="phone" placeholder="Số điện thoại" value="{{ old('sodienthoai') }}" required>
                    <label for="phone">Số điện thoại</label>
                    @error('sodienthoai')
                      <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                  </div>
                </div>


                <!-- Mật khẩu -->
                <div class="col-12">
                  <div class="form-floating mb-3">
                    <input type="password" class="form-control @error('mat_khau') is-invalid @enderror" name="mat_khau" id="password" placeholder="Password" required>
                    <label for="password">Mật khẩu</label>
                    @error('mat_khau')
                      <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                  </div>
                </div>

                <!-- Xác nhận mật khẩu -->
                <div class="col-12">
                  <div class="form-floating mb-3">
                    <input type="password" class="form-control @error('confirmPassword') is-invalid @enderror" name="confirmPassword" id="confirmPassword" placeholder="Confirm Password" required>
                    <label for="confirmPassword">Nhập lại mật khẩu</label>
                    @error('confirmPassword')
                      <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                  </div>
                </div>

                <!-- Hình ảnh -->
                <div class="col-12">
                  <div class="form-floating mb-3">
                    <input type="file" class="form-control @error('hinh_anh') is-invalid @enderror" name="hinh_anh" id="picture" required>
                    <label for="picture">Chọn hình ảnh</label>
                    @error('hinh_anh')
                      <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                  </div>
                </div>

                <!-- Đồng ý điều khoản -->
                <div class="col-12">
                  <div class="form-check">
                    <input class="form-check-input @error('iAgree') is-invalid @enderror" type="checkbox" name="iAgree" id="iAgree" {{ old('iAgree') ? 'checked' : '' }} required>
                    <label class="form-check-label text-secondary" for="iAgree">
                      Tôi đồng ý <a href="#!" class="link-primary text-decoration-none">với các điều kiện</a>
                    </label>
                    @error('iAgree')
                      <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                  </div>
                </div>

                <!-- Nút submit -->
                <div class="col-12">
                  <div class="d-grid my-3">
                    <button class="btn btn-primary btn-lg" type="submit">Đăng ký</button>
                  </div>
                </div>

                <!-- Link đăng nhập -->
                <div class="col-12">
                    <p class="m-0 text-secondary text-center">Đã có tài khoản ? <a href="login" class="link-primary text-decoration-none">Đăng nhập</a></p>
                  </p>
                </div>
              </div>
            </form>

          </div>
        </div>
      </div>
    </div>
  </div>
</section>
