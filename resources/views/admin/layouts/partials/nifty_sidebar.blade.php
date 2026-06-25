<div class="mainnav__categoriy py-3">
    <h6 class="mainnav__caption mt-0 fw-bold">{{ __('admin_nav.menu_caption') }}</h6>
    <ul class="mainnav__menu nav flex-column">
        <li class="nav-item has-sub">
            <a href="#" class="mininav-toggle nav-link {{ request()->is('admin/dashboard*') && !request()->is('admin/dashboards') ? 'active' : '' }} collapsed" data-bs-toggle="collapse" data-bs-target="#nav-dashboard"><i class="fa fa-th-large fs-5 me-2"></i><span class="nav-label ms-1">{{ __('admin_nav.dashboard') }}</span></a>
            <ul class="mininav-content nav collapse {{ request()->is('admin/dashboard*') ? 'show' : '' }}" id="nav-dashboard">
                @if(isset($dashboards) && $dashboards->count() > 0)
                    @foreach ($dashboards as $dashboard)
                        @php
                            $dashboard_url = ($dashboard->is_embedded == 1) ? url('/admin/dashboards') . '?resource=' . $dashboard->id : ($dashboard->publication ?? url('/admin/dashboards') . '?resource=' . $dashboard->id);
                            $app_url = config('app.url');
                            $url_parsed = parse_url($dashboard_url);
                            $app_url_parsed = parse_url($app_url);
                            $is_external_link = isset($url_parsed['host']) && isset($app_url_parsed['host']) && $url_parsed['host'] !== $app_url_parsed['host'];
                        @endphp
                        <li class="nav-item"><a href="{{ $dashboard_url }}" target="{{ $is_external_link ? '_blank' : '_self' }}" class="nav-link">{{ $dashboard->title }}</a></li>
                    @endforeach
                @else
                    <li class="nav-item"><a href="{{ url('admin/dashboard') }}" class="nav-link">{{ __('admin_nav.view_all_dashboards') }}</a></li>
                @endif
            </ul>
        </li>
        @can('view_rcc_dashboard')
            @if (states_enabled())
                @can('view_performance')
                <li class="nav-item has-sub">
                    <a href="#" class="mininav-toggle nav-link {{ request()->is('admin/rccdashboards*') || request()->is('admin/kpi*') ? 'active' : '' }} collapsed" data-bs-toggle="collapse" data-bs-target="#nav-kpis"><i class="fa fa-chart-bar fs-5 me-2"></i><span class="nav-label ms-1">{{ __('admin_nav.kpis') }}</span></a>
                    <ul class="mininav-content nav collapse {{ request()->is('admin/rccdashboards*') || request()->is('admin/kpi*') ? 'show' : '' }}" id="nav-kpis">
                        <li class="nav-item"><a href="{{ route('admin.rccdashboards') }}" class="nav-link {{ request()->is('admin/rccdashboards*') ? 'active' : '' }}">{{ __('admin_nav.rcc_dashboard') }}</a></li>
                        <li class="nav-item"><a href="{{ url('admin/kpi') }}" class="nav-link {{ request()->is('admin/kpi') && !request()->is('admin/kpi/*') ? 'active' : '' }}">{{ __('admin_nav.manage_indicators') }}</a></li>
                        <li class="nav-item"><a href="{{ url('admin/kpi/subject-areas') }}" class="nav-link {{ request()->is('admin/kpi/subject-areas*') ? 'active' : '' }}">{{ __('admin_nav.subject_areas') }}</a></li>
                        <li class="nav-item"><a href="{{ url('admin/kpi/data') }}" class="nav-link {{ request()->is('admin/kpi/data*') ? 'active' : '' }}">{{ __('admin_nav.country_values') }}</a></li>
                    </ul>
                </li>
                @endcan
            @endif
        @endcan
        @can('view_publications')
        <li class="nav-item has-sub">
            @php
                $publishMenuPending = (int)($pending_publications_count ?? 0) + (int)($pending_content_requests_count ?? 0);
            @endphp
            <a href="#" class="mininav-toggle nav-link collapsed" data-bs-toggle="collapse" data-bs-target="#nav-publish"><i class="fa fa-file-alt fs-5 me-2"></i><span class="nav-label ms-1">{{ __('admin_nav.publish') }}</span>@if($publishMenuPending > 0)<span class="badge bg-danger rounded-pill ms-1">{{ $publishMenuPending > 99 ? '99+' : $publishMenuPending }}</span>@endif</a>
            <ul class="mininav-content nav collapse" id="nav-publish">
                <li class="nav-item"><a href="{{ url('admin/publications/create') }}" class="nav-link">{{ __('admin_nav.publish_a_resource') }}</a></li>
                <li class="nav-item"><a href="{{ url('admin/publications') }}" class="nav-link">{{ __('admin_nav.manage_resources') }}</a></li>
                <li class="nav-item"><a href="{{ url('admin/publications/pending') }}" class="nav-link">{{ __('admin_nav.pending_approval') }} @if(isset($pending_publications_count) && $pending_publications_count > 0)<span class="badge bg-danger ms-1">{{ $pending_publications_count }}</span>@endif</a></li>
                <li class="nav-item"><a href="{{ url('admin/publications/rejected') }}" class="nav-link">{{ __('admin_nav.rejected_resources') }} @if(isset($rejected_publications_count) && $rejected_publications_count > 0)<span class="badge bg-secondary ms-1">{{ $rejected_publications_count }}</span>@endif</a></li>
                <li class="nav-item"><a href="{{ url('admin/publications/summaries') }}" class="nav-link">{{ __('admin_nav.summaries_abstracts') }}</a></li>
                <li class="nav-item"><a href="{{ url('admin/publications/moderate') }}" class="nav-link">{{ __('admin_nav.moderate_comments') }} @if(isset($pending_publication_comments_count) && $pending_publication_comments_count > 0)<span class="badge bg-danger ms-1">{{ $pending_publication_comments_count }}</span>@endif</a></li>
                <li class="nav-item"><a href="{{ route('admin.rss_feeds.index') }}" class="nav-link">{{ __('admin_nav.rss_feeds') }}</a></li>
                <li class="nav-item"><a href="{{ route('admin.rss_staging.index') }}" class="nav-link">{{ __('admin_nav.rss_staging') }}</a></li>
                <li class="nav-item"><a href="{{ route('admin.participant-badges.index') }}" class="nav-link">{{ __('admin_nav.participant_badge_management') }}</a></li>
                @can('view_content_requests')
                <li class="nav-item"><a href="{{ route('admin.content-requests.index') }}" class="nav-link"><i class="fa fa-bell me-1 {{ (isset($pending_content_requests_count) && $pending_content_requests_count > 0) ? 'text-warning' : '' }}"></i>{{ __('admin_nav.content_requests') }} @if(isset($pending_content_requests_count) && $pending_content_requests_count > 0)<span class="badge bg-danger ms-1 rounded-pill">{{ $pending_content_requests_count > 99 ? '99+' : $pending_content_requests_count }}</span>@endif</a></li>
                <li class="nav-item"><a href="{{ route('admin.content-requests.index', ['status' => 'processed']) }}" class="nav-link"><i class="fa fa-check-circle me-1 text-success"></i>{{ __('admin_nav.processed_content_requests') }} @if(isset($processed_content_requests_count) && $processed_content_requests_count > 0)<span class="badge bg-success ms-1 rounded-pill">{{ $processed_content_requests_count > 99 ? '99+' : $processed_content_requests_count }}</span>@endif</a></li>
                @endcan
                @can('manage_experts')<li class="nav-item"><a href="{{ url('admin/experts') }}" class="nav-link">{{ __('admin_nav.roster_of_experts') }}</a></li>@endcan
                @can('manage_facts')<li class="nav-item"><a href="{{ url('admin/facts') }}" class="nav-link">{{ __('admin_nav.facts') }}</a></li>@endcan
                @can('view_quotes')<li class="nav-item"><a href="{{ url('admin/quotes') }}" class="nav-link">{{ __('admin_nav.quotes') }}</a></li>@endcan
                @can('view_quize')<li class="nav-item"><a href="{{ url('admin/quiz') }}" class="nav-link">{{ __('admin_nav.quiz') }}</a></li>@endcan
            </ul>
        </li>
        @endcan
        @can('view_forumns')
        <li class="nav-item"><a href="{{ url('admin/messaging') }}" class="nav-link mininav-toggle"><i class="fa fa-envelope fs-5 me-2"></i><span class="nav-label ms-1">{{ __('admin_nav.messaging') }}</span></a></li>
        <li class="nav-item has-sub">
            <a href="#" class="mininav-toggle nav-link collapsed" data-bs-toggle="collapse" data-bs-target="#nav-forums"><i class="fa fa-comments fs-5 me-2"></i><span class="nav-label ms-1">{{ __('admin_nav.forums') }}</span>@if(isset($pending_forums_count) && $pending_forums_count > 0)<span class="badge bg-danger rounded-pill ms-1">{{ $pending_forums_count }}</span>@endif</a>
            <ul class="mininav-content nav collapse" id="nav-forums">
                <li class="nav-item"><a href="{{ url('admin/forums') }}" class="nav-link">{{ __('admin_nav.pending_approval_forums') }}</a></li>
                <li class="nav-item"><a href="{{ url('admin/forums/approved') }}" class="nav-link">{{ __('admin_nav.approved_forums') }}</a></li>
                <li class="nav-item"><a href="{{ url('admin/forums/rejected') }}" class="nav-link">{{ __('admin_nav.rejected_forums') }}</a></li>
                <li class="nav-item"><a href="{{ url('admin/forums/moderate') }}" class="nav-link">{{ __('admin_nav.moderate_forums') }}</a></li>
            </ul>
        </li>
        <li class="nav-item has-sub">
            <a href="#" class="mininav-toggle nav-link collapsed" data-bs-toggle="collapse" data-bs-target="#nav-learning"><i class="fa fa-book fs-5 me-2"></i><span class="nav-label ms-1">{{ __('admin_nav.learning') }}</span></a>
            <ul class="mininav-content nav collapse" id="nav-learning">
                <li class="nav-item"><a href="{{ route('admin.courses.index') }}" class="nav-link">{{ __('admin_nav.courses') }}</a></li>
                <li class="nav-item"><a href="{{ route('admin.courses.integrations') }}" class="nav-link">{{ __('admin_nav.platform_integrations') }}</a></li>
                <li class="nav-item"><a href="{{ route('admin.courses.ai-config') }}" class="nav-link">{{ __('admin_nav.ai_config') }}</a></li>
                <li class="nav-item"><a href="{{ route('admin.courses.sitemap') }}" class="nav-link">{{ __('admin_nav.sitemap') }}</a></li>
                @can('view_events')
                <li class="nav-item"><a href="{{ url('admin/events') }}" class="nav-link">{{ __('admin_nav.events') }}</a></li>
                @endcan
            </ul>
        </li>
        <li class="nav-item has-sub">
            <a href="#" class="mininav-toggle nav-link collapsed" data-bs-toggle="collapse" data-bs-target="#nav-cops"><i class="fa fa-users fs-5 me-2"></i><span class="nav-label ms-1">{{ __('admin_nav.cops') }}</span>@if(isset($pending_cop_approvals_count) && $pending_cop_approvals_count > 0)<span class="badge bg-danger rounded-pill ms-1" style="background:#dc3545!important;color:#fff!important;">{{ $pending_cop_approvals_count }}</span>@endif</a>
            <ul class="mininav-content nav collapse" id="nav-cops">
                <li class="nav-item"><a href="{{ url('admin/commsofpractice') }}" class="nav-link">{{ __('admin_nav.manage_cops') }}</a></li>
                <li class="nav-item"><a href="{{ route('admin.commsofpractice.participants') }}" class="nav-link">{{ __('admin_nav.cop_participants_directory') }}@if(isset($pending_cop_approvals_count) && $pending_cop_approvals_count > 0)<span class="badge bg-danger rounded-pill ms-1" style="background:#dc3545!important;color:#fff!important;">{{ $pending_cop_approvals_count }}</span>@endif</a></li>
            </ul>
        </li>
        @endcan
        <li class="nav-item has-sub">
            <a href="#" class="mininav-toggle nav-link collapsed" data-bs-toggle="collapse" data-bs-target="#nav-dropdowns"><i class="fa fa-list fs-5 me-2"></i><span class="nav-label ms-1">{{ __('admin_nav.dropdown_lists') }}</span></a>
            <ul class="mininav-content nav collapse" id="nav-dropdowns">
                @can('view_file_types')<li class="nav-item"><a href="{{ url('admin/filetypes') }}" class="nav-link">{{ __('admin_nav.resource_and_asset_types') }}</a></li>@endcan
                @can('view_file_types')<li class="nav-item"><a href="{{ url('admin/tools') }}" class="nav-link">{{ __('admin_nav.tools') }}</a></li>@endcan
                @can('view_sources')<li class="nav-item"><a href="{{ url('admin/authors') }}" class="nav-link">{{ __('admin_nav.data_sources') }}</a></li>@endcan
                @can('view_sources')<li class="nav-item"><a href="{{ url('admin/datarecords/categories') }}" class="nav-link">{{ __('admin_nav.categories') }}</a></li>@endcan
                @can('view_sources')<li class="nav-item"><a href="{{ route('admin.subcategories.index') }}" class="nav-link">{{ __('admin_nav.sub_categories') }}</a></li>@endcan
                @can('view_themes')<li class="nav-item"><a href="{{ url('admin/themes') }}" class="nav-link">{{ __('admin_nav.security_themes') }}</a></li>@endcan
                @can('view_sub_themes')<li class="nav-item"><a href="{{ url('admin/subthemes') }}" class="nav-link">{{ __('admin_nav.security_sub_themes') }}</a></li>@endcan
                @can('view_faqs')<li class="nav-item"><a href="{{ url('admin/faqs') }}" class="nav-link">{{ __('admin_nav.faqs') }}</a></li>@endcan
                @can('view_geo_coverage')<li class="nav-item"><a href="{{ states_enabled() ? url('admin/areas') : url('admin/adminunits') }}" class="nav-link">{{ states_enabled() ? __('admin_nav.geographical_coverage') : __('frontend_nav.administrative_units') }}</a></li>@endcan
                @can('view_asset_types')<li class="nav-item"><a href="{{ url('admin/assettypes') }}" class="nav-link">{{ __('admin_nav.health_asset_types') }}</a></li>@endcan
                @can('view_assets')<li class="nav-item"><a href="{{ url('admin/healthassets') }}" class="nav-link">{{ __('admin_nav.health_assets') }}</a></li>@endcan
                @can('manage_experts')<li class="nav-item"><a href="{{ url('admin/experts/types') }}" class="nav-link">{{ __('admin_nav.workforce_types') }}</a></li>@endcan
                @can('view_tags')<li class="nav-item"><a href="{{ url('admin/tags') }}" class="nav-link">{{ __('admin_nav.tags') }}</a></li>@endcan
                @can('manage_experts')<li class="nav-item"><a href="{{ url('admin/licenses') }}" class="nav-link">{{ __('admin_nav.licenses') }}</a></li>@endcan
                <li class="nav-item"><a href="{{ url('admin/static-links') }}" class="nav-link">{{ __('admin_nav.static_links') }}</a></li>
                @can('view_privacy_policy')<li class="nav-item"><a href="{{ url('admin/privacy') }}" class="nav-link">{{ __('admin_nav.privacy_policy') }}</a></li>@endcan
            </ul>
        </li>
        <li class="nav-item has-sub">
            <a href="#" class="mininav-toggle nav-link {{ request()->is('admin/search-logs*') ? '' : 'collapsed' }}" data-bs-toggle="collapse" data-bs-target="#nav-settings"><i class="fa fa-cog fs-5 me-2"></i><span class="nav-label ms-1">{{ __('admin_nav.settings') }}</span></a>
            <ul class="mininav-content nav collapse {{ request()->is('admin/search-logs*') ? 'show' : '' }}" id="nav-settings">
                <li class="nav-item"><a href="{{ route('admin.storage.index') }}" class="nav-link">{{ __('admin_nav.storage_management') }}</a></li>
                <li class="nav-item"><a href="{{ route('admin.maps.index') }}" class="nav-link {{ request()->is('admin/maps*') ? 'active' : '' }}">{{ __('admin_nav.maps_management') }}</a></li>
                <li class="nav-item"><a href="{{ route('admin.federation.index') }}" class="nav-link">{{ __('admin_nav.federated_hubs') }}</a></li>
                @if(function_exists('federation_consumer_enabled') && federation_consumer_enabled())
                <li class="nav-item"><a href="{{ route('admin.federation.pending-content') }}" class="nav-link">{{ __('admin_nav.federated_content_pending') }}</a></li>
                @endif
                <li class="nav-item"><a href="{{ url('admin/configure') }}" class="nav-link">{{ __('admin_nav.system_configurations') }}</a></li>
                <li class="nav-item"><a href="{{ route('admin.site-languages.index') }}" class="nav-link">{{ __('admin_nav.site_languages') }}</a></li>
                <li class="nav-item"><a href="{{ route('admin.language-management.index') }}" class="nav-link">{{ __('admin_nav.language_management') }}</a></li>
                <li class="nav-item"><a href="{{ url('admin/accessgroups') }}" class="nav-link">{{ __('admin_nav.content_access_groups') }}</a></li>
                <li class="nav-item"><a href="{{ url('permissions/users') }}" class="nav-link">{{ __('admin_nav.manage_users') }}</a></li>
                <li class="nav-item"><a href="{{ url('permissions/roles') }}" class="nav-link">{{ __('admin_nav.roles') }}</a></li>
                <li class="nav-item"><a href="{{ url('permissions') }}" class="nav-link">{{ __('admin_nav.permissions') }}</a></li>
                <li class="nav-item"><a href="{{ url('admin/logs/user') }}" class="nav-link">{{ __('admin_nav.user_logs') }}</a></li>
                <li class="nav-item"><a href="{{ url('admin/logs/access') }}" class="nav-link">{{ __('admin_nav.site_access_logs') }}</a></li>
                <li class="nav-item"><a href="{{ route('admin.search-logs.index') }}" class="nav-link">{{ __('admin_nav.search_history') }}</a></li>
                <li class="nav-item"><a href="{{ url('admin/metrics') }}" class="nav-link">{{ __('admin_nav.system_metrics') }}</a></li>
                @can('view_mailing_list')<li class="nav-item"><a href="{{ url('mailing_list') }}" class="nav-link">{{ __('admin_nav.mailing_list') }}</a></li>@endcan
            </ul>
        </li>
    </ul>
</div>
