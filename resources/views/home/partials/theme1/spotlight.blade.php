<!-- Intro Banner
================================================== -->
<!-- add class "disable-gradient" to enable consistent background overlay -->
<div class="intro-banner" data-background-image="images/home-background.jpg">
	<div class="container">
		
		<!-- Intro Headline -->
		<div class="row">
			<div class="col-md-12">
				<div class="banner-headline">
					<h3>
						<strong>Hire experts or be hired for any job, any time.</strong>
						<br>
						<span>Thousands of small businesses use <strong class="color">Hireo</strong> to turn their ideas into reality.</span>
					</h3>
				</div>
			</div>
		</div>
		
		<!-- Search Bar -->
		<div class="row">
			<div class="col-md-12">
				<div class="intro-banner-search-form margin-top-95">

					<!-- Search Field -->
					<form action="{{ url('records/search') }}" class="intro-search-field">
						<label for="autocomplete-input" class="field-title ripple-effect text-sm">Advanced Filters</label>
						<div class="input-with-icon">
							<input id="autocomplete-input" type="text" placeholder="Online Job">
							<i class="icon-material-outline-search"></i>
						</div>
					</form>

					<!-- Button -->
					<div class="intro-search-button">
						<button class="button ripple-effect" type="submit">Search</button>
					</div>
				</div>
			</div>
           
		</div>
<!-- Stats -->
<div class="row mt-3">
    @include('home.partials.theme_tabs')
</div>

</div>
</div>

		
