@extends('layouts.app')

@section('styles')
<style>
    .content-request-wrapper {
        min-height: calc(100vh - 200px);
        padding: 3rem 0;
        background: transparent;
    }

    .content-request-card {
        background: #ffffff;
        border-radius: 16px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        max-width: 900px;
        margin: 0 auto;
        border: 1px solid #e2e8f0;
    }

    .content-request-header {
        background: linear-gradient(135deg, var(--theme-color-primary, #119A48) 0%, color-mix(in srgb, var(--theme-color-primary, #119A48) 85%, black) 100%);
        padding: 2.5rem 2.5rem 2rem;
        text-align: center;
        color: white;
    }

    .content-request-header h2 {
        font-size: 2rem;
        font-weight: 700;
        margin: 0 0 0.5rem 0;
        color: white;
    }

    .content-request-header p {
        margin: 0;
        opacity: 0.95;
        font-size: 1rem;
    }

    .content-request-body {
        padding: 2.5rem 2.5rem;
    }

    .form-section-title {
        font-size: 1.125rem;
        font-weight: 600;
        color: #2d3748;
        margin-bottom: 1.5rem;
        padding-bottom: 0.75rem;
        border-bottom: 2px solid #e2e8f0;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .form-section-title i {
        color: var(--theme-color-primary, #119A48);
        font-size: 1.25rem;
    }

    .form-label {
        font-weight: 600;
        color: #2d3748;
        font-size: 0.9375rem;
        margin-bottom: 0.5rem;
        display: block;
    }

    .form-label .required {
        color: #e53e3e;
        margin-left: 3px;
    }

    .form-control-custom {
        border: 2px solid #e2e8f0;
        border-radius: 8px;
        padding: 0.875rem 1rem;
        font-size: 0.95rem;
        transition: all 0.3s ease;
        background: #f8f9fa;
        width: 100%;
        box-sizing: border-box;
    }

    .form-control-custom:focus {
        border-color: var(--theme-color-primary, #119A48);
        background: #ffffff;
        box-shadow: 0 0 0 3px rgba(17, 154, 72, 0.1);
        outline: none;
    }

    .form-control-custom::placeholder {
        color: #a0aec0;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-row {
        display: flex;
        gap: 1.5rem;
        margin-bottom: 0;
    }

    .form-col {
        flex: 1;
    }

    .invalid-feedback {
        display: block;
        width: 100%;
        margin-top: 0.375rem;
        font-size: 0.875rem;
        color: #e53e3e;
        font-weight: 500;
    }

    .form-control-custom.is-invalid {
        border-color: #e53e3e;
        background: #fff5f5;
    }

    /* Style Select2 dropdown to match form controls */
    .select2-container .select2-selection--single {
        border: 2px solid #e2e8f0;
        border-radius: 8px;
        padding: 0.5rem;
        height: auto;
        background: #f8f9fa;
        transition: all 0.3s ease;
    }

    .select2-container--default .select2-selection--single:focus,
    .select2-container--default.select2-container--focus .select2-selection--single {
        border-color: var(--theme-color-primary, #119A48);
        background: #ffffff;
        box-shadow: 0 0 0 3px rgba(17, 154, 72, 0.1);
        outline: none;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        padding: 0;
        line-height: 1.5;
        color: #2d3748;
    }

    .submit-section {
        margin-top: 2.5rem;
        padding-top: 2rem;
        border-top: 2px solid #e2e8f0;
        display: flex;
        justify-content: flex-end;
        gap: 1rem;
    }

    .btn-submit {
        background: var(--theme-color-primary, #119A48);
        border: none;
        border-radius: 8px;
        padding: 0.875rem 2rem;
        font-size: 1rem;
        font-weight: 600;
        color: white;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .btn-submit:hover {
        background: color-mix(in srgb, var(--theme-color-primary, #119A48) 85%, black);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(17, 154, 72, 0.3);
        color: white;
    }

    .btn-submit:active {
        transform: translateY(0);
    }

    .btn-cancel {
        background: #e2e8f0;
        border: none;
        border-radius: 8px;
        padding: 0.875rem 2rem;
        font-size: 1rem;
        font-weight: 600;
        color: #4a5568;
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
    }

    .btn-cancel:hover {
        background: #cbd5e0;
        color: #2d3748;
        text-decoration: none;
    }

    .info-box {
        background: #edf2f7;
        border-left: 4px solid var(--theme-color-primary, #119A48);
        padding: 1rem 1.25rem;
        border-radius: 6px;
        margin-bottom: 2rem;
    }

    .info-box p {
        margin: 0;
        color: #4a5568;
        font-size: 0.9375rem;
        line-height: 1.6;
    }

    .info-box i {
        color: var(--theme-color-primary, #119A48);
        margin-right: 0.5rem;
    }

    @media (max-width: 768px) {
        .content-request-wrapper {
            padding: 2rem 1rem;
        }

        .content-request-header {
            padding: 2rem 1.5rem 1.5rem;
        }

        .content-request-header h2 {
            font-size: 1.5rem;
        }

        .content-request-body {
            padding: 2rem 1.5rem;
        }

        .form-row {
            flex-direction: column;
            gap: 0;
        }

        .submit-section {
            flex-direction: column;
        }

        .btn-submit,
        .btn-cancel {
            width: 100%;
            justify-content: center;
        }
    }
</style>
@endsection

@section('content')
    <div class="gray py-4">
        <div class="content-request-wrapper">
            <div class="container">
                <div class="content-request-card">
                    <div class="content-request-header">
                        <h2><i class="fa fa-file-alt me-2"></i>Content Request Form</h2>
                        <p>Help us understand what content you need</p>
            </div>

                    <div class="content-request-body">
                        @if(Session::has('message') || Session::has('alert'))
                            <div class="alert alert-{{ Session::get('alert_class', Session::has('message') ? 'success' : 'info') }} alert-dismissible fade show" role="alert">
                                {{ Session::get('message') ?? Session::get('alert') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        @if($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <strong>Please fix the following errors:</strong>
                                <ul class="mb-0 mt-2">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        <div class="info-box">
                            <p>
                                <i class="fa fa-info-circle"></i>
                                <strong>Request Content:</strong> Use this form to request specific health-related content that you'd like to see added to our knowledge hub. We review all requests and will do our best to provide the information you need.
                            </p>
                        </div>

                        <form method="POST" action="{{ route('content-request') }}" id="content_request_form" enctype="multipart/form-data" data-parsley-validate="">
                        @csrf

                            <div class="form-section">
                                <div class="form-section-title">
                                    <i class="fa fa-clipboard-list"></i>
                                    Request Details
                                </div>

                                <div class="form-group">
                                    <label class="form-label" for="title">
                                        What is the subject of your request?
                                        <span class="required">*</span>
                                    </label>
                                    <input type="text" 
                                           id="title" 
                                           name="title" 
                                           class="form-control-custom @error('title') is-invalid @enderror"
                                           placeholder="e.g., COVID-19 vaccination data for East Africa" 
                                           value="{{ old('title') }}" 
                                           required>
                                    @error('title')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label class="form-label" for="summernote">
                                        Share more details about your request
                                        <span class="required">*</span>
                                    </label>
                                    <textarea id="summernote" 
                                              name="description" 
                                              class="form-control-custom @error('description') is-invalid @enderror"
                                              placeholder="Please provide detailed information about the content you're requesting. Include any specific topics, regions, timeframes, or formats you need."
                                              rows="6"
                                              required>{!! old('description') !!}</textarea>
                                    @error('description')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            @if(!current_user())
                            <div class="form-section" style="margin-top: 2.5rem;">
                                <div class="form-section-title">
                                    <i class="fa fa-user"></i>
                                    Contact Information
                                </div>

                                <div class="form-row">
                                    <div class="form-col">
                                        <div class="form-group">
                                            <label class="form-label" for="country_id">
                                                Which country are you from?
                                                <span class="required">*</span>
                                            </label>
                                    @include('partials.countries.dropdown', [
                                        'field' => 'country_id',
                                        'required' => 'required',
                                                'class' => 'select2 form-control-custom @error("country_id") is-invalid @enderror',
                                                'selected' => old('country_id') ?? (current_user() ? current_user()->country_id : ''),
                                    ])
                                            @error('country_id')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                </div>
                            </div>

                                    <div class="form-col">
                                        <div class="form-group">
                                            <label class="form-label" for="email">
                                                Email Address
                                                <span class="required">*</span>
                                            </label>
                                            <input type="email" 
                                                   id="email" 
                                        name="email"
                                                   class="form-control-custom @error('email') is-invalid @enderror"
                                                   placeholder="your.email@example.com" 
                                                   value="{{ old('email') ?? (current_user() ? current_user()->email : '') }}"
                                                   required>
                                            @error('email')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                </div>
                            </div>
                                </div>
                            </div>
                            @else
                                <input type="hidden" name="country_id" value="{{ current_user()->country_id }}">
                                <input type="hidden" name="email" value="{{ current_user()->email }}">
                            @endif

                            <div class="form-group mt-3">
                                @php
                                    $recaptchaSiteKey = config('recaptcha.api_site_key');
                                    $showRecaptcha = $recaptchaSiteKey && !empty($recaptchaSiteKey);
                                @endphp
                                @if($showRecaptcha)
                                    {!! \Biscolab\ReCaptcha\Facades\ReCaptcha::htmlFormSnippet() !!}
                                    @error('g-recaptcha-response')
                                        <span class="text-danger small d-block mt-1">{{ $message }}</span>
                                    @enderror
                                @endif
                            </div>

                            <div class="submit-section">
                                <a href="{{ url('/') }}" class="btn-cancel">
                                    <i class="fa fa-times me-2"></i>Cancel
                                </a>
                                <button type="submit" class="btn-submit">
                                    <i class="fa fa-paper-plane me-2"></i>Submit Request
                                </button>
                        </div>
                    </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    @include('common.select2')
    @include('account.partials.create_js')
    @include('account.partials.wizard_js')
@endsection
