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

                                @include('partials.jobs.dropdown', [
                                    'field' => 'job',
                                    'selected' => old('job'),
                                    'valueField' => 'name',
                                ])

                                @error('job')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="checkbox" id="job_missing_register"
                                        name="job_missing" value="1" {{ old('job_missing') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="job_missing_register">
                                        My job title is missing from the list
                                    </label>
                                </div>
                                <div id="job_title_custom_wrap_register" class="mt-2" style="{{ old('job_missing') ? '' : 'display:none;' }}">
                                    <input type="text" class="form-control camel-case-input" name="job_title_custom"
                                        id="job_title_custom_register" value="{{ old('job_title_custom') }}"
                                        placeholder="Type your job title (optional)">
                                    <small class="text-muted">Optional if your title is not listed.</small>
                                    @error('job_title_custom')
                                        <span class="invalid-feedback d-block" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
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
                                <label>Your Interests *</label>

                                @include('partials.publications.subtheme_dropdown', [
                                    'field' => 'preferences[]',
                                    'multiple' => 'multiple',
                                    'selected' => old('preferences', []),
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
                                    'selected' => old('communities', []),
                                ])
                            </div>

                            <div class="form-group col-md-6">
                                <label>Organization / Institution</label>
                                <input type="text" class="form-control camel-case-input"
                                    name="organization_name" placeholder="Organization / Institution Name"
                                    value="{{ old('organization_name') }}">
                            </div>

                            <div class="form-group col-md-6">
                                <label>ORCID</label>
                                <input type="text" class="form-control" name="orcid"
                                    placeholder="ORCID ID (e.g., 0000-0000-0000-0000)"
                                    value="{{ old('orcid') }}" maxlength="19">
                                <small class="form-text text-muted">Optional</small>
                            </div>

                            <div class="form-group col-md-6">
                                <label>Language</label>
                                <select class="form-control select2" name="langauge">
                                    @foreach(\App\Models\SiteLanguage::selectorMap() as $code => $row)
                                        <option value="{{ $code }}" {{ old('langauge', 'en') === $code ? 'selected' : '' }}>
                                            {{ $row['name'] }}
                                        </option>
                                    @endforeach
                                </select>
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
                                {!! htmlFormSnippet() !!}
                                @error('g-recaptcha-response')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
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

                        <div class="row justify-content-center">
                            <div class="btn-group" role="group" aria-label="Login with social media">
                                @if(settings()->enable_microsoft_login ?? true)
                                <a href="{{ url('auth/microsoft') }}" class="btn btn-outline-primary"><i
                                        class="lni lni-microsoft"></i>
                                    Join with Microsoft</a>
                                @endif
                                @if(settings()->enable_google_login ?? true)
                                <a href="{{ url('auth/google') }}" class="btn btn-outline-danger"><i
                                        class="lni lni-google"></i>
                                    Join with Google</a>
                                @endif
                                @if(settings()->enable_linkedin_login ?? true)
                                <a href="{{ url('auth/linkedin') }}" class="btn btn-outline-primary"><i
                                        class="fab fa-linkedin"></i>
                                    Join with LinkedIn</a>
                                @endif
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
    <script>
        (function() {
            function toTitleCase(str) {
                if (!str) return '';
                return str.toLowerCase().split(/\s+/).map(function(word) {
                    return word.charAt(0).toUpperCase() + word.slice(1);
                }).join(' ');
            }

            var missingCheckbox = document.getElementById('job_missing_register');
            var customWrap = document.getElementById('job_title_custom_wrap_register');
            var customInput = document.getElementById('job_title_custom_register');
            var dropdown = document.querySelector('select[name="job"]');

            function syncJobInputs() {
                if (!missingCheckbox || !customWrap || !dropdown) return;
                if (missingCheckbox.checked) {
                    customWrap.style.display = '';
                    dropdown.removeAttribute('required');
                } else {
                    customWrap.style.display = 'none';
                    dropdown.setAttribute('required', 'required');
                    if (customInput) customInput.value = '';
                }
            }

            if (missingCheckbox) {
                missingCheckbox.addEventListener('change', syncJobInputs);
                syncJobInputs();
            }

            document.querySelectorAll('.camel-case-input').forEach(function(input) {
                input.addEventListener('blur', function() {
                    if (this.value && this.value.trim() !== '') {
                        this.value = toTitleCase(this.value.trim());
                    }
                });
            });
        })();
    </script>
@endsection
