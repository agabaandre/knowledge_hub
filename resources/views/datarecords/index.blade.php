@extends('layouts.plain')

@section('styles')

@endsection

@section('content')

<div class="gray pt-2">
<div class="container">
<form class="row container justify-content-center align-items-center" action="">
	    <div class="form-group col-lg-4">
			<label>RCC</label>
			@include('partials.regions.dropdown',['class'=>'select2','allfield'=>'ALL','selected'=>@$_GET['rcc']])
		</div>
		<div class="form-group col-lg-4">
			<label>Member State</label>
			@include('partials.countries.dropdown',['class'=>'select2','allfield'=>'All'])
		</div>
		<input type="hidden" name="slug" value="<?php echo (isset($_GET['slug']))?$_GET['slug']:''; ?>" />
		<div class="form-group col-lg-4">
			<button type="submit" class="btn btn btn-md btn-sm btn-success mt-4">Apply Filter</button>
		</div>
</form>
	<!-- Item Wrap Start -->
	<div class="col-lg-12 col-md-12 col-sm-12 ">

		<div class="row">
			<div class="col-xl-12 col-lg-12 col-md-12 col-12">
				<div class="row align-items-center justify-content-between mx-0 bg-white rounded py-4 mb-4">
					<div class="col-sm-12">
						<h6 class="mb-0 ft-medium fs-sm text-center" {{ (count($records)==0)?'py-5':'' }}>
							{{ count($records) }} Data Records Available
						</h6>
					</div>

					@auth
						@if(count($records)>0)
						<div class="col-xl-3 col-lg-4 col-md-5 col-sm-12 float-end">
							<a href="{{ current_url() }}export=1" class="btn btn-sm btn-success rounded"><i class="fa fa-file-excel"></i>&nbsp; Export to Excel</a>
						</div>
						@endif
					@endauth
				</div>
			</div>
		</div>
		<!-- All jobs -->
		<div class="row " style="min-height: 300px;">
				<!-- Single job -->
				@foreach($records as $record)
				<div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">

				<div class="job_grid d-block border rounded px-3 pt-3 pb-2">
					<div class="jb-list01">
						<div class="jb-list-01-title">
								<a href="{{url('categories/data/detail')}}?id={{$record->id}}">
									<h5 class="ft-medium mb-1">{{ $record->title }}</h5>
									<p>{{ @$record->category->category_name }}</p>
									<hr>

									<p>{!! truncate($record->description,182) !!}</p>
								</a>
								<a class="text-success" href="{{ asset('categories/data/detail') }}?id={{$record->id}}">View Details</a>
								</p>
						</div>
						<div class="jb-list-01-info d-block mb-3">
							<span class="text-muted mr-2"><i class="lni lni-alarm-clock mr-1"></i>
							Published: {{ time_ago($record->created_at) }}
							</span>
						</div>
					</div>
				</div>
			</div>
			@endforeach
		</div>

		<div class="row">
			<div class="col-lg-12 col-md-12 col-sm-12">
				{{ $records->links() }}
			</div>
		</div>

	</div>

</div>
</div>
</div>

@endsection

@section('scripts')
@include('common.select2')

<script>

   var countries = @json($countries);

   console.log(countries);

    var selectedCountry = '<?php echo @$_GET['country_id']; ?>';
	var selectedRcc = '<?php echo @$_GET['rcc']; ?>';

	if(parseInt(selectedCountry)>0){

		const selected_country = countries.find(item=>(item.region_id === parseInt(selectedCountry) || item.region_id === parseInt(selectedRcc)));
		let rc_countries = countries.filter(item=>item.region_id === parseInt(selected_country.region_id));

		rc_countries.forEach(country => {
			$('#country_id').append(`<option ${(country.id  === selected_country.id)?'selected':''} value="${country.id}">${country.name}</option>`);
		});

		$('#country_id').val(selected_country.id).trigger('change');

	}


	$('#rcc').on('change', function(e){

		const rcc_countries = countries.filter(item=>item.region_id === parseInt(e.target.value));

		console.log('RCC Countries',rcc_countries);

		$('#country_id').empty();

		rcc_countries.forEach(country => {

			$('#country_id').append(`<option value="${country.id}">${country.name}</option>`);
		});


	});

</script>


@endsection
