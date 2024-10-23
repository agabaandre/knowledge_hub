@extends('layouts.plain')

@section('content')
    <!-- ======================= Login Detail ======================== -->
    <section class="middle gray">
        <div class="container">
            <div class="row align-items-center justify-content-center py-1">

                <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12">

                    <form class="border p-3 rounded  bg-white" action="{{ route('login') }}" method="POST">
                        @csrf

                        <h3 class="py-3">Login to access more</h3>
                        <div class="form-group">
                            <label>User Name *</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                placeholder="Username*" name="email" value="{{ old('email') }}" required
                                autocomplete="email" autofocus>
                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label>Password *</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror"
                                placeholder="Password*" name="password" required autocomplete="current-password">
                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <div class="py-3">
                                {!! NoCaptcha::display() !!}
                                @error('g-recaptcha-response')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="flex-1">
                                    <input id="dd" class="checkbox-custom" type="checkbox" name="remember"
                                        id="remember" {{ old('remember') ? 'checked' : '' }}>
                                    <label for="dd" class="checkbox-custom-label">Remember Me</label>
                                </div>
                                <div class="eltio_k2">
                                    <a href="{{ route('password.request') }}">Forgot Password</a>
                                </div>
                            </div>
                        </div>

                        <div class="form-group pt-3">
                            <button type="submit"
                                class="btn btn-md full-width theme-bg text-light fs-md ft-medium">Login</button>
                        </div>

                        <div class="form-group pt-3">
                            <h4 class="text-center"><a href="{{ route('register') }}" class="">Register</a></h4>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </section>
@endsection

@section('scripts')
    {!! NoCaptcha::renderJs() !!}
@endsection
