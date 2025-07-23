@extends('layouts.plain')

@section('content')
    <style>
        .auth-btn {
            min-width: 180px;
            margin: 0.25rem 0.5rem 0.25rem 0.5rem;
            font-size: 0.98rem;
            padding: 0.5rem 1.25rem;
            border-radius: 0.5rem;
        }
        .auth-btn-group {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 0.75rem;
            margin-bottom: 1.25rem;
        }
        .auth-divider {
            display: flex;
            align-items: center;
            text-align: center;
            margin: 1.5rem 0 1.5rem 0;
        }
        .auth-divider::before, .auth-divider::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid #dee2e6;
        }
        .auth-divider:not(:empty)::before {
            margin-right: .75em;
        }
        .auth-divider:not(:empty)::after {
            margin-left: .75em;
        }
        .auth-link {
            font-size: 0.97rem;
        }
        .auth-form-title {
            font-weight: 600;
            color: #119A48;
            letter-spacing: 0.5px;
        }
        @media (max-width: 575.98px) {
            .auth-btn {
                min-width: 120px;
                font-size: 0.93rem;
                padding: 0.45rem 0.75rem;
            }
        }
    </style>
    <!-- ======================= Login Detail ======================== -->
    <section class="middle gray">
        <div class="container">
            <div class="row align-items-center justify-content-center py-4">

                <div class="col-xl-5 col-lg-6 col-md-10 col-sm-12">

                    <form class="border p-4 rounded bg-white shadow-sm" action="{{ route('login') }}" method="POST">
                        @csrf

                        <h3 class="py-2 text-center auth-form-title">Sign in to your account</h3>

                        <div class="form-group mb-3">
                            <label class="mb-1">User Name *</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                placeholder="Username*" name="email" value="{{ old('email') }}" required
                                autocomplete="email" autofocus>
                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label class="mb-1">Password *</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror"
                                placeholder="Password*" name="password" required autocomplete="current-password">
                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <div class="py-2">
                                {!! htmlFormSnippet() !!}
                                @error('g-recaptcha-response')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group mb-2">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <input id="dd" class="checkbox-custom" type="checkbox" name="remember"
                                        {{ old('remember') ? 'checked' : '' }}>
                                    <label for="dd" class="checkbox-custom-label auth-link">Remember Me</label>
                                </div>
                                <div>
                                    <a href="{{ route('password.request') }}" class="auth-link">Forgot Password?</a>
                                </div>
                            </div>
                        </div>

                        <div class="form-group pt-2 mb-0">
                            <button type="submit"
                                class="btn btn-success auth-btn full-width w-100">Login</button>
                        </div>

                        <div class="auth-divider">or</div>

                        <div class="auth-btn-group mb-2">
                            {{-- <a href="{{ url('auth/microsoft') }}" class="btn btn-outline-primary auth-btn d-flex align-items-center justify-content-center">
                                <i class="lni lni-microsoft me-2"></i> Login with Microsoft
                            </a> --}}
                            <a href="{{ url('auth/google') }}" class="btn btn-outline-danger auth-btn d-flex align-items-center justify-content-center">
                                <i class="lni lni-google me-2"></i> Login with Google
                            </a>
                        </div>

                        <div class="auth-btn-group mb-3">
                            {{-- <a href="{{ url('auth/microsoft') }}" class="btn btn-outline-primary auth-btn d-flex align-items-center justify-content-center">
                                <i class="lni lni-microsoft me-2"></i> Register with Microsoft
                            </a> --}}
                            <a href="{{ url('auth/google') }}" class="btn btn-outline-danger auth-btn d-flex align-items-center justify-content-center">
                                <i class="lni lni-google me-2"></i> Register with Google
                            </a>
                        </div>

                        <div class="auth-divider"></div>

                        <div class="d-flex justify-content-center">
                            <a href="{{ route('register') }}" class="btn btn-outline-dark auth-btn w-100 text-center">
                                <i class="fa fa-user-plus me-2"></i>Register Now
                            </a>
                        </div>

                    </form>

                </div>
            </div>
        </div>
    </section>
@endsection
