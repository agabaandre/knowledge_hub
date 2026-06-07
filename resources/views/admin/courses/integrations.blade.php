@extends(admin_layout())

@section('styles')
@include('admin.courses.partials.learning_admin_styles')
@endsection

@section('content')
<div class="container-fluid py-3 learning-admin-page">
    @include('layouts.partials.alerts')
    @include('admin.courses.partials.learning_subnav', ['learningNav' => 'integrations'])

    <div class="card learning-hero shadow-sm mb-4">
        <div class="card-body p-4">
            <h3><i class="fa fa-plug me-2" style="color: var(--la-primary);"></i>{{ __('admin_nav.learning_platform_integrations') }}</h3>
            <p>{{ __('admin_nav.learning_platform_integrations_intro') }}</p>
            <div class="d-flex flex-wrap gap-2 mt-3">
                <span class="learning-stat-pill"><i class="fa fa-book"></i> Moodle</span>
                <span class="learning-stat-pill"><i class="fa fa-server"></i> Frappe LMS</span>
                <span class="learning-stat-pill"><i class="fa fa-graduation-cap"></i> Open edX</span>
            </div>
        </div>
    </div>

    <form method="post" action="{{ route('admin.courses.integrations.save') }}">
        @csrf
        @include('admin.courses.partials.learning_integrations_form')

        <div class="learning-save-bar">
            <button type="submit" class="btn btn-primary">
                <i class="fa fa-save me-1"></i>{{ __('admin_nav.save_integrations') }}
            </button>
            <a href="{{ route('admin.courses.index') }}" class="btn btn-outline-secondary">{{ __('admin_nav.back_to_courses') }}</a>
        </div>
    </form>
</div>
@endsection
