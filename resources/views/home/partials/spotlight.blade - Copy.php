<div class="spotlight px-3 py-3 custom-bg">
<div class="col-md-12 pt-4" style="display: flex; justify-content: right;">
           @include('layouts.partials.language')
</div>
<form action="{{ url('records/search') }}" class="filters" style="min-width: 70%;">
							<div class="row no-gutters bg-white rounded search-form">
									<div class="col-xl-8 col-lg-8 col-md-8 col-sm-12 col-12">
										<div class="form-group mb-0 position-relative main_search">
											<input type="text" class="form-control left-ico autocomplete term " name="term" value="{{ @old('term') }}" placeholder="Type Keywords to explore" />
										</div>
									</div>
								
									<div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 col-12 bg-show">
										<div class="form-group mb-0 position-relative">
											<button class="btn full-width  theme-bg text-white fs-md py-3" type="submit">Explore</button>
										</div>
									</div>

								</div>
                @include('partials.search.advanced_search')

                
            <div class="col-md-4 sm-show mt-1">
            <button class="btn full-width  theme-bg text-white fs-md py-3" type="submit">Explore</button>
            </div>
	</form>

 
<div  class="row spot-row col-sm-12">
    
    <div class="col-lg-4 col-md-12 col-sm-12 px-3">
        @include('home.partials.quotes')
        @include('home.partials.tags')
    </div>

    <div class="col-lg-8 col-md-12 col-sm-12 pb-5" style="z-index: 100;">
      <div class="row justify-content-center mb-2">
      <h3 class="text-center">Test your knowledge! Click on one of the themes below</h3>
      </div>
      @include('home.partials.theme_tabs')
    </div>

</div>

<div  class="spot-row">
</div>

</div>
