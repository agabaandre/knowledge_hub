<div class="horizontal-main hor-menu clearfix side-header">
    <div class="horizontal-mainwrapper container clearfix">
        <!--Nav-->
        <nav class="horizontalMenu clearfix">
            <ul class="horizontalMenu-list">
                @can('view_dashboard')
                    <li aria-haspopup="true"><a href="#" class="sub-icon">
                            <i class=""></i> Dashboards <i class="fe fe-chevron-down horizontal-icon"></i>
                        </a>
                        <ul class="sub-menu">
                            @foreach ($dashboards as $dashboard)
                                <li aria-haspopup="true">
                                    <a href="{{ !$dashboard->is_embedded ? url('/admin/dashboards') . '?resource=' . $dashboard->id : url($dashboard->publication) }}"
                                        class="slide-item">{{ $dashboard->title }}</a>
                                </li>
                            @endforeach
                        </ul>
                    </li>
                @endcan

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
                    <li aria-haspopup="true"><a href="#" class="sub-icon"><i class=""></i>Publish<i
                                class="fe fe-chevron-down horizontal-icon"></i></a>
                        <ul class="sub-menu">
                            <li aria-haspopup="true"><a href="{{ url('admin/publications/create') }}"
                                    class="slide-item">Publish a Resource</a></li>
                            <li aria-haspopup="true"><a href="{{ url('admin/publications') }}" class="slide-item">Manage
                                    Resources</a></li>
                            <li aria-haspopup="true"><a href="{{ url('admin/publications/summaries') }}"
                                    class="slide-item">Resource Sumaries & Abstracts</a></li>
                            <li aria-haspopup="true"><a href="{{ url('admin/publications/moderate') }}"
                                    class="slide-item">Moderate Comments</a></li>
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
                    <li aria-haspopup="true"><a href="#" class="sub-icon"><i class=""></i>Forums<i
                                class="fe fe-chevron-down horizontal-icon"></i></a>
                        <ul class="sub-menu">
                            <li aria-haspopup="true"><a href="{{ url('admin/forums') }}" class="slide-item">Forums</a></li>
                            <li aria-haspopup="true"><a href="{{ url('admin/forums/moderate') }}"
                                    class="slide-item">Moderate Forums</a></li>
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
                <li aria-haspopup="true"><a href="{{ url('admin/commsofpractice') }}" class="sub-icon"><i
                            class=""></i>COPs</a>
                </li>
            @endcan

            <li aria-haspopup="true"><a href="{{ url('admin/events') }}" class="sub-icon"><i
                        class=""></i>Events</a>
            </li>

            <li aria-haspopup="true"><a href="#" class="sub-icon"><i class=""></i>Dropdown Lists<i
                        class="fe fe-chevron-down horizontal-icon"></i></a>
                <ul class="sub-menu">
                    @can('view_file_types')
                        <li aria-haspopup="true"><a href="{{ url('admin/filetypes') }}">Resource and Asset Types</a></li>
                    @endcan

                    <li class=""><a href="{{ url('admin/tools') }}" class="">Tools</a></li>

                    @can('view_sources')
                        <li aria-haspopup="true"><a href=" {{ url('admin/authors') }}">Data Sources</a></li>
                        <li aria-haspopup="true"><a href="{{ url('admin/datarecords/categories') }}">Categories</a></li>
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

                    @can('view_privacy_policy')
                        <li aria-haspopup="true"><a href="{{ url('admin/privacy') }}">Privacy Policy</a></li>
                    @endcan
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
