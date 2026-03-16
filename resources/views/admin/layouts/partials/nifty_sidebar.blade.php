<div class="mainnav__categoriy py-3">
    <h6 class="mainnav__caption mt-0 fw-bold">Menu</h6>
    <ul class="mainnav__menu nav flex-column">
        <li class="nav-item has-sub">
            <a href="#" class="mininav-toggle nav-link {{ request()->is('admin/dashboard*') && !request()->is('admin/dashboards') ? 'active' : '' }} collapsed" data-bs-toggle="collapse" data-bs-target="#nav-dashboard"><i class="fa fa-th-large fs-5 me-2"></i><span class="nav-label ms-1">Dashboard</span></a>
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
                    <li class="nav-item"><a href="{{ url('admin/dashboard') }}" class="nav-link">View All Dashboards</a></li>
                @endif
            </ul>
        </li>
        @can('view_rcc_dashboard')
            @if (states_enabled())
                @can('view_performance')
                <li class="nav-item has-sub">
                    <a href="#" class="mininav-toggle nav-link collapsed" data-bs-toggle="collapse" data-bs-target="#nav-kpis"><i class="fa fa-chart-bar fs-5 me-2"></i><span class="nav-label ms-1">KPIs</span></a>
                    <ul class="mininav-content nav collapse" id="nav-kpis">
                        <li class="nav-item"><a href="{{ url('admin/kpi') }}" class="nav-link">Add Indicator</a></li>
                        <li class="nav-item"><a href="{{ url('admin/kpi/data') }}" class="nav-link">Indicator Data</a></li>
                    </ul>
                </li>
                @endcan
            @endif
        @endcan
        @can('view_publications')
        <li class="nav-item has-sub">
            <a href="#" class="mininav-toggle nav-link collapsed" data-bs-toggle="collapse" data-bs-target="#nav-publish"><i class="fa fa-file-alt fs-5 me-2"></i><span class="nav-label ms-1">Publish</span>@if(isset($pending_publications_count) && $pending_publications_count > 0)<span class="badge bg-danger rounded-pill ms-1">{{ $pending_publications_count }}</span>@endif</a>
            <ul class="mininav-content nav collapse" id="nav-publish">
                <li class="nav-item"><a href="{{ url('admin/publications/create') }}" class="nav-link">Publish a Resource</a></li>
                <li class="nav-item"><a href="{{ url('admin/publications') }}" class="nav-link">Manage Resources</a></li>
                <li class="nav-item"><a href="{{ url('admin/publications/pending') }}" class="nav-link">Pending Approval @if(isset($pending_publications_count) && $pending_publications_count > 0)<span class="badge bg-danger ms-1">{{ $pending_publications_count }}</span>@endif</a></li>
                <li class="nav-item"><a href="{{ url('admin/publications/summaries') }}" class="nav-link">Summaries & Abstracts</a></li>
                <li class="nav-item"><a href="{{ url('admin/publications/moderate') }}" class="nav-link">Moderate Comments @if(isset($pending_publication_comments_count) && $pending_publication_comments_count > 0)<span class="badge bg-danger ms-1">{{ $pending_publication_comments_count }}</span>@endif</a></li>
                @can('view_content_requests')<li class="nav-item"><a href="{{ route('admin.content-requests.index') }}" class="nav-link">Content Requests</a></li>@endcan
                @can('manage_experts')<li class="nav-item"><a href="{{ url('admin/experts') }}" class="nav-link">Roster of Experts</a></li>@endcan
                @can('manage_facts')<li class="nav-item"><a href="{{ url('admin/facts') }}" class="nav-link">Facts</a></li>@endcan
                @can('view_quotes')<li class="nav-item"><a href="{{ url('admin/quotes') }}" class="nav-link">Quotes</a></li>@endcan
                @can('view_quize')<li class="nav-item"><a href="{{ url('admin/quiz') }}" class="nav-link">Quiz</a></li>@endcan
            </ul>
        </li>
        @endcan
        @can('view_forumns')
        <li class="nav-item"><a href="{{ url('admin/messaging') }}" class="nav-link mininav-toggle"><i class="fa fa-envelope fs-5 me-2"></i><span class="nav-label ms-1">Messaging</span></a></li>
        <li class="nav-item has-sub">
            <a href="#" class="mininav-toggle nav-link collapsed" data-bs-toggle="collapse" data-bs-target="#nav-forums"><i class="fa fa-comments fs-5 me-2"></i><span class="nav-label ms-1">Forums</span>@if(isset($pending_forums_count) && $pending_forums_count > 0)<span class="badge bg-danger rounded-pill ms-1">{{ $pending_forums_count }}</span>@endif</a>
            <ul class="mininav-content nav collapse" id="nav-forums">
                <li class="nav-item"><a href="{{ url('admin/forums') }}" class="nav-link">Forums</a></li>
                <li class="nav-item"><a href="{{ url('admin/forums/moderate') }}" class="nav-link">Moderate Forums</a></li>
            </ul>
        </li>
        <li class="nav-item has-sub">
            <a href="#" class="mininav-toggle nav-link collapsed" data-bs-toggle="collapse" data-bs-target="#nav-lms"><i class="fa fa-book fs-5 me-2"></i><span class="nav-label ms-1">LMS</span></a>
            <ul class="mininav-content nav collapse" id="nav-lms">
                <li class="nav-item"><a href="{{ url('admin/courses') }}" class="nav-link">Courses</a></li>
            </ul>
        </li>
        <li class="nav-item"><a href="{{ url('admin/commsofpractice') }}" class="nav-link mininav-toggle"><i class="fa fa-users fs-5 me-2"></i><span class="nav-label ms-1">COPs</span>@if(isset($pending_cop_approvals_count) && $pending_cop_approvals_count > 0)<span class="badge bg-danger rounded-pill ms-1" style="background:#dc3545!important;color:#fff!important;">{{ $pending_cop_approvals_count }}</span>@endif</a></li>
        @endcan
        @can('view_events')
        <li class="nav-item"><a href="{{ url('admin/events') }}" class="nav-link mininav-toggle"><i class="pli-calendar-4 fs-5 me-2"></i><span class="nav-label ms-1">Events</span></a></li>
        @endcan
        <li class="nav-item has-sub">
            <a href="#" class="mininav-toggle nav-link collapsed" data-bs-toggle="collapse" data-bs-target="#nav-dropdowns"><i class="fa fa-list fs-5 me-2"></i><span class="nav-label ms-1">Dropdown Lists</span></a>
            <ul class="mininav-content nav collapse" id="nav-dropdowns">
                @can('view_file_types')<li class="nav-item"><a href="{{ url('admin/filetypes') }}" class="nav-link">Resource and Asset Types</a></li>@endcan
                @can('view_file_types')<li class="nav-item"><a href="{{ url('admin/tools') }}" class="nav-link">Tools</a></li>@endcan
                @can('view_sources')<li class="nav-item"><a href="{{ url('admin/authors') }}" class="nav-link">Data Sources</a></li>@endcan
                @can('view_sources')<li class="nav-item"><a href="{{ url('admin/datarecords/categories') }}" class="nav-link">Categories</a></li>@endcan
                @can('view_themes')<li class="nav-item"><a href="{{ url('admin/themes') }}" class="nav-link">Security Themes</a></li>@endcan
                @can('view_sub_themes')<li class="nav-item"><a href="{{ url('admin/subthemes') }}" class="nav-link">Security Sub-Themes</a></li>@endcan
                @can('view_faqs')<li class="nav-item"><a href="{{ url('admin/faqs') }}" class="nav-link">FAQs</a></li>@endcan
                @can('view_geo_coverage')<li class="nav-item"><a href="{{ states_enabled() ? url('admin/areas') : url('admin/adminunits') }}" class="nav-link">{{ states_enabled() ? 'Geographical Coverage' : 'Administrative Units' }}</a></li>@endcan
                @can('view_asset_types')<li class="nav-item"><a href="{{ url('admin/assettypes') }}" class="nav-link">Health Asset Types</a></li>@endcan
                @can('view_assets')<li class="nav-item"><a href="{{ url('admin/healthassets') }}" class="nav-link">Health Assets</a></li>@endcan
                @can('manage_experts')<li class="nav-item"><a href="{{ url('admin/experts/types') }}" class="nav-link">Workforce Types</a></li>@endcan
                @can('view_tags')<li class="nav-item"><a href="{{ url('admin/tags') }}" class="nav-link">Tags</a></li>@endcan
                @can('manage_experts')<li class="nav-item"><a href="{{ url('admin/licenses') }}" class="nav-link">Licenses</a></li>@endcan
                <li class="nav-item"><a href="{{ url('admin/static-links') }}" class="nav-link">Static Links</a></li>
                @can('view_privacy_policy')<li class="nav-item"><a href="{{ url('admin/privacy') }}" class="nav-link">Privacy Policy</a></li>@endcan
                <li class="nav-item"><a href="{{ url('admin/events') }}" class="nav-link">Events</a></li>
            </ul>
        </li>
        <li class="nav-item has-sub">
            <a href="#" class="mininav-toggle nav-link collapsed" data-bs-toggle="collapse" data-bs-target="#nav-settings"><i class="fa fa-cog fs-5 me-2"></i><span class="nav-label ms-1">Settings</span></a>
            <ul class="mininav-content nav collapse" id="nav-settings">
                <li class="nav-item"><a href="{{ url('admin/configure') }}" class="nav-link">System Configurations</a></li>
                <li class="nav-item"><a href="{{ url('admin/accessgroups') }}" class="nav-link">Content Access Groups</a></li>
                <li class="nav-item"><a href="{{ url('permissions/users') }}" class="nav-link">Manage Users</a></li>
                <li class="nav-item"><a href="{{ url('permissions/roles') }}" class="nav-link">Roles</a></li>
                <li class="nav-item"><a href="{{ url('permissions') }}" class="nav-link">Permissions</a></li>
                <li class="nav-item"><a href="{{ url('admin/logs/user') }}" class="nav-link">User Logs</a></li>
                <li class="nav-item"><a href="{{ url('admin/logs/access') }}" class="nav-link">Site Access Logs</a></li>
                <li class="nav-item"><a href="{{ url('admin/metrics') }}" class="nav-link">System Metrics</a></li>
                @can('view_mailing_list')<li class="nav-item"><a href="{{ url('mailing_list') }}" class="nav-link">Mailing List</a></li>@endcan
            </ul>
        </li>
    </ul>
</div>
