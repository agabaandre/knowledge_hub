<div class="px-3 py-3 custom-bg" style="background: linear-gradient(var(--theme-color-primary), rgba(0, 0, 0, 0.4)) !important; margin-top: 96px;">
	<div class="container">
		<div class="row justify-content-center">
			<div class="col-xl-10 col-lg-10 col-md-12">

				<form action="{{ url('records/search') }}" class="filters w-100">
					<div class="row g-0 bg-white rounded search-form shadow-sm" id="simple_search">
						<div class="col-md-8 col-sm-12">
							<div class="form-group mb-0 position-relative main_search">
								<input type="text" class="form-control left-ico autocomplete term main-search"
									name="term" value="{{ @old('term') }}" placeholder="Type Keywords" />
							</div>
						</div>
						<div class="col-md-4 col-sm-12">
							<div class="form-group mb-0 position-relative">
								<button class="btn w-100 theme-bg text-white fs-md py-3" type="submit">
									<i class="fa fa-magnifying-glass"></i> Search
								</button>
							</div>
						</div>
					</div>

					@include('partials.search.advanced_search')
				</form>

			</div>
		</div>

		<!-- Optional spacing before results or tabs -->
		<div class="row mt-4">
			<div class="col-12">
				@include('home.partials.theme_tabs')
			</div>
		</div>

		<div class="spot-row"></div>
	</div>
</div>
