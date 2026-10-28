<div class="horizontal-main hor-menu clearfix side-header">
    <div class="horizontal-mainwrapper container clearfix">
        <!--Nav-->
        <nav class="horizontalMenu clearfix">
            <ul class="horizontalMenu-list">
                    <li aria-haspopup="true"><a href="{{ url('admin/dashboard') }}" class="sub-icon">
                            <i class=""></i> {{ __('admin_nav.dashboard') }} <i class="fe fe-chevron-down horizontal-icon"></i>
                        </a>
                        <ul class="sub-menu">
                            <li aria-haspopup="true">
                                <a href="{{ url('admin/dashboard') }}" class="slide-item">{{ __('admin_nav.overview') }}</a>
                            </li>
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
                                           class="slide-item">{{ \App\Support\UiLocaleLabels::adminDashboardPublicationLabel($dashboard) }}</a>
                                    </li>
                                @endforeach
                            @endif
                            <li aria-haspopup="true">
                                <a href="{{ url('admin/dashboard/list') }}" class="slide-item">{{ __('admin_nav.view_all_dashboards') }}</a>
                            </li>
                        </ul>
                    </li>

                    <li aria-haspopup="true">
                        <a href="{{ route('admin.approvals.index') }}" class="sub-icon" style="position: relative;">
                            {{ __('admin_nav.approvals') }}
                            @php $approvalsBadgeNav = (int)($pending_publications_count ?? 0) + (int)($pending_forums_count ?? 0) + (int)($pending_cop_approvals_count ?? 0) + (int)($pending_federated_content_count ?? 0); @endphp
                            @if($approvalsBadgeNav > 0)
                                <span class="badge badge-danger badge-pill" style="position: absolute; top: 0px; right: -8px; min-width: 18px; height: 18px; font-size: 0.7rem; padding: 2px 5px;">{{ $approvalsBadgeNav > 99 ? '99+' : $approvalsBadgeNav }}</span>
                            @endif
                        </a>
                    </li>

                @can('view_rcc_dashboard')
                    @if (states_enabled())
                        @can('view_performance')
                            <li aria-haspopup="true"><a href="#" class="sub-icon"><i
                                        class=""></i>{{ __('admin_nav.kpis') }}<i class="fe fe-chevron-down horizontal-icon"></i></a>
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
                            <i class=""></i>{{ __('admin_nav.publish') }}
                            @if($publishMenuPendingNav > 0)
                                <span class="badge badge-danger badge-pill" style="position: absolute; top: 0px; right: -8px; min-width: 18px; height: 18px; font-size: 0.7rem; padding: 2px 5px;">{{ $publishMenuPendingNav > 99 ? '99+' : $publishMenuPendingNav }}</span>
                            @endif
                            <i class="fe fe-chevron-down horizontal-icon"></i>
                        </a>
                        <ul class="sub-menu">
                            <li aria-haspopup="true"><a href="{{ url('admin/publications/create') }}"
                                    class="slide-item">{{ __('admin_nav.publish_a_resource') }}</a></li>
                                <li aria-haspopup="true"><a href="{{ url('admin/publications') }}" class="slide-item">{{ __('admin_nav.manage_resources') }}</a></li>
                            <li aria-haspopup="true">
                                <a href="{{ url('admin/publications/pending') }}" class="slide-item" style="position: relative; display: inline-block; width: 100%;">
                                    {{ __('admin_nav.pending_approval') }}
                                    @if(isset($pending_publications_count) && $pending_publications_count > 0)
                                        <span class="badge badge-danger badge-pill" style="position: absolute; top: 50%; right: 10px; transform: translateY(-50%); min-width: 18px; height: 18px; font-size: 0.7rem; padding: 2px 5px;">{{ $pending_publications_count }}</span>
                                    @endif
                                </a>
                            </li>
                            <li aria-haspopup="true">
                                <a href="{{ url('admin/publications/rejected') }}" class="slide-item" style="position: relative; display: inline-block; width: 100%;">
                                    {{ __('admin_nav.rejected_resources') }}
                                    @if(isset($rejected_publications_count) && $rejected_publications_count > 0)
                                        <span class="badge badge-secondary badge-pill" style="position: absolute; top: 50%; right: 10px; transform: translateY(-50%); min-width: 18px; height: 18px; font-size: 0.7rem; padding: 2px 5px;">{{ $rejected_publications_count }}</span>
                                    @endif
                                </a>
                            </li>
                            <li aria-haspopup="true"><a href="{{ url('admin/publications/summaries') }}"
                                    class="slide-item">{{ __('admin_nav.summaries_abstracts') }}</a></li>
                            <li aria-haspopup="true">
                                <a href="{{ url('admin/publications/moderate') }}" class="slide-item" style="position: relative; display: inline-block; width: 100%;">
                                    {{ __('admin_nav.moderate_comments') }}
                                    @if(isset($pending_publication_comments_count) && $pending_publication_comments_count > 0)
                                        <span class="badge badge-danger badge-pill" style="position: absolute; top: 50%; right: 10px; transform: translateY(-50%); min-width: 18px; height: 18px; font-size: 0.7rem; padding: 2px 5px;">{{ $pending_publication_comments_count }}</span>
                                    @endif
                                </a>
                            </li>
                            <li aria-haspopup="true"><a href="{{ route('admin.rss_feeds.index') }}" class="slide-item">{{ __('admin_nav.rss_feeds') }}</a></li>
                            <li aria-haspopup="true"><a href="{{ route('admin.rss_staging.index') }}" class="slide-item">{{ __('admin_nav.rss_staging') }}</a></li>
                            <li aria-haspopup="true"><a href="{{ route('admin.participant-badges.index') }}" class="slide-item">{{ __('admin_nav.participant_badge_management') }}</a></li>
                            @can('view_content_requests')
                            <li aria-haspopup="true">
                                <a href="{{ route('admin.content-requests.index') }}" class="slide-item" style="position: relative; display: inline-block; width: 100%;">
                                    <i class="fa fa-bell mr-1 {{ (isset($pending_content_requests_count) && $pending_content_requests_count > 0) ? 'text-warning' : '' }}"></i>{{ __('admin_nav.content_requests') }}
                                    @if(isset($pending_content_requests_count) && $pending_content_requests_count > 0)
                                        <span class="badge badge-danger badge-pill" style="position: absolute; top: 50%; right: 10px; transform: translateY(-50%); min-width: 18px; height: 18px; font-size: 0.7rem; padding: 2px 5px;">{{ $pending_content_requests_count > 99 ? '99+' : $pending_content_requests_count }}</span>
                                    @endif
                                </a>
                            </li>
                            <li aria-haspopup="true">
                                <a href="{{ route('admin.content-requests.index', ['status' => 'processed']) }}" class="slide-item" style="position: relative; display: inline-block; width: 100%;">
                                    <i class="fa fa-check-circle mr-1 text-success"></i>{{ __('admin_nav.processed_content_requests') }}
                                    @if(isset($processed_content_requests_count) && $processed_content_requests_count > 0)
                                        <span class="badge badge-success badge-pill" style="position: absolute; top: 50%; right: 10px; transform: translateY(-50%); min-width: 18px; height: 18px; font-size: 0.7rem; padding: 2px 5px;">{{ $processed_content_requests_count > 99 ? '99+' : $processed_content_requests_count }}</span>
                                    @endif
                                </a>
                            </li>
                            @endcan
                            @can('manage_experts')
                                <li aria-haspopup="true"><a href="{{ url('admin/experts') }}">{{ __('admin_nav.roster_of_experts') }}</a></li>
                            @endcan

                            {{-- <li aria-haspopup="true"><a href="{{ url('admin/datarecords') }}" class="slide-item">Categories Data</a></li> --}}
                            {{-- <li aria-haspopup="true"><a href="{{ url('admin/datarecords/create') }}" class="slide-item">Publish Category Data </a></li> --}}
                            @can('manage_facts')
                                <li aria-haspopup="true"><a href="{{ url('admin/facts') }}">{{ __('admin_nav.facts') }}</a></li>
                            @endcan

                            @can('view_quotes')
                                <li aria-haspopup="true"><a href="{{ url('admin/quotes') }}">{{ __('admin_nav.quotes') }}</a></li>
                            @endcan

                            @can('view_quize')
                                <li aria-haspopup="true"><a href="{{ url('admin/quiz') }}">{{ __('admin_nav.quiz') }}</a></li>
                            @endcan

                        </ul>
                    </li>
                @endcan

                @can('view_forumns')
                    <li aria-haspopup="true"><a href="#" class="sub-icon"><i class=""></i>{{ __('admin_nav.messaging') }}<i
                                class="fe fe-envelope horizontal-icon"></i></a>
                        <ul class="sub-menu">
                            <li aria-haspopup="true"><a href="{{ url('admin/messaging') }}" class="slide-item">{{ __('admin_nav.send_app_push_notifications') }}</a></li>

                        </ul>
                    </li>
                @endcan

                @can('view_forumns')
                    <li aria-haspopup="true">
                        <a href="#" class="sub-icon" style="position: relative;">
                            <i class=""></i>{{ __('admin_nav.forums') }}
                            @if(isset($pending_forums_count) && $pending_forums_count > 0)
                                <span class="badge badge-danger badge-pill" style="position: absolute; top: 0px; right: -8px; min-width: 18px; height: 18px; font-size: 0.7rem; padding: 2px 5px;">{{ $pending_forums_count }}</span>
                            @endif
                            <i class="fe fe-chevron-down horizontal-icon"></i>
                        </a>
                        <ul class="sub-menu">
                            <li aria-haspopup="true">
                                <a href="{{ url('admin/forums') }}" class="slide-item" style="position: relative; display: inline-block; width: 100%;">
                                    {{ __('admin_nav.pending_approval_forums') }}
                                    @if(isset($pending_forums_count) && $pending_forums_count > 0)
                                        <span class="badge badge-danger badge-pill" style="position: absolute; top: 50%; right: 10px; transform: translateY(-50%); min-width: 18px; height: 18px; font-size: 0.7rem; padding: 2px 5px;">{{ $pending_forums_count }}</span>
                                    @endif
                                </a>
                            </li>
                            <li aria-haspopup="true">
                                <a href="{{ url('admin/forums/approved') }}" class="slide-item">{{ __('admin_nav.approved_forums') }}</a>
                            </li>
                            <li aria-haspopup="true">
                                <a href="{{ url('admin/forums/rejected') }}" class="slide-item">{{ __('admin_nav.rejected_forums') }}</a>
                            </li>
                            <li aria-haspopup="true">
                                <a href="{{ url('admin/forums/moderate') }}" class="slide-item" style="position: relative; display: inline-block; width: 100%;">
                                    {{ __('admin_nav.moderate_forums') }}
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
                            <li aria-haspopup="true"><a href="{{ route('admin.courses.ai-config') }}" class="slide-item">{{ __('admin_nav.ai_config') }}</a></li>
                            <li aria-haspopup="true"><a href="{{ route('admin.courses.sitemap') }}" class="slide-item">{{ __('admin_nav.sitemap') }}</a></li>
                            @can('view_events')
                            <li aria-haspopup="true"><a href="{{ url('admin/events') }}" class="slide-item">{{ __('admin_nav.events') }}</a></li>
                            @endcan
                        </ul>
                    </li>
                @endcan


            @can('view_forumns')
                <li aria-haspopup="true">
                    <a href="#" class="sub-icon" style="position: relative;">
                        <i class=""></i>{{ __('admin_nav.cops') }}
                        @if(isset($pending_cop_approvals_count) && $pending_cop_approvals_count > 0)
                            <span class="badge badge-danger badge-pill" style="position: absolute; top: 0px; right: -8px; min-width: 18px; height: 18px; font-size: 0.7rem; padding: 2px 5px; background:#dc3545!important;color:#fff!important;">{{ $pending_cop_approvals_count }}</span>
                        @endif
                        <i class="fe fe-chevron-down horizontal-icon"></i>
                    </a>
                    <ul class="sub-menu">
                        <li aria-haspopup="true"><a href="{{ url('admin/commsofpractice') }}" class="slide-item">{{ __('admin_nav.manage_cops') }}</a></li>
                        <li aria-haspopup="true">
                            <a href="{{ route('admin.commsofpractice.participants') }}" class="slide-item">
                                {{ __('admin_nav.cop_participants_directory') }}
                                @if(isset($pending_cop_approvals_count) && $pending_cop_approvals_count > 0)
                                    <span class="badge badge-danger badge-pill ms-1" style="background:#dc3545!important;color:#fff!important;">{{ $pending_cop_approvals_count }}</span>
                                @endif
                            </a>
                        </li>
                    </ul>
                </li>
            @endcan

            <li aria-haspopup="true"><a href="#" class="sub-icon"><i class=""></i>{{ __('admin_nav.dropdown_lists') }}<i
                        class="fe fe-chevron-down horizontal-icon"></i></a>
                <ul class="sub-menu">
                    @can('view_file_types')
                        <li aria-haspopup="true"><a href="{{ url('admin/filetypes') }}">{{ __('admin_nav.resource_and_asset_types') }}</a></li>
                    @endcan

                    @can('view_file_types')
                        <li class=""><a href="{{ url('admin/tools') }}" class="">{{ __('admin_nav.tools') }}</a></li>
                    @endcan

                    @can('view_sources')
                        <li aria-haspopup="true"><a href=" {{ url('admin/authors') }}">{{ __('admin_nav.data_sources') }}</a></li>
                        <li aria-haspopup="true"><a href="{{ url('admin/datarecords/categories') }}">{{ __('admin_nav.categories') }}</a></li>
                        <li aria-haspopup="true"><a href="{{ route('admin.subcategories.index') }}">{{ __('admin_nav.sub_categories') }}</a></li>
                    @endcan

                    @can('view_themes')
                        <li aria-haspopup="true"><a href="{{ url('admin/themes') }}">{{ __('admin_nav.security_themes') }}</a></li>
                    @endcan

                    @can('view_sub_themes')
                        <li aria-haspopup="true"><a href="{{ url('admin/subthemes') }}">{{ __('admin_nav.security_sub_themes') }}</a></li>
                    @endcan

                    @can('view_faqs')
                        <li aria-haspopup="true"><a href="{{ url('admin/faqs') }}">{{ __('admin_nav.faqs') }}</a></li>
                    @endcan

                    @can('view_geo_coverage')
                        @if (states_enabled())
                            <li aria-haspopup="true"><a href=" {{ url('admin/areas') }}">{{ __('admin_nav.geographical_coverage') }}</a></li>
                        @else
                            <li aria-haspopup="true"><a href=" {{ url('admin/adminunits') }}">{{ __('frontend_nav.administrative_units') }}</a>
                            </li>
                        @endif
                    @endcan

                    <!-- Privacy Policy -->
                    @can('view_asset_types')
                        <li aria-haspopup="true"><a href="{{ url('admin/assettypes') }}">{{ __('admin_nav.health_asset_types') }}</a></li>
                    @endcan

                    @can('view_assets')
                        <li aria-haspopup="true"><a href="{{ url('admin/healthassets') }}">{{ __('admin_nav.health_assets') }}</a></li>
                    @endcan

                    @can('manage_experts')
                        <li aria-haspopup="true"><a href="{{ url('admin/experts/types') }}">{{ __('admin_nav.workforce_types') }}</a></li>
                    @endcan

                    @can('view_tags')
                        <li aria-haspopup="true"><a href="{{ url('admin/tags') }}">{{ __('admin_nav.tags') }}</a></li>
                    @endcan

                    @can('manage_experts')
                        <li aria-haspopup="true"><a href="{{ url('admin/licenses') }}">{{ __('admin_nav.licenses') }}</a></li>
                    @endcan

                    <li aria-haspopup="true"><a href="{{ url('admin/static-links') }}">{{ __('admin_nav.static_links') }}</a></li>

                    @can('view_privacy_policy')
                        <li aria-haspopup="true"><a href="{{ url('admin/privacy') }}">{{ __('admin_nav.privacy_policy') }}</a></li>
                    @endcan
                </ul>
            </li>

            <li aria-haspopup="true"><a href="#" class="sub-icon"><i class=""></i>{{ __('admin_nav.settings') }} <i
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
