@extends('layouts.plain')

@section('content')
    <style>
        .login-wrapper {
            min-height: calc(100vh - 200px);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 0;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        }

        .login-card {
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            max-width: 450px;
            width: 100%;
        }

        .login-header {
            background: linear-gradient(135deg, var(--theme-color-primary, #119A48) 0%, #0d7a3a 100%);
            padding: 2.5rem 2rem;
            text-align: center;
            color: white;
        }

        .login-header h2 {
            font-size: 1.75rem;
            font-weight: 700;
            margin: 0;
            color: white;
            letter-spacing: -0.5px;
        }

        .login-header p {
            margin: 0.5rem 0 0 0;
            opacity: 0.9;
            font-size: 0.95rem;
        }

        .login-body {
            padding: 2.5rem 2rem;
        }

        .form-label {
            font-weight: 600;
            color: #2d3748;
            font-size: 0.875rem;
            margin-bottom: 0.5rem;
            display: block;
        }

        .form-control {
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            padding: 0.75rem 1rem;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            background: #f8f9fa;
        }

        .form-control:focus {
            border-color: var(--theme-color-primary, #119A48);
            background: #ffffff;
            box-shadow: 0 0 0 3px rgba(17, 154, 72, 0.1);
            outline: none;
        }

        .form-control::placeholder {
            color: #a0aec0;
        }

        .checkbox-wrapper {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .checkbox-wrapper input[type="checkbox"] {
            width: 18px;
            height: 18px;
            cursor: pointer;
            accent-color: var(--theme-color-primary, #119A48);
        }

        .checkbox-wrapper label {
            margin: 0;
            font-size: 0.875rem;
            color: #4a5568;
            cursor: pointer;
        }

        .forgot-password-link {
            color: var(--theme-color-primary, #119A48);
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 500;
            transition: color 0.2s ease;
        }

        .forgot-password-link:hover {
            color: #0d7a3a;
            text-decoration: underline;
        }

        .btn-login {
            background: var(--theme-color-primary, #119A48);
            border: none;
            border-radius: 8px;
            padding: 0.875rem 1.5rem;
            font-size: 1rem;
            font-weight: 600;
            color: white;
            width: 100%;
            transition: all 0.3s ease;
            margin-top: 1rem;
        }

        .btn-login:hover {
            background: #0d7a3a;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(17, 154, 72, 0.3);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .divider {
            display: flex;
            align-items: center;
            text-align: center;
            margin: 1.5rem 0;
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid #e2e8f0;
        }

        .divider span {
            padding: 0 1rem;
            color: #718096;
            font-size: 0.875rem;
        }

        .social-btn-group {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
            margin-bottom: 1.5rem;
        }

        .btn-social {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            padding: 0.75rem 1rem;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            background: white;
            color: #2d3748;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }

        .btn-social:hover {
            border-color: var(--theme-color-primary, #119A48);
            background: #f8f9fa;
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            text-decoration: none;
            color: #2d3748;
        }

        .btn-social i {
            font-size: 1.25rem;
        }

        .btn-microsoft {
            color: #00a1f1;
        }

        .btn-microsoft:hover {
            border-color: #00a1f1;
            color: #00a1f1;
        }

        .btn-google {
            color: #db4437;
        }

        .btn-google:hover {
            border-color: #db4437;
            color: #db4437;
        }

        .register-link {
            text-align: center;
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid #e2e8f0;
        }

        .register-link p {
            margin: 0;
            color: #718096;
            font-size: 0.875rem;
        }

        .register-link a {
            color: var(--theme-color-primary, #119A48);
            font-weight: 600;
            text-decoration: none;
            margin-left: 0.5rem;
        }

        .register-link a:hover {
            text-decoration: underline;
        }

        .invalid-feedback {
            display: block;
            width: 100%;
            margin-top: 0.375rem;
            font-size: 0.875rem;
            color: #e53e3e;
        }

        .form-group {
            margin-bottom: 1.25rem;
        }

        @media (max-width: 575.98px) {
            .login-wrapper {
                padding: 1rem;
            }

            .login-card {
                margin: 1rem;
                border-radius: 12px;
            }

            .login-header {
                padding: 2rem 1.5rem;
            }

            .login-header h2 {
                font-size: 1.5rem;
            }

            .login-body {
                padding: 2rem 1.5rem;
            }

            .social-btn-group {
                gap: 0.5rem;
            }
        }
    </style>

    <div class="login-wrapper">
        <div class="login-card">
            <div class="login-header">
                <h2>Welcome Back</h2>
                <p>Sign in to continue to your account</p>
            </div>

            <div class="login-body">
                @if(Session::has('alert') || Session::has('message'))
                    <div class="alert alert-{{ Session::get('alert_class', 'info') }} alert-dismissible fade show" role="alert">
                        {{ Session::get('alert') ?? Session::get('message') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <form action="{{ route('login') }}" method="POST" id="loginForm">
                    @csrf

                    <div class="form-group">
                        <label class="form-label" for="email">Email Address</label>
                        <input type="email" 
                               id="email" 
                               name="email" 
                               class="form-control @error('email') is-invalid @enderror"
                               placeholder="Enter your email address" 
                               value="{{ old('email') }}" 
                               required 
                               autocomplete="email" 
                               autofocus>
                        @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="password">Password</label>
                        <input type="password" 
                               id="password" 
                               name="password" 
                               class="form-control @error('password') is-invalid @enderror"
                               placeholder="Enter your password" 
                               required 
                               autocomplete="current-password">
                        @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <div class="py-2">
                            {!! \Biscolab\ReCaptcha\Facades\ReCaptcha::htmlFormSnippet() !!}
                            @error('g-recaptcha-response')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="checkbox-wrapper">
                                <input type="checkbox" 
                                       id="remember" 
                                       name="remember" 
                                       {{ old('remember') ? 'checked' : '' }}>
                                <label for="remember">Remember me</label>
                            </div>
                            <a href="{{ route('password.request') }}" class="forgot-password-link">
                                Forgot Password?
                            </a>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-login">
                        <i class="fa fa-sign-in-alt me-2"></i>Sign In
                    </button>
                </form>

                <div class="divider">
                    <span>or continue with</span>
                </div>

                <div class="social-btn-group">
                    <a href="{{ url('auth/microsoft') }}" class="btn-social btn-microsoft">
                        <i class="lni lni-microsoft"></i>
                        <span>Microsoft</span>
                    </a>
                    <a href="{{ url('auth/google') }}" class="btn-social btn-google">
                        <i class="lni lni-google"></i>
                        <span>Google</span>
                    </a>
                </div>

                <div class="register-link">
                    <p>
                        Don't have an account?
                        <a href="{{ route('register') }}">Register Now</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection