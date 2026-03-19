<div class="horizontal-main hor-menu clearfix side-header">
    <div class="horizontal-mainwrapper container clearfix">
        <!--Nav-->
        <nav class="horizontalMenu clearfix">
            <ul class="horizontalMenu-list">
                    <li aria-haspopup="true"><a href="{{ url('admin/dashboard') }}" class="sub-icon">
                            <i class=""></i> Dashboard <i class="fe fe-chevron-down horizontal-icon"></i>
                        </a>
                        <ul class="sub-menu">
                            @if(isset($dashboards) && $dashboards->count() > 0)
                                @foreach ($dashboards as $dashboard)
                                    @php
                                        // Use publication link directly if is_embedded != 1, otherwise use admin view
                                        $dashboard_url = ($dashboard->is_embedded == 1) 
                                            ? url('/admin/dashboards') . '?resource=' . $dashboard->id 
                                            : ($dashboard->publication ?? url('/admin/dashboards') . '?resource=' . $dashboard->id);
                                        
                                        // Check if external URL
                                        $app_url = config('app.url');
                                        $url_parsed = parse_url($dashboard_url);
                                        $app_url_parsed = parse_url($app_url);
                                        $is_external_link = isset($url_parsed['host']) && 
                                                           isset($app_url_parsed['host']) && 
                                                           $url_parsed['host'] !== $app_url_parsed['host'];
                                    @endphp
                                    <li aria-haspopup="true">
                                        <a href="{{ $dashboard_url }}" 
                                           target="{{ $is_external_link ? '_blank' : '_self' }}"
                                           class="slide-item">{{ $dashboard->title }}</a>
                                    </li>
                                @endforeach
                            @else
                                <li aria-haspopup="true">
                                    <a href="{{ url('admin/dashboard') }}" class="slide-item">View All Dashboards</a>
                                </li>
                            @endif
                        </ul>
                    </li>

                @can('view_rcc_dashboard')
                    @if (states_enabled())
                        @can('view_performance')
                            <li aria-haspopup="true"><a href="{{ url('admin/rccdashboards') }}" class="sub-icon"><i
                                        class=""></i>KPIs<i class="fe fe-chevron-down horizontal-icon"></i></a>
                                <ul class="sub-menu">
                                    <li aria-haspopup="true"><a href="{{ url('admin/kpi') }}" class="slide-item">Add
                                            Indicator</a></li>
                                    <li aria-haspopup="true"><a href="{{ url('admin/kpi/data') }}" class="slide-item">Inidicator
                                            Data</a></li>
                                </ul>
                            </li>
                        @endcan
                    @endif
                @endcan

                </li>

                @can('view_publications')
                    <li aria-haspopup="true">
                        <a href="#" class="sub-icon" style="position: relative;">
                            <i class=""></i>Publish
                            @if(isset($pending_publications_count) && $pending_publications_count > 0)
                                <span class="badge badge-danger badge-pill" style="position: absolute; top: 0px; right: -8px; min-width: 18px; height: 18px; font-size: 0.7rem; padding: 2px 5px;">{{ $pending_publications_count }}</span>
                            @endif
                            <i class="fe fe-chevron-down horizontal-icon"></i>
                        </a>
                        <ul class="sub-menu">
                            <li aria-haspopup="true"><a href="{{ url('admin/publications/create') }}"
                                    class="slide-item">Publish a Resource</a></li>
                                <li aria-haspopup="true"><a href="{{ url('admin/publications') }}" class="slide-item">Manage
                                        Resources</a></li>
                            <li aria-haspopup="true">
                                <a href="{{ url('admin/publications/pending') }}" class="slide-item" style="position: relative; display: inline-block; width: 100%;">
                                    Resources Pending Approval
                                    @if(isset($pending_publications_count) && $pending_publications_count > 0)
                                        <span class="badge badge-danger badge-pill" style="position: absolute; top: 50%; right: 10px; transform: translateY(-50%); min-width: 18px; height: 18px; font-size: 0.7rem; padding: 2px 5px;">{{ $pending_publications_count }}</span>
                                    @endif
                                </a>
                            </li>
                            <li aria-haspopup="true"><a href="{{ url('admin/publications/summaries') }}"
                                    class="slide-item">Resource Sumaries & Abstracts</a></li>
                            <li aria-haspopup="true">
                                <a href="{{ url('admin/publications/moderate') }}" class="slide-item" style="position: relative; display: inline-block; width: 100%;">
                                    Moderate Comments
                                    @if(isset($pending_publication_comments_count) && $pending_publication_comments_count > 0)
                                        <span class="badge badge-danger badge-pill" style="position: absolute; top: 50%; right: 10px; transform: translateY(-50%); min-width: 18px; height: 18px; font-size: 0.7rem; padding: 2px 5px;">{{ $pending_publication_comments_count }}</span>
                                    @endif
                                </a>
                            </li>
                            <li aria-haspopup="true"><a href="{{ route('admin.rss_feeds.index') }}" class="slide-item">RSS Feeds</a></li>
                            <li aria-haspopup="true"><a href="{{ route('admin.rss_staging.index') }}" class="slide-item">RSS Staging</a></li>
                            <li aria-haspopup="true"><a href="{{ route('admin.participant-badges.index') }}" class="slide-item">Participant badge management</a></li>
                            @can('view_content_requests')
                            <li aria-haspopup="true"><a href="{{ route('admin.content-requests.index') }}" class="slide-item">
                                <i class="fa fa-file-alt mr-1"></i>Content Requests</a></li>
                            @endcan
                            @can('manage_experts')
                                <li aria-haspopup="true"><a href="{{ url('admin/experts') }}">Roster of Experts</a></li>
                            @endcan

                            {{-- <li aria-haspopup="true"><a href="{{ url('admin/datarecords') }}" class="slide-item">Categories Data</a></li> --}}
                            {{-- <li aria-haspopup="true"><a href="{{ url('admin/datarecords/create') }}" class="slide-item">Publish Category Data </a></li> --}}
                            @can('manage_facts')
                                <li aria-haspopup="true"><a href="{{ url('admin/facts') }}">Facts</a></li>
                            @endcan

                            @can('view_quotes')
                                <li aria-haspopu="true"><a href="{{ url('admin/quotes') }}">Quotes</a></li>
                            @endcan

                            @can('view_quize')
                                <li aria-haspopup="true"><a href="{{ url('admin/quiz') }}">Quiz</a></li>
                            @endcan

                        </ul>
                    </li>
                @endcan

                @can('view_forumns')
                    <li aria-haspopup="true"><a href="#" class="sub-icon"><i class=""></i>Messaging<i
                                class="fe fe-envelope horizontal-icon"></i></a>
                        <ul class="sub-menu">
                            <li aria-haspopup="true"><a href="{{ url('admin/messaging') }}" class="slide-item">Send App
                                    Push Notifications</a></li>

                        </ul>
                    </li>
                @endcan

                @can('view_forumns')
                    <li aria-haspopup="true">
                        <a href="#" class="sub-icon" style="position: relative;">
                            <i class=""></i>Forums
                            @if(isset($pending_forums_count) && $pending_forums_count > 0)
                                <span class="badge badge-danger badge-pill" style="position: absolute; top: 0px; right: -8px; min-width: 18px; height: 18px; font-size: 0.7rem; padding: 2px 5px;">{{ $pending_forums_count }}</span>
                            @endif
                            <i class="fe fe-chevron-down horizontal-icon"></i>
                        </a>
                        <ul class="sub-menu">
                            <li aria-haspopup="true">
                                <a href="{{ url('admin/forums') }}" class="slide-item" style="position: relative; display: inline-block; width: 100%;">
                                    Forums
                                    @if(isset($pending_forums_count) && $pending_forums_count > 0)
                                        <span class="badge badge-danger badge-pill" style="position: absolute; top: 50%; right: 10px; transform: translateY(-50%); min-width: 18px; height: 18px; font-size: 0.7rem; padding: 2px 5px;">{{ $pending_forums_count }}</span>
                                    @endif
                                </a>
                            </li>
                            <li aria-haspopup="true">
                                <a href="{{ url('admin/forums/moderate') }}" class="slide-item" style="position: relative; display: inline-block; width: 100%;">
                                    Moderate Forums
                                    @if(isset($pending_forum_comments_count) && $pending_forum_comments_count > 0)
                                        <span class="badge badge-danger badge-pill" style="position: absolute; top: 50%; right: 10px; transform: translateY(-50%); min-width: 18px; height: 18px; font-size: 0.7rem; padding: 2px 5px;">{{ $pending_forum_comments_count }}</span>
                                    @endif
                                </a>
                            </li>
                        </ul>
                    </li>
                @endcan
                @can('view_forumns')
                    <li aria-haspopup="true"><a href="#" class="sub-icon"><i class=""></i>LMS<i
                                class="fe fe-chevron-down horizontal-icon"></i></a>
                        <ul class="sub-menu">
                            <li aria-haspopup="true"><a href="{{ url('admin/courses') }}" class="slide-item">Courses</a>
                            </li>
                    </li>
                </ul>
                </li>
            @endcan


            @can('view_forumns')
                <li aria-haspopup="true">
                    <a href="{{ url('admin/commsofpractice') }}" class="sub-icon" style="position: relative;">
                        <i class=""></i>COPs
                        @if(isset($pending_cop_approvals_count) && $pending_cop_approvals_count > 0)
                            <span class="badge badge-danger badge-pill" style="position: absolute; top: 0px; right: -8px; min-width: 18px; height: 18px; font-size: 0.7rem; padding: 2px 5px; background:#dc3545!important;color:#fff!important;">{{ $pending_cop_approvals_count }}</span>
                        @endif
                    </a>
                </li>
            @endcan

            @can('view_events')
                <li aria-haspopup="true"><a href="{{ url('admin/events') }}" class="sub-icon"><i class=""></i>Events</a></li>
            @endcan

            <li aria-haspopup="true"><a href="#" class="sub-icon"><i class=""></i>Dropdown Lists<i
                        class="fe fe-chevron-down horizontal-icon"></i></a>
                <ul class="sub-menu">
                    @can('view_file_types')
                        <li aria-haspopup="true"><a href="{{ url('admin/filetypes') }}">Resource and Asset Types</a></li>
                    @endcan

                    @can('view_file_types')
                        <li class=""><a href="{{ url('admin/tools') }}" class="">Tools</a></li>
                    @endcan

                    @can('view_sources')
                        <li aria-haspopup="true"><a href=" {{ url('admin/authors') }}">Data Sources</a></li>
                        <li aria-haspopup="true"><a href="{{ url('admin/datarecords/categories') }}">Categories</a></li>
                        <li aria-haspopup="true"><a href="{{ route('admin.subcategories.index') }}">Sub Categories</a></li>
                    @endcan

                    @can('view_themes')
                        <li aria-haspopup="true"><a href="{{ url('admin/themes') }}">Security Themes</a></li>
                    @endcan

                    @can('view_sub_themes')
                        <li aria-haspopup="true"><a href="{{ url('admin/subthemes') }}">Security Sub-Themes</a></li>
                    @endcan

                    @can('view_faqs')
                        <li aria-haspopup="true"><a href="{{ url('admin/faqs') }}">FAQs</a></li>
                    @endcan

                    @can('view_geo_coverage')
                        @if (states_enabled())
                            <li aria-haspopup="true"><a href=" {{ url('admin/areas') }}">Geographical Coverage</a></li>
                        @else
                            <li aria-haspopup="true"><a href=" {{ url('admin/adminunits') }}">Administrative Units</a>
                            </li>
                        @endif
                    @endcan

                    <!-- Privacy Policy -->
                    @can('view_asset_types')
                        <li aria-haspopup="true"><a href="{{ url('admin/assettypes') }}">Health Asset Types</a></li>
                    @endcan

                    @can('view_assets')
                        <li aria-haspopup="true"><a href="{{ url('admin/healthassets') }}">Health Assets</a></li>
                    @endcan

                    @can('manage_experts')
                        <li aria-haspopup="true"><a href="{{ url('admin/experts/types') }}">Workforce Types</a></li>
                    @endcan

                    @can('view_tags')
                        <li aria-haspopup="true"><a href="{{ url('admin/tags') }}">Tags</a></li>
                    @endcan

                    @can('manage_experts')
                        <li aria-haspopup="true"><a href="{{ url('admin/licenses') }}">Licenses</a></li>
                    @endcan

                    <li aria-haspopup="true"><a href="{{ url('admin/static-links') }}">Static Links</a></li>

                    @can('view_privacy_policy')
                        <li aria-haspopup="true"><a href="{{ url('admin/privacy') }}">Privacy Policy</a></li>
                    @endcan
                        <li aria-haspopup="true"><a href="{{ url('admin/events') }}">Events</a></li>
                </ul>
            </li>

            <li aria-haspopup="true"><a href="#" class="sub-icon"><i class=""></i>Settings <i
                        class="fe fe-chevron-down horizontal-icon"></i></a>
                <ul class="sub-menu">
                    <li class=""><a href="{{ url('admin/configure') }}" class="">System
                            Configurations</a></li>
                    <li class=""><a href="{{ url('admin/accessgroups') }}" class="">Content Access
                            Groups</a></li>
                    <li class=""><a href="{{ url('permissions/users') }}" class="">Manage Users</a></li>
                    <li class=""><a href="{{ url('permissions/roles') }}" class="">Roles</a></li>
                    <li class=""><a href="{{ url('permissions') }}" class="">Permissions</a></li>
                    <li class=""><a href="{{ url('admin/logs/user') }}" class="">User Logs</a></li>
                    <li class=""><a href="{{ url('admin/logs/access') }}" class="">Site Access Logs</a>
                    </li>
                    <li class=""><a href="{{ url('admin/metrics') }}" class="">System Metrics</a></li>
                    @can('view_mailing_list')
                        <li class=""><a href="{{ url('mailing_list') }}" class="">Mailing List</a></li>
                    @endcan
                </ul>
            </li>
            </ul>
        </nav>
        <!--Nav-->
    </div>
</div>
<!--Horizontal-main -->
