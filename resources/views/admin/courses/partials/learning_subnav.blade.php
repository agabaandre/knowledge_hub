<ul class="nav nav-tabs mb-3">
    <li class="nav-item">
        <a class="nav-link {{ ($learningNav ?? 'courses') === 'courses' ? 'active' : '' }}" href="{{ route('admin.courses.index') }}">
            <i class="fa fa-list me-1"></i>{{ __('admin_nav.courses') }}
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ ($learningNav ?? 'courses') === 'integrations' ? 'active' : '' }}" href="{{ route('admin.courses.integrations') }}">
            <i class="fa fa-plug me-1"></i>{{ __('admin_nav.platform_integrations') }}
        </a>
    </li>
</ul>
