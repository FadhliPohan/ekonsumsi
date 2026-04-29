@extends('layouts.master-without-nav')

@section('title')
    Login - Digital Signage
@endsection

@section('body')

    <body>
    @endsection

    @section('content')
        <div class="account-pages my-5 pt-sm-5">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-8 col-lg-6 col-xl-5">
                        <div class="card overflow-hidden">
                            <div class="bg-primary bg-soft">
                                <div class="row">
                                    <div class="col-7">
                                        <div class="text-primary p-4">
                                            <h5 class="text-primary">Selamat datang!</h5>
                                            <p>Silahkan masuk ke e-konsumsi.</p>
                                        </div>
                                    </div>
                                    <div class="col-5 align-self-end">
                                        <img src="{{ URL::asset('/assets/images/profile-img.png') }}" alt=""
                                            class="img-fluid">
                                    </div>
                                </div>
                            </div>
                            <div class="card-body pt-0">
                                <div class="auth-logo">
                                    <a href="{{ route('dashboard') }}" class="auth-logo-light">
                                        <div class="avatar-md profile-user-wid mb-4">
                                            <span class="avatar-title rounded-circle bg-light">
                                                <img src="{{ URL::asset('/assets/images/logos/logo_no_text.png') }}" alt=""
                                                    class="rounded-circle" height="34">
                                            </span>
                                        </div>
                                    </a>

                                    <a href="{{ route('dashboard') }}" class="auth-logo-dark">
                                        <div class="avatar-md profile-user-wid mb-4">
                                            <span class="avatar-title rounded-circle bg-light">
                                                <img src="{{ URL::asset('/assets/images/logos/logo_no_text.png') }}" alt=""
                                                    class="rounded-circle" height="34">
                                            </span>
                                        </div>
                                    </a>
                                </div>
                                <div class="p-2">
                                    @if (session('status'))
                                        <div class="alert alert-success" role="alert">
                                            {{ session('status') }}
                                        </div>
                                    @endif

                                    <form class="form-horizontal" method="POST" action="{{ route('login') }}">
                                        @csrf

                                        <div class="mb-3">
                                            <label for="email" class="form-label">Email</label>
                                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                                id="email" name="email" value="{{ old('email') }}"
                                                placeholder="Enter email" required autofocus>
                                            @error('email')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Password</label>
                                            <div class="input-group auth-pass-inputgroup">
                                                <input type="password"
                                                    class="form-control @error('password') is-invalid @enderror"
                                                    id="password" name="password" placeholder="Enter password"
                                                    aria-label="Password" aria-describedby="password-addon" required>
                                                <button class="btn btn-light" type="button" id="password-addon">
                                                    <i class="mdi mdi-eye-outline"></i>
                                                </button>
                                            </div>
                                            @error('password')
                                                <span class="invalid-feedback d-block" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>

                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="remember" name="remember"
                                                {{ old('remember') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="remember">
                                                Remember me
                                            </label>
                                        </div>

                                        <div class="mt-3 d-grid">
                                            <button class="btn btn-primary waves-effect waves-light" type="submit">
                                                Log In
                                            </button>
                                        </div>

                                        @if (Route::has('password.request'))
                                            <div class="mt-4 text-center">
                                                <a href="{{ route('password.request') }}" class="text-muted">
                                                    <i class="mdi mdi-lock me-1"></i> Forgot your password?
                                                </a>
                                            </div>
                                        @endif
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div class="mt-5 text-center">
                            <div>
                                <p>©
                                    <script>
                                        document.write(new Date().getFullYear())
                                    </script> Digital Signage.
                                    Crafted with <i class="mdi mdi-heart text-danger"></i> by PT Pusri
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- end account-pages -->
    @endsection

    @section('script')
        <script>
            // Toggle password visibility
            document.getElementById('password-addon').addEventListener('click', function() {
                var passwordInput = document.getElementById('password');
                var icon = this.querySelector('i');

                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    icon.classList.remove('mdi-eye-outline');
                    icon.classList.add('mdi-eye-off-outline');
                } else {
                    passwordInput.type = 'password';
                    icon.classList.remove('mdi-eye-off-outline');
                    icon.classList.add('mdi-eye-outline');
                }
            });
        </script>
    @endsection
