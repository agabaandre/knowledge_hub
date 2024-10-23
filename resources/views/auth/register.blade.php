@extends('layouts.plain')

@section('')
    <style>
        .select2-container {
            border: 1px #000 solid !important;
            min-width: 250px;
            text-align: left !important;
            min-width: 50px !important;
        }
    </style>
@endsection

@section('content')
    <section class="middle gray">
        <div class="container">
            <div class="row align-items-center justify-content-center">


                <div class="col-xl-8 col-lg-8 col-md-12 col-sm-12 mfliud">
                    <form class="border p-3 rounded bg-white" method="POST" action="{{ route('registration') }}">
                        <h3 class="py-3 text-success">Register for an account</h3>

                        @csrf

                        <div class="row">
                            <div class="form-group col-md-6">
                                <label>First Name *</label>
                                <input type="text" class="form-control @error('firstname') is-invalid @enderror"
                                    name="firstname" placeholder="First Name" value="{{ old('firstname') }}" required
                                    autocomplete="firstname" autofocus>
                                @error('firstname')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group col-md-6">
                                <label>Last Name</label>
                                <input type="text" class="form-control @error('lastname') is-invalid @enderror"
                                    name="lastname" placeholder="Last Name" value="{{ old('lastname') }}" required
                                    autocomplete="lastname" autofocus>
                                @error('lastname')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="form-group col-md-6">
                                <label>Email *</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                    name="email" placeholder="Email Address*" value="{{ old('email') }}" required
                                    autocomplete="off" autofocus>
                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="form-group col-md-6">
                                <label>Phone *</label>
                                <input type="tel" class="form-control @error('phone') is-invalid @enderror"
                                    name="phone" placeholder="Phone Number*" value="{{ old('phone') }}" required
                                    autocomplete="off" autofocus>
                                @error('phone')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="form-group col-md-6">
                                <label>Job Title *</label>

                                @include('partials.jobs.dropdown', ['field' => 'job', 'selected' => []])

                                @error('job')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="form-group col-md-6">
                                <label>Country *</label>

                                @include('partials.countries.dropdown', [
                                    'field' => 'country_id',
                                    'selected' => old('country_id'),
                                ])

                                @error('country_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="form-group col-md-6">
                                <label>Preferences *</label>

                                @include('partials.tags.dropdown', [
                                    'field' => 'preferences[]',
                                    'selected' => [],
                                ])

                                @error('preferences')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="form-group col-md-6">
                                <label class="form-label" for="communities">Preferred Communities to Join</label>
                                @include('partials.publications.publication_communities_dropdown', [
                                    'field' => 'communities[]',
                                    'selected' => @$row->communities ? $row->communities : [],
                                ])
                            </div>

                        </div>

                        <div class="row">
                            <div class="form-group col-md-6">
                                <label>Password *</label>
                                <input type="password" name="password"
                                    class="form-control @error('password') is-invalid @enderror" placeholder="Password*"
                                    value="{{ old('password') }}" required autocomplete="off" autofocus>
                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="form-group col-md-6">
                                <label>Confirm Password *</label>
                                <input type="password" name="password_confirmation" class="form-control"
                                    placeholder="Confirm Password*">
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-md-12">
                                {!! NoCaptcha::display() !!}
                            </div>
                        </div>

                        <div class="form-group">

                            @error('g-recaptcha-response')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                            <p>By registering your details, you agree with our Terms & Conditions, and Privacy and Cookie
                                Policy.</p>
                        </div>

                        <div class="form-group">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="flex-1">
                                    <input id="subscribe" class="checkbox-custom" name="subscribe" type="checkbox">
                                    <label for="subscribe" class="checkbox-custom-label">Sign me up for the
                                        Newsletter!</label>
                                </div>
                            </div>
                        </div>


                        <div class="row justify-content-center">
                            <div class="form-group col-lg-6">
                                <button type="submit"
                                    class="btn btn-md full-width theme-bg text-light fs-md ft-medium">Create An
                                    Account</button>
                            </div>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </section>
@endsection

@section('scripts')
    @include('common.select2')
    {!! NoCaptcha::renderJs() !!}
@endsection
