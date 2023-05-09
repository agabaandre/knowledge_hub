<div class="spotlight px-3 py-5" style="background-color:#03343b ;background-image:url(<?php echo asset('frontend/img/landing-bg.png') ?>); background-repeat:no-repeat;">

<form action="{{ url('records/search') }}" class="bg-white rounded search-form" style="min-width: 70%;">
							<div class="row no-gutters">
									<div class="col-xl-8 col-lg-8 col-md-8 col-sm-12 col-12">
										<div class="form-group mb-0 position-relative">
											<input type="text" class="form-control left-ico autocomplete term" name="term" value="{{ @old('term') }}" placeholder="Type Keywords to explore" />
										</div>
									</div>
								
									<div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 col-12">
										<div class="form-group mb-0 position-relative">
											<button class="btn full-width  theme-bg text-white fs-md py-3" type="submit">Explore</button>
										</div>
									</div>
								</div>
							</form>

    <div  class="spot-row">
        <div class="col-lg-8 col-md-12 col-sm-12 px-3">
        <h3 class="text-center">Test your knowledge! Click on one of the themes below</h3>
        </div>
    </div>


<div  class="row spot-row col-sm-12">
    
    <div class="col-lg-4 col-md-12 col-sm-12 px-3">
        @include('home.partials.quotes')
        @include('home.partials.tags')
    </div>

    <div class="slide_items col-lg-8 col-md-12 col-sm-12">
    @foreach($subthemes as $subtheme)

    <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 item">
        <div class="cats-wrap text-center spot-item mt-1">
            <a href="{{ url('records/subtheme')}}?subtheme={{$subtheme->id}}" class="cats-box d-block rounded bg-white px-2 py-4">
                <div class="text-center mb-2 mx-auto position-relative d-inline-flex align-items-center justify-content-center p-3 py-3 " >
                    <img src="{{ asset('frontend/img/icons/'.$subtheme->icon)}}" style="max-width: 60%;"/>
                </div>
                <div class="cats-box-caption">
                    <h4 class="fs-sm mb-0 ft-sm m-catrio" data-bs-toggle="tool-tip" data-bs-title="{{$subtheme->description}}">{{truncate($subtheme->description,16)}}</h4>
                </div>
            </a>
        </div>
    </div>
    @endforeach
    </div>
</div>

<div  class="spot-row">
</div>

</div>