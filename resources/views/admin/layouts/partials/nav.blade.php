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
                            <li aria-haspopup="true"><a href="#" class="sub-icon"><i
                                        class=""></i>KPIs<i class="fe fe-chevron-down horizontal-icon"></i></a>
                                <ul class="sub-menu">
                                    <li aria-haspopup="true"><a href="{{ route('admin.rccdashboards') }}" class="slide-item">{{ __('admin_nav.rcc_dashboard') }}</a></li>
                                    <li aria-haspopup="true"><a href="{{ url('admin/kpi') }}" class="slide-item">{{ __('admin_nav.manage_indicators') }}</a></li>
                                    <li aria-haspopup="true"><a href="{{ url('admin/kpi/subject-areas') }}" class="slide-item">{{ __('admin_nav.subject_areas') }}</a></li>
                                    <li aria-haspopup="true"><a href="{{ url('admin/kpi/data') }}" class="slide-item">{{ __('admin_nav.country_values') }}</a></li>
                                </ul>
                            </li>
                        @endcan
                    @endif
                @endcan

                </li>

                @can('view_publications')
                    @php
                        $publishMenuPendingNav = (int)($pending_publications_count ?? 0) + (int)($pending_content_requests_count ?? 0);
                    @endphp
                    <li aria-haspopup="true">
                        <a href="#" class="sub-icon" style="position: relative;">
                            <i class=""></i>Publish
                            @if($publishMenuPendingNav > 0)
                                <span class="badge badge-danger badge-pill" style="position: absolute; top: 0px; right: -8px; min-width: 18px; height: 18px; font-size: 0.7rem; padding: 2px 5px;">{{ $publishMenuPendingNav > 99 ? '99+' : $publishMenuPendingNav }}</span>
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
                            <li aria-haspopup="true">
                                <a href="{{ route('admin.content-requests.index') }}" class="slide-item" style="position: relative; display: inline-block; width: 100%;">
                                    <i class="fa fa-bell mr-1 {{ (isset($pending_content_requests_count) && $pending_content_requests_count > 0) ? 'text-warning' : '' }}"></i>Content Requests
                                    @if(isset($pending_content_requests_count) && $pending_content_requests_count > 0)
                                        <span class="badge badge-danger badge-pill" style="position: absolute; top: 50%; right: 10px; transform: translateY(-50%); min-width: 18px; height: 18px; font-size: 0.7rem; padding: 2px 5px;">{{ $pending_content_requests_count > 99 ? '99+' : $pending_content_requests_count }}</span>
                                    @endif
                                </a>
                            </li>
                            <li aria-haspopup="true">
                                <a href="{{ route('admin.content-requests.index', ['status' => 'processed']) }}" class="slide-item" style="position: relative; display: inline-block; width: 100%;">
                                    <i class="fa fa-check-circle mr-1 text-success"></i>Processed Content Requests
                                    @if(isset($processed_content_requests_count) && $processed_content_requests_count > 0)
                                        <span class="badge badge-success badge-pill" style="position: absolute; top: 50%; right: 10px; transform: translateY(-50%); min-width: 18px; height: 18px; font-size: 0.7rem; padding: 2px 5px;">{{ $processed_content_requests_count > 99 ? '99+' : $processed_content_requests_count }}</span>
                                    @endif
                                </a>
                            </li>
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
                                    Pending approval
                                    @if(isset($pending_forums_count) && $pending_forums_count > 0)
                                        <span class="badge badge-danger badge-pill" style="position: absolute; top: 50%; right: 10px; transform: translateY(-50%); min-width: 18px; height: 18px; font-size: 0.7rem; padding: 2px 5px;">{{ $pending_forums_count }}</span>
                                    @endif
                                </a>
                            </li>
                            <li aria-haspopup="true">
                                <a href="{{ url('admin/forums/approved') }}" class="slide-item">Approved forums</a>
                            </li>
                            <li aria-haspopup="true">
                                <a href="{{ url('admin/forums/rejected') }}" class="slide-item">Rejected forums</a>
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
                    <li aria-haspopup="true"><a href="#" class="sub-icon"><i class=""></i>{{ __('admin_nav.learning') }}<i
                                class="fe fe-chevron-down horizontal-icon"></i></a>
                        <ul class="sub-menu">
                            <li aria-haspopup="true"><a href="{{ route('admin.courses.index') }}" class="slide-item">{{ __('admin_nav.courses') }}</a></li>
                            <li aria-haspopup="true"><a href="{{ route('admin.courses.integrations') }}" class="slide-item">{{ __('admin_nav.platform_integrations') }}</a></li>
                            @can('view_events')
                            <li aria-haspopup="true"><a href="{{ url('admin/events') }}" class="slide-item">{{ __('admin_nav.events') }}</a></li>
                            @endcan
                        </ul>
                    </li>
                @endcan


            @can('view_forumns')
                <li aria-haspopup="true">
                    <a href="#" class="sub-icon" style="position: relative;">
                        <i class=""></i>COPs
                        @if(isset($pending_cop_approvals_count) && $pending_cop_approvals_count > 0)
                            <span class="badge badge-danger badge-pill" style="position: absolute; top: 0px; right: -8px; min-width: 18px; height: 18px; font-size: 0.7rem; padding: 2px 5px; background:#dc3545!important;color:#fff!important;">{{ $pending_cop_approvals_count }}</span>
                        @endif
                        <i class="fe fe-chevron-down horizontal-icon"></i>
                    </a>
                    <ul class="sub-menu">
                        <li aria-haspopup="true"><a href="{{ url('admin/commsofpractice') }}" class="slide-item">Manage COPs</a></li>
                        <li aria-haspopup="true"><a href="{{ route('admin.commsofpractice.participants') }}" class="slide-item">COP Participants Directory</a></li>
                    </ul>
                </li>
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
                </ul>
            </li>

            <li aria-haspopup="true"><a href="#" class="sub-icon"><i class=""></i>Settings <i
                        class="fe fe-chevron-down horizontal-icon"></i></a>
                <ul class="sub-menu">
                    <li class=""><a href="{{ route('admin.storage.index') }}" class="">{{ __('admin_nav.storage_management') }}</a></li>
                    <li class=""><a href="{{ route('admin.maps.index') }}" class="">{{ __('admin_nav.maps_management') }}</a></li>
                    <li class=""><a href="{{ route('admin.federation.index') }}" class="">{{ __('admin_nav.federated_hubs') }}</a></li>
                    <li class=""><a href="{{ url('admin/configure') }}" class="">{{ __('admin_nav.system_configurations') }}</a></li>
                    <li class=""><a href="{{ route('admin.site-languages.index') }}" class="">{{ __('admin_nav.site_languages') }}</a></li>
                    <li class=""><a href="{{ route('admin.language-management.index') }}" class="">{{ __('admin_nav.language_management') }}</a></li>
                    <li class=""><a href="{{ url('admin/accessgroups') }}" class="">{{ __('admin_nav.content_access_groups') }}</a></li>
                    <li class=""><a href="{{ url('permissions/users') }}" class="">{{ __('admin_nav.manage_users') }}</a></li>
                    <li class=""><a href="{{ url('permissions/roles') }}" class="">{{ __('admin_nav.roles') }}</a></li>
                    <li class=""><a href="{{ url('permissions') }}" class="">{{ __('admin_nav.permissions') }}</a></li>
                    <li class=""><a href="{{ url('admin/logs/user') }}" class="">{{ __('admin_nav.user_logs') }}</a></li>
                    <li class=""><a href="{{ url('admin/logs/access') }}" class="">{{ __('admin_nav.site_access_logs') }}</a>
                    </li>
                    <li class=""><a href="{{ url('admin/metrics') }}" class="">{{ __('admin_nav.system_metrics') }}</a></li>
                    @can('view_mailing_list')
                        <li class=""><a href="{{ url('mailing_list') }}" class="">{{ __('admin_nav.mailing_list') }}</a></li>
                    @endcan
                </ul>
            </li>
            </ul>
        </nav>
        <!--Nav-->
    </div>
</div>
<!--Horizontal-main -->
