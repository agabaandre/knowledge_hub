@extends(admin_layout())

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">{{ __('admin_nav.ai_config') }}</h4>
                    <p class="text-muted small mb-0 mt-1">{{ __('admin_nav.ai_config_intro') }}</p>
                </div>
                <div class="card-body">
                    @include('layouts.partials.alerts')
                    @include('admin.courses.partials.learning_subnav', ['learningNav' => 'ai'])

                    <form method="post" action="{{ route('admin.courses.ai-config.save') }}">
                        @csrf
                        @include('admin.courses.partials.ai_config_form')
                        <div class="d-flex flex-wrap gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-save me-1"></i>{{ __('admin_nav.save_ai_config') }}
                            </button>
                            <a href="{{ route('admin.courses.index') }}" class="btn btn-outline-secondary">{{ __('admin_nav.back_to_courses') }}</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
