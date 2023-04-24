	<!-- ======================= Searchbar Banner ======================== -->
	<div class="py-3 theme-bg">
				<div class="container">
					<div class="row justify-content-between align-items-center">

						<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">							
							<form action="{{ url('records/search') }}" class="search-form" >
								<div class="row no-gutters">
									<div class="col-xl-7 col-lg-7 col-md-7 col-sm-12 col-12">
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
						</div>
						
						
					</div>
				</div>
			</div>

			


