<ul class="nav nav-tabs learning-subnav mb-4">
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
    <li class="nav-item">
        <a class="nav-link {{ ($learningNav ?? 'courses') === 'ai' ? 'active' : '' }}" href="{{ route('admin.courses.ai-config') }}">
            <i class="fa fa-robot me-1"></i>{{ __('admin_nav.ai_config') }}
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ ($learningNav ?? 'courses') === 'sitemap' ? 'active' : '' }}" href="{{ route('admin.courses.sitemap') }}">
            <i class="fa fa-sitemap me-1"></i>{{ __('admin_nav.sitemap') }}
        </a>
    </li>
</ul>
