	<!-- ======================= Searchbar Banner ======================== -->

	@php
      $show_types = (strpos(current_url(),'record') >-1 || strpos(current_url(),'publication') >-1 )?true:false;
   @endphp



	<div class="py-3 theme-bg">
				<div class="container">
					<div class="row justify-content-between align-items-center">

						<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">	
							@if($show_types)						
							<form action="{{ (strpos(current_url(),'record') >-1)?url('records'):''}}" class="search-form" >
								<div class="row no-gutters">
									<div class="col-xl-{{ ($show_types)?'7':'10' }} col-lg-{{ ($show_types)?'7':'10' }} col-md-{{ ($show_types)?'7':'10' }} col-sm-12 col-12">
										<div class="form-group mb-0 position-relative">
											<input type="text"  value="{{ @$search->term }}" class="form-control left-ico autocomplete term custom-height-lg" name="term" placeholder="What are you looking for?"  style="font-size:18px;"/>
										</div>
									</div>
									<div class="col-xl-3 col-lg-3 col-md-3 col-sm-12 col-12">
										<div class="form-group">
											<select class="select2 custom-select" name="type">
											  <option value="" selected>All</option>
											  @foreach ($file_types as $type)
											  <option value="{{$type->id}}" 
											  	{{ (@$search->type==$type->id)?"selected":""}}
											  	>{{ $type->name}}</option>
											   @endforeach>
											</select>
										</div>
									</div>
									<div class="col-xl-2 col-lg-2 col-md-2 col-sm-12 col-12">
										<div class="form-group mb-0 position-relative">
											<button class="btn full-width py-3 text-success fs-md" type="submit">Search</button>
										</div>
									</div>
								</div>
                                </form>
						@else
						
					      <div class="single_widgets widget_search px-0 py-0">
                            <form action="{{ (strpos(current_url(),'record') >-1)?url('records'):((strpos(current_url(),'thread') >-1)?url('forums'):'')}}" class="sidebar-search-form px-0 py-0">
                                <input class="px-3 py-0" style="font-size: 12pt;" value="{{ @$search->term }}" type="search" name="term" placeholder="What are you looking for?">
								
                                <button type="submit"><i class="ti-search"></i></button>
                            </form>
                          </div>
						  @endif


						</div>
						
						
					</div>
				</div>
			</div>

			


