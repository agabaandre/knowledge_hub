@extends('admin.layouts.tabular')

@section('styles')
 @include('common.table')
@endsection

@section('content')
<div class="row">
	<div class="card col-lg-12">
		<div class="card-header text-left">
			<h3 class="card-title float-left mt-2 mb-2">{{ $title ?? 'Authors' }}</h3>
			 <hr>
		</div>

		<div class="card-body text-left">
		<?php echo $uitable; ?>
		</div>

	</div>


    @endsection