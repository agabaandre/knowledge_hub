<div class="horizontal-main hor-menu clearfix side-header">
	<div class="horizontal-mainwrapper container clearfix">
		<!--Nav-->
		<nav class="horizontalMenu clearfix">
			<ul class="horizontalMenu-list">
				@can('view_dashboard')
					<li aria-haspopup="true"><a href="{{ url('dashboard') }}" class="sub-icon"><i class=""></i>Main Dashboard <i class="fe fe-chevron-down horizontal-icon"></i></a>
					</li>
				@endcan

				@can('view_rcc_dashboard')
					
				<li aria-haspopup="true"><a href="{{ url('rccdashboards') }}" class="sub-icon"><i class=""></i>RCC Dashboard<i class="fe fe-chevron-down horizontal-icon"></i></a>
				@endcan

				</li>

				@can('view_publications')
				<li aria-haspopup="true"><a href="#" class="sub-icon"><i class=""></i>Publish Resource<i class="fe fe-chevron-down horizontal-icon"></i></a>
					<ul class="sub-menu">
						<li aria-haspopup="true"><a href="{{ url('admin/publications/create') }}" class="slide-item"> Create New</a></li>
						<li aria-haspopup="true"><a href="{{ url('admin/publications') }}" class="slide-item">Publications</a></li>
						<li aria-haspopup="true"><a href="{{ url('admin/publications/moderate') }}" class="slide-item">Moderate Publications</a></li>
					</ul>
				</li>
				@endcan

				@can('view_forumns')
				<li aria-haspopup="true"><a href="#" class="sub-icon"><i class=""></i>Forums<i class="fe fe-chevron-down horizontal-icon"></i></a>
					<ul class="sub-menu">
						<li aria-haspopup="true"><a href="{{ url('forums/admin') }}" class="slide-item">Forums</a></li>
						<li aria-haspopup="true"><a href="{{ url('forums/moderate') }}" class="slide-item">Moderate Forums</a></li>
					</ul>
				</li>
				@endcan

				@can('view_performance')
				<li aria-haspopup="true"><a href="#" class="sub-icon"><i class=""></i>Performance<i class="fe fe-chevron-down horizontal-icon"></i></a>
					<ul class="sub-menu">
						<li aria-haspopup="true"><a href="{{ url('kpi/create') }}" class="slide-item">Add Indicator</a></li>
						<li aria-haspopup="true"><a href="{{ url('kpi/data') }}" class="slide-item">Inidicator Data</a></li>
					</ul>
				</li>
				@endcan
				
				<li aria-haspopup="true"><a href="#" class="sub-icon"><i class=""></i>Dropdown Lists<i class="fe fe-chevron-down horizontal-icon"></i></a>
					<ul class="sub-menu">
						@can('view_file_types')
						<li aria-haspopup="true"><a href="{{ url('filetypes') }}">Resource and Asset Types</a></li>
						@endcan
						
						@can('view_sources')
						<li aria-haspopup="true"><a href=" {{ url('authors') }}">Data Sources/Authors</a></li>
						@endcan

						@can('view_themes')
						<li aria-haspopup="true"><a href="{{ url('healththemes') }}">Security Themes</a></li>
						@endcan
						@can('view_sub_themes')
						<li aria-haspopup="true"><a href="{{ url('subthemes') }}">Security Sub-Themes</a></li>
						@endcan

						@can('view_geo_coverage')
						<li aria-haspopup="true"><a href=" {{ url('geoareas') }}">Geographical Coverage</a></li>
						@endcan

						<!-- File Types -->
						@can('view_file_types')
						<li aria-haspopup="true"><a href="{{ url('filetypes') }}">File Types</a></li>
						@endcan

						@can('view_tags')
						<li aria-haspopup="true"><a href="{{ url('tags') }}">Search Tags</a></li>
						@endcan

						@can('view_quotes')
						<li aria-haspopup="true"><a href="{{ url('quotes') }}">Quotes</a></li>
						@endcan

						@can('view_quize')
						<li aria-haspopup="true"><a href="{{ url('quize') }}">Quiz</a></li>
						@endcan

						<!-- Privacy Policy -->
						@can('view_asset_types')
						<li aria-haspopup="true"><a href="{{ url('assettypes') }}">Health Asset Types</a></li>
						@endcan

						@can('view_assets')
						<li aria-haspopup="true"><a href="{{ url('healthassets/admin') }}">Health Assets</a></li>
						@endcan

						@can('experts')
						<li aria-haspopup="true"><a href="{{ url('experts/admin') }}">Health Experts</a></li>
						@endcan
						
						@can('view_privacy_policy')
						<li aria-haspopup="true"><a href="{{ url('privacy_policy') }}">Privacy Policy</a></li>
						@endcan
					</ul>
				</li>

				<li aria-haspopup="true"><a href="#" class="sub-icon"><i class=""></i>Settings <i class="fe fe-chevron-down horizontal-icon"></i></a>
					<ul class="sub-menu">

						<li class=""><a href="{{ url('permissions/users') }}" class="">Manage Users</a></li>
						<li class=""><a href="{{ url('permissions/roles') }}" class="">Roles</a></li>
						<li class=""><a href="{{ url('permissions') }}" class="">Permissions</a></li>
						<li class=""><a href="{{ url('auth/logs') }}" class="">User Logs</a></li>
						<li class=""><a href="{{ url('constants') }}" class="">Constants</a></li>
						
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