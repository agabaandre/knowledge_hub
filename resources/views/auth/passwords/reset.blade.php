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
            width: 100%;
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

        .btn-reset {
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

        .btn-reset:hover {
            background: #0d7a3a;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(17, 154, 72, 0.3);
        }

        .btn-reset:active {
            transform: translateY(0);
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

        .back-to-login {
            text-align: center;
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid #e2e8f0;
        }

        .alert {
            position: relative;
            padding: 1rem 2.5rem 1rem 1rem;
            margin-bottom: 1rem;
            border: 1px solid transparent;
            border-radius: 8px;
            display: flex;
            align-items: center;
        }

        .alert-success {
            color: #0f5132;
            background-color: #d1e7dd;
            border-color: #badbcc;
        }

        .alert-info {
            color: #084298;
            background-color: #cfe2ff;
            border-color: #b6d4fe;
        }

        .alert-danger {
            color: #842029;
            background-color: #f8d7da;
            border-color: #f5c2c7;
        }

        .alert-warning {
            color: #664d03;
            background-color: #fff3cd;
            border-color: #ffecb5;
        }

        .btn-close {
            position: absolute;
            top: 0.75rem;
            right: 0.75rem;
            padding: 0;
            background-color: transparent;
            border: 0;
            border-radius: 0.375rem;
            opacity: 0.5;
            cursor: pointer;
            width: 1.5rem;
            height: 1.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: opacity 0.15s ease-in-out;
        }

        .btn-close:hover {
            opacity: 1;
        }

        .btn-close::before {
            content: '×';
            font-size: 1.5rem;
            line-height: 1;
            font-weight: 700;
            color: inherit;
        }

        .alert-success .btn-close::before {
            color: #0f5132;
        }

        .alert-info .btn-close::before {
            color: #084298;
        }

        .alert-danger .btn-close::before {
            color: #842029;
        }

        .alert-warning .btn-close::before {
            color: #664d03;
        }

        .back-to-login p {
            margin: 0;
            color: #718096;
            font-size: 0.875rem;
        }

        .back-to-login a {
            color: var(--theme-color-primary, #119A48);
            font-weight: 600;
            text-decoration: none;
            margin-left: 0.5rem;
        }

        .back-to-login a:hover {
            text-decoration: underline;
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
        }
    </style>

    <div class="login-wrapper">
        <div class="login-card">
            <div class="login-header">
                <h2>Reset Password</h2>
                <p>Enter your new password below</p>
            </div>

            <div class="login-body">
                @if(Session::has('alert') || Session::has('message'))
                    <div class="alert alert-{{ Session::get('alert_class', 'info') }} alert-dismissible fade show" role="alert">
                        {{ Session::get('alert') ?? Session::get('message') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                    <form method="POST" action="{{ route('password.update') }}">
                        @csrf

                        <input type="hidden" name="token" value="{{ $token }}">

                    <div class="form-group">
                        <label class="form-label" for="email">Email Address</label>
                        <input id="email" 
                               type="email" 
                               class="form-control @error('email') is-invalid @enderror" 
                               name="email" 
                               value="{{ $email ?? old('email') }}" 
                               required 
                               autocomplete="email" 
                               autofocus
                               placeholder="Enter your email address">

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                        </div>

                    <div class="form-group">
                        <label class="form-label" for="password">New Password</label>
                        <input id="password" 
                               type="password" 
                               class="form-control @error('password') is-invalid @enderror" 
                               name="password" 
                               required 
                               autocomplete="new-password"
                               placeholder="Enter your new password">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                        </div>

                    <div class="form-group">
                        <label class="form-label" for="password-confirm">Confirm Password</label>
                        <input id="password-confirm" 
                               type="password" 
                               class="form-control" 
                               name="password_confirmation" 
                               required 
                               autocomplete="new-password"
                               placeholder="Confirm your new password">
                        </div>

                    <button type="submit" class="btn btn-reset">
                        <i class="fa fa-key me-2"></i>Reset Password
                                </button>
                    </form>

                <div class="back-to-login">
                    <p>
                        Remember your password?
                        <a href="{{ route('login') }}">Back to Login</a>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Handle alert close button clicks
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.btn-close[data-bs-dismiss="alert"]').forEach(function(button) {
                button.addEventListener('click', function() {
                    var alert = this.closest('.alert');
                    if (alert) {
                        alert.style.transition = 'opacity 0.15s linear';
                        alert.style.opacity = '0';
                        setTimeout(function() {
                            alert.remove();
                        }, 150);
                    }
                });
            });
        });
    </script>
@endsection
