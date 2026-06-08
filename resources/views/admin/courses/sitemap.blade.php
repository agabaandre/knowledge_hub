@extends(admin_layout())

@section('styles')
@include('admin.courses.partials.learning_admin_styles')
@endsection

@section('content')
<div class="container-fluid py-3 learning-admin-page">
    @include('layouts.partials.alerts')
    @include('admin.courses.partials.learning_subnav', ['learningNav' => 'sitemap'])

    <div class="card learning-hero shadow-sm mb-4">
        <div class="card-body p-4">
            <h3><i class="fa fa-sitemap me-2" style="color: var(--la-primary);"></i>{{ __('admin_nav.sitemap') }}</h3>
            <p class="mb-3">{{ __('admin_nav.sitemap_intro') }}</p>
            <div class="d-flex flex-wrap gap-2">
                <span class="learning-stat-pill">
                    <i class="fa fa-layer-group"></i>
                    {{ number_format($sitemapPage['section_count'] ?? 0) }} {{ __('admin_nav.sitemap_sections') }}
                </span>
                <span class="learning-stat-pill">
                    <i class="fa fa-file-lines"></i>
                    {{ number_format($sitemapPage['publication_count'] ?? 0) }} {{ __('admin_nav.sitemap_publications') }}
                </span>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-5">
            <div class="card shadow-sm h-100">
                <div class="card-body p-4">
                    <h5 class="mb-3">{{ __('admin_nav.sitemap_generate_title') }}</h5>
                    <p class="text-muted small mb-4">{{ __('admin_nav.sitemap_generate_help') }}</p>
                    <form method="post" action="{{ route('admin.courses.sitemap.generate') }}">
                        @csrf
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-rotate me-1"></i>{{ __('admin_nav.sitemap_generate_button') }}
                        </button>
                    </form>
                    <p class="text-muted small mt-3 mb-0">
                        <i class="fa fa-clock me-1"></i>{{ __('admin_nav.sitemap_schedule_note') }}
                    </p>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card shadow-sm h-100">
                <div class="card-body p-4">
                    <h5 class="mb-3">{{ __('admin_nav.sitemap_public_urls') }}</h5>
                    <ul class="list-unstyled mb-4">
                        <li class="mb-2">
                            <strong>{{ __('admin_nav.sitemap_index') }}:</strong>
                            <a href="{{ $sitemapPage['sitemap_index_url'] ?? url('sitemap.xml') }}" target="_blank" rel="noopener noreferrer">
                                {{ $sitemapPage['sitemap_index_url'] ?? url('sitemap.xml') }}
                                <i class="fa fa-arrow-up-right-from-square ms-1" style="font-size:0.75rem;"></i>
                            </a>
                        </li>
                        <li>
                            <strong>{{ __('admin_nav.robots_txt') }}:</strong>
                            <a href="{{ $sitemapPage['robots_url'] ?? url('robots.txt') }}" target="_blank" rel="noopener noreferrer">
                                {{ $sitemapPage['robots_url'] ?? url('robots.txt') }}
                                <i class="fa fa-arrow-up-right-from-square ms-1" style="font-size:0.75rem;"></i>
                            </a>
                        </li>
                    </ul>

                    <h6 class="text-muted text-uppercase small mb-2">{{ __('admin_nav.sitemap_section_list') }}</h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-striped mb-0">
                            <thead>
                                <tr>
                                    <th>{{ __('admin_nav.sitemap_section') }}</th>
                                    <th>{{ __('admin_nav.sitemap_lastmod') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($sitemapPage['sections'] ?? [] as $section)
                                    <tr>
                                        <td>
                                            <a href="{{ $section['loc'] ?? '#' }}" target="_blank" rel="noopener noreferrer">
                                                {{ $section['loc'] ?? '' }}
                                            </a>
                                        </td>
                                        <td>{{ $section['lastmod'] ?? '—' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
