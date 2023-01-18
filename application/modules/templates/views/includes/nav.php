<div class="horizontal-main hor-menu clearfix side-header">
	<div class="horizontal-mainwrapper container clearfix">
		<!--Nav-->
		<nav class="horizontalMenu clearfix">
			<ul class="horizontalMenu-list">
				<?php if (can_access_multi(['view_dashboard'])) { ?>
					<li aria-haspopup="true"><a href="<?php echo base_url() ?>dashboard" class="sub-icon"><i class=""></i>Main Dashboard <i class="fe fe-chevron-down horizontal-icon"></i></a>
					</li>
				<?php } ?>

				<?php if (can_access_multi(['view_rcc_dashboard'])) { ?>
					
				<li aria-haspopup="true"><a href="<?php echo base_url() ?>rccdashboards" class="sub-icon"><i class=""></i>RCC Dashboard<i class="fe fe-chevron-down horizontal-icon"></i></a>
				<?php } ?>

				</li>

				<?php if (can_access_multi(['view_publications'])) { ?>
				<li aria-haspopup="true"><a href="#" class="sub-icon"><i class=""></i>Publish Resource<i class="fe fe-chevron-down horizontal-icon"></i></a>
					<ul class="sub-menu">
						<li aria-haspopup="true"><a href="<?php echo base_url() ?>publications/create" class="slide-item"> Create New</a></li>
						<li aria-haspopup="true"><a href="<?php echo base_url() ?>publications" class="slide-item">Publications</a></li>
						<li aria-haspopup="true"><a href="<?php echo base_url() ?>publications/moderate" class="slide-item">Moderate Publications</a></li>
					</ul>
				</li>
				<?php } ?>

				<?php if (can_access_multi(['view_forumns'])) { ?>
				<li aria-haspopup="true"><a href="#" class="sub-icon"><i class=""></i>Forums<i class="fe fe-chevron-down horizontal-icon"></i></a>
					<ul class="sub-menu">
						<li aria-haspopup="true"><a href="<?php echo base_url() ?>forums/admin" class="slide-item">Forums</a></li>
						<li aria-haspopup="true"><a href="<?php echo base_url() ?>forums/moderate" class="slide-item">Moderate Forums</a></li>
					</ul>
				</li>
				<?php } ?>

				<?php if (can_access_multi(['view_performance'])) { ?>
				<li aria-haspopup="true"><a href="#" class="sub-icon"><i class=""></i>Performance<i class="fe fe-chevron-down horizontal-icon"></i></a>
					<ul class="sub-menu">
						<li aria-haspopup="true"><a href="<?php echo base_url(); ?>kpi/create" class="slide-item">Add Indicator</a></li>
						<li aria-haspopup="true"><a href="<?php echo base_url(); ?>kpi/data" class="slide-item">Inidicator Data</a></li>
					</ul>
				</li>
				<?php } ?>


				
				<li aria-haspopup="true"><a href="#" class="sub-icon"><i class=""></i>Dropdown Lists<i class="fe fe-chevron-down horizontal-icon"></i></a>
					<ul class="sub-menu">
						<?php if (can_access_multi(['view_file_types'])) { ?><li aria-haspopup="true"><a href="<?php echo base_url() ?>filetypes">Resource and Asset Types</a></li><?php } ?>
						<?php if (can_access_multi(['view_sources'])) { ?><li aria-haspopup="true"><a href=" <?php echo base_url() ?>authors">Data Sources/Authors</a></li><?php } ?>
						<?php if (can_access_multi(['view_themes'])) { ?><li aria-haspopup="true"><a href="<?php echo base_url() ?>healththemes">Security Themes</a></li><?php } ?>
						<?php if (can_access_multi(['view_sub_themes'])) { ?><li aria-haspopup="true"><a href="<?php echo base_url() ?>subthemes">Security Sub-Themes</a></li><?php } ?>
						<?php if (can_access_multi(['view_geo_coverage'])) { ?><li aria-haspopup="true"><a href=" <?php echo base_url() ?>geoareas">Geographical Coverage</a></li><?php } ?>
						<!-- File Types -->
						<?php if (can_access_multi(['view_file_types'])) { ?><li aria-haspopup="true"><a href="<?php echo base_url() ?>filetypes">File Types</a></li><?php } ?>
						<?php if (can_access_multi(['view_tags'])) { ?><li aria-haspopup="true"><a href="<?php echo base_url() ?>tags">Search Tags</a></li><?php } ?>
						<?php if (can_access_multi(['view_quotes'])) { ?><li aria-haspopup="true"><a href="<?php echo base_url() ?>quotes">Quotes</a></li><?php } ?>
						<?php if (can_access_multi(['view_quize'])) { ?><li aria-haspopup="true"><a href="<?php echo base_url() ?>quize">Quize</a></li><?php } ?>
						<!-- Privacy Policy -->
						<?php if (can_access_multi(['view_privacy_policy'])) { ?><li aria-haspopup="true"><a href="<?php echo base_url() ?>privacypolicy">Privacy Policy</a></li><?php } ?>
					</ul>
				</li>

				<li aria-haspopup="true"><a href="#" class="sub-icon"><i class=""></i>Settings <i class="fe fe-chevron-down horizontal-icon"></i></a>
					<ul class="sub-menu">

						<li class=""><a href="<?php echo base_url() ?>auth/users" class="">Manage Users</a></li>
						<li class=""><a href="<?php echo base_url() ?>permissions" class="">Groups and Permissions</a></li>
						<li class=""><a href="<?php echo base_url() ?>auth/logs" class="">User Logs</a></li>
						<li class=""><a href="<?php echo base_url() ?>constants" class="">Constants</a></li>
					</ul>
				</li>
			</ul>
		</nav>
		<!--Nav-->
	</div>
</div>
<!--Horizontal-main -->